<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. vw_fin_ejecucion_mes_subtitulo
        // 1. vw_fin_ejecucion_mes_subtitulo
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_ejecucion_mes_subtitulo AS
            SELECT 
                anio, 
                mes, 
                subtitulo, 
                SUM(devengado) as total_devengado,
                SUM(presupuesto_vigente) as total_presupuesto
            FROM fin_ejecucion_fact
            WHERE item = '00' -- Only Level 1 aggregates
            GROUP BY anio, mes, subtitulo
        ");

        // 2. vw_fin_ejecucion_acum_subtitulo
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_ejecucion_acum_subtitulo AS
            SELECT 
                anio, 
                mes, 
                subtitulo, 
                total_devengado,
                SUM(total_devengado) OVER (PARTITION BY anio, subtitulo ORDER BY mes) as acumulado_devengado
            FROM (
                SELECT anio, mes, subtitulo, SUM(devengado) as total_devengado
                FROM fin_ejecucion_fact
                WHERE item = '00' -- Only Level 1 aggregates
                GROUP BY anio, mes, subtitulo
            ) s
        ");

        // 3. vw_fin_top_items_mes
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_top_items_mes AS
            SELECT 
                anio, 
                mes, 
                item, 
                SUM(devengado) as total_devengado,
                RANK() OVER (PARTITION BY anio, mes ORDER BY SUM(devengado) DESC) as ranking
            FROM fin_ejecucion_fact
            WHERE item != '00' -- Exclude Level 1 aggregates
            GROUP BY anio, mes, item
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_fin_top_items_mes");
        DB::statement("DROP VIEW IF EXISTS vw_fin_ejecucion_acum_subtitulo");
        DB::statement("DROP VIEW IF EXISTS vw_fin_ejecucion_mes_subtitulo");
    }
};
