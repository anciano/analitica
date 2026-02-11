<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. vw_fin_gasto_base - Base transaction view (CRITICAL)
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_gasto_base AS
            SELECT
                id,
                DATE(anio || '-' || LPAD(mes::text, 2, '0') || '-01') AS fecha,
                (anio * 100 + mes) AS periodo_yyyymm,
                anio,
                mes,
                -- Use subtitulo and item as-is (they are already codes)
                subtitulo AS subtitulo_codigo,
                item AS item_codigo,
                -- Extract full code from concepto (e.g., '2101 Personal de Planta' -> '2101')
                TRIM(SPLIT_PART(concepto, ' ', 1)) AS codigo_completo,
                -- Extract name from concepto (everything after first space)
                TRIM(SUBSTRING(concepto FROM ' (.*)$')) AS nombre,
                nivel,
                -- Original fields
                asignacion,
                concepto,
                devengado AS monto,
                presupuesto_vigente,
                compromiso,
                devengado,
                pagado,
                saldo,
                fuente,
                import_run_id,
                created_at
            FROM fin_ejecucion_fact
        ");

        // 2. vw_dim_fecha - Date dimension
        DB::statement("
            CREATE OR REPLACE VIEW vw_dim_fecha AS
            WITH date_series AS (
                SELECT generate_series(
                    '2024-01-01'::date,
                    '2027-12-31'::date,
                    '1 day'::interval
                )::date AS fecha
            )
            SELECT
                fecha,
                EXTRACT(YEAR FROM fecha)::integer AS anio,
                EXTRACT(MONTH FROM fecha)::integer AS mes,
                CASE EXTRACT(MONTH FROM fecha)
                    WHEN 1 THEN 'Enero'
                    WHEN 2 THEN 'Febrero'
                    WHEN 3 THEN 'Marzo'
                    WHEN 4 THEN 'Abril'
                    WHEN 5 THEN 'Mayo'
                    WHEN 6 THEN 'Junio'
                    WHEN 7 THEN 'Julio'
                    WHEN 8 THEN 'Agosto'
                    WHEN 9 THEN 'Septiembre'
                    WHEN 10 THEN 'Octubre'
                    WHEN 11 THEN 'Noviembre'
                    WHEN 12 THEN 'Diciembre'
                END AS nombre_mes,
                EXTRACT(QUARTER FROM fecha)::integer AS trimestre,
                CASE WHEN EXTRACT(MONTH FROM fecha) <= 6 THEN 1 ELSE 2 END AS semestre,
                EXTRACT(WEEK FROM fecha)::integer AS semana_anio,
                EXTRACT(DAY FROM fecha)::integer AS dia_mes,
                EXTRACT(DOW FROM fecha)::integer AS dia_semana,
                CASE EXTRACT(DOW FROM fecha)
                    WHEN 0 THEN 'Domingo'
                    WHEN 1 THEN 'Lunes'
                    WHEN 2 THEN 'Martes'
                    WHEN 3 THEN 'Miércoles'
                    WHEN 4 THEN 'Jueves'
                    WHEN 5 THEN 'Viernes'
                    WHEN 6 THEN 'Sábado'
                END AS nombre_dia,
                (EXTRACT(YEAR FROM fecha)::integer * 100 + EXTRACT(MONTH FROM fecha)::integer) AS periodo_yyyymm,
                (DATE_TRUNC('month', fecha) = DATE_TRUNC('month', CURRENT_DATE)) AS es_mes_actual,
                (fecha < DATE_TRUNC('month', CURRENT_DATE)) AS es_mes_cerrado
            FROM date_series
        ");

        // 3. vw_dim_fin_clasificador - Financial classifier dimension
        DB::statement("
            CREATE OR REPLACE VIEW vw_dim_fin_clasificador AS
            WITH clasificador_base AS (
                SELECT DISTINCT
                    subtitulo,
                    item,
                    concepto,
                    TRIM(SPLIT_PART(concepto, ' ', 1)) AS codigo_completo,
                    nivel
                FROM fin_ejecucion_fact
                WHERE concepto IS NOT NULL
            )
            SELECT
                -- Unique key: subtitulo + item
                subtitulo || '-' || item AS clasificador_key,
                codigo_completo,
                -- Extract name from concepto (everything after first space)
                TRIM(SUBSTRING(concepto FROM ' (.*)$')) AS nombre,
                -- Original fields
                subtitulo AS subtitulo_codigo,
                item AS item_codigo,
                concepto,
                nivel,
                -- Hierarchical path (simplified for now, can be enhanced later)
                CASE 
                    WHEN LENGTH(codigo_completo) = 2 THEN TRIM(SUBSTRING(concepto FROM ' (.*)$'))
                    ELSE 
                        (SELECT TRIM(SUBSTRING(c2.concepto FROM ' (.*)$'))
                         FROM clasificador_base c2 
                         WHERE c2.subtitulo = clasificador_base.subtitulo 
                         AND LENGTH(TRIM(SPLIT_PART(c2.concepto, ' ', 1))) = 2
                         LIMIT 1) || ' > ' || TRIM(SUBSTRING(concepto FROM ' (.*)$'))
                END AS path
            FROM clasificador_base
        ");

        // 4. vw_fin_costo_por_dotacion - Cost per headcount analysis
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_costo_por_dotacion AS
            WITH gastos_mes AS (
                SELECT
                    (anio * 100 + mes) AS periodo_yyyymm,
                    anio,
                    mes,
                    subtitulo AS subtitulo_codigo,
                    TRIM(SPLIT_PART(concepto, ' ', 1)) AS codigo_completo,
                    TRIM(SUBSTRING(concepto FROM ' (.*)$')) AS nombre,
                    SUM(devengado) AS gasto_total
                FROM fin_ejecucion_fact
                -- Only level 2 (subtitulos) to avoid double-counting
                WHERE nivel = 2
                GROUP BY anio, mes, subtitulo, concepto
            ),
            dotacion_mes AS (
                SELECT
                    (anio * 100 + mes) AS periodo_yyyymm,
                    anio,
                    mes,
                    -- FTE calculation: total hours / 176 (standard monthly hours)
                    SUM(horas) / 176.0 AS dotacion_fte,
                    COUNT(DISTINCT rut_hash) AS dotacion_personas,
                    SUM(horas) AS total_horas,
                    SUM(total_haberes) AS total_haberes
                FROM hr_dotacion_fact
                GROUP BY anio, mes
            )
            SELECT
                g.periodo_yyyymm,
                g.anio,
                g.mes,
                g.subtitulo_codigo,
                g.codigo_completo,
                g.nombre,
                g.gasto_total,
                d.dotacion_fte,
                d.dotacion_personas,
                d.total_horas,
                d.total_haberes,
                CASE 
                    WHEN d.dotacion_fte > 0 THEN g.gasto_total / d.dotacion_fte
                    ELSE NULL
                END AS costo_por_fte,
                CASE 
                    WHEN d.dotacion_personas > 0 THEN g.gasto_total / d.dotacion_personas
                    ELSE NULL
                END AS costo_por_persona,
                CASE 
                    WHEN d.dotacion_personas > 0 THEN d.total_haberes / d.dotacion_personas
                    ELSE NULL
                END AS haberes_promedio
            FROM gastos_mes g
            LEFT JOIN dotacion_mes d ON g.periodo_yyyymm = d.periodo_yyyymm
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_fin_costo_por_dotacion');
        DB::statement('DROP VIEW IF EXISTS vw_dim_fin_clasificador');
        DB::statement('DROP VIEW IF EXISTS vw_dim_fecha');
        DB::statement('DROP VIEW IF EXISTS vw_fin_gasto_base');
    }
};
