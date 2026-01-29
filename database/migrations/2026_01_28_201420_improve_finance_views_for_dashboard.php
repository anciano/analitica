<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. vw_fin_resumen_mensual (Totals per month - Level 1 Aggregates ONLY)
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_resumen_mensual AS
            SELECT 
                anio, 
                mes, 
                SUM(devengado) as total_devengado,
                SUM(presupuesto_vigente) as total_presupuesto,
                (SUM(devengado) / NULLIF(SUM(presupuesto_vigente), 0)) * 100 as porcentaje_ejecucion
            FROM fin_ejecucion_fact
            WHERE item = '00' -- Only count Level 1 aggregates to avoid double counting
            GROUP BY anio, mes
        ");

        // 2. vw_fin_ranking_items (Detailed items Level 2, excluding aggregates)
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_ranking_items AS
            SELECT 
                anio, 
                mes, 
                subtitulo,
                item,
                concepto,
                SUM(devengado) as total_devengado,
                RANK() OVER (PARTITION BY anio, mes ORDER BY SUM(devengado) DESC) as ranking
            FROM fin_ejecucion_fact
            WHERE item != '00' -- Exclude Level 1 aggregates
            GROUP BY anio, mes, subtitulo, item, concepto
        ");

        // 3. vw_fin_tendencia_mensual (MoM variation based on true totals)
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_tendencia_mensual AS
            SELECT 
                anio, 
                mes, 
                total_devengado,
                LAG(total_devengado) OVER (ORDER BY anio, mes) as devengado_anterior,
                total_devengado - LAG(total_devengado) OVER (ORDER BY anio, mes) as variacion_monto,
                ((total_devengado - LAG(total_devengado) OVER (ORDER BY anio, mes)) / NULLIF(LAG(total_devengado) OVER (ORDER BY anio, mes), 0)) * 100 as variacion_pct
            FROM (
                SELECT anio, mes, SUM(devengado) as total_devengado
                FROM fin_ejecucion_fact
                WHERE item = '00' -- Only count Level 1 aggregates
                GROUP BY anio, mes
            ) s
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_fin_tendencia_mensual");
        DB::statement("DROP VIEW IF EXISTS vw_fin_ranking_items");
        DB::statement("DROP VIEW IF EXISTS vw_fin_resumen_mensual");
    }
};
