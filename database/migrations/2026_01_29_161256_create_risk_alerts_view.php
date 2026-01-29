<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // View for calculating 3-month average and acceleration ratio
        // We only care about Level 2 items (item != '00')
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_alertas AS
            WITH monthly_data AS (
                SELECT 
                    anio, 
                    mes, 
                    subtitulo, 
                    item, 
                    concepto,
                    SUM(devengado) as devengado_mes
                FROM fin_ejecucion_fact
                WHERE item != '00' -- Filter out Level 1 aggregates
                GROUP BY anio, mes, subtitulo, item, concepto
            ),
            moving_averages AS (
                SELECT 
                    *,
                    AVG(devengado_mes) OVER (
                        PARTITION BY subtitulo, item 
                        ORDER BY anio, mes 
                        ROWS BETWEEN 3 PRECEDING AND 1 PRECEDING
                    ) as promedio_3_meses,
                    LAG(devengado_mes) OVER (
                        PARTITION BY subtitulo, item 
                        ORDER BY anio, mes
                    ) as devengado_anterior
                FROM monthly_data
            )
            SELECT 
                anio,
                mes,
                subtitulo,
                item,
                concepto,
                devengado_mes,
                COALESCE(promedio_3_meses, 0) as promedio_3_meses,
                devengado_anterior,
                -- Acceleration Ratio: Current / Average (avoid div by zero)
                CASE 
                    WHEN COALESCE(promedio_3_meses, 0) > 0 THEN devengado_mes / promedio_3_meses
                    ELSE 0 
                END as ratio_aceleracion,
                -- MoM Variation %
                CASE 
                    WHEN COALESCE(devengado_anterior, 0) > 0 THEN ((devengado_mes - devengado_anterior) / devengado_anterior) * 100
                    ELSE 0 
                END as variacion_pct
            FROM moving_averages
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_fin_alertas");
    }
};
