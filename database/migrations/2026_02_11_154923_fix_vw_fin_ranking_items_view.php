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
        DB::statement("DROP VIEW IF EXISTS vw_fin_ranking_items");
        DB::statement("
            CREATE VIEW vw_fin_ranking_items AS
            SELECT 
                anio, 
                mes, 
                subtitulo,
                item,
                codigo_completo,
                concepto,
                SUM(devengado) as total_devengado,
                RANK() OVER (PARTITION BY anio, mes ORDER BY SUM(devengado) DESC) as ranking
            FROM fin_ejecucion_fact
            WHERE item != '00'
            GROUP BY anio, mes, subtitulo, item, codigo_completo, concepto
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original if needed, but the original was in 2026_01_28_201420_improve_finance_views_for_dashboard
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
            WHERE item != '00'
            GROUP BY anio, mes, subtitulo, item, concepto
        ");
    }
};
