<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW vw_fin_plan_vs_ejecucion AS
            WITH planificados AS (
                SELECT 
                    p.anio,
                    m.mes,
                    c.codigo AS item_codigo,
                    c.denominacion AS item_nombre,
                    cc.codigo AS cc_codigo,
                    cc.nombre AS cc_nombre,
                    SUM(m.monto_planificado) AS programado
                FROM fin_plan_mensual m
                JOIN fin_plan_items i ON m.plan_item_id = i.id
                JOIN fin_planes p ON i.plan_id = p.id
                JOIN fin_clasificador_items c ON i.clasificador_item_id = c.id
                JOIN fin_centros_costo cc ON i.centro_costo_id = cc.id
                WHERE p.estado = 'aprobado'
                GROUP BY p.anio, m.mes, c.codigo, c.denominacion, cc.codigo, cc.nombre
            ),
            ejecutados AS (
                SELECT 
                    f.anio,
                    f.mes,
                    f.codigo_completo AS item_codigo,
                    cc.codigo AS cc_codigo,
                    SUM(f.compromiso) AS compromiso,
                    SUM(f.devengado) AS devengado,
                    SUM(f.pagado) AS pagado,
                    SUM(f.deuda_flotante) AS deuda_flotante
                FROM fin_ejecucion_fact f
                LEFT JOIN fin_centros_costo cc ON f.centro_costo_id = cc.id
                GROUP BY f.anio, f.mes, f.codigo_completo, cc.codigo
            ),
            periodos AS (
                SELECT DISTINCT anio, mes, item_codigo, cc_codigo FROM planificados
                UNION
                SELECT DISTINCT anio, mes, item_codigo, cc_codigo FROM ejecutados
            )
            SELECT 
                per.anio,
                per.mes,
                (per.anio * 100 + per.mes) AS periodo_yyyymm,
                per.item_codigo,
                COALESCE(p.item_nombre, (SELECT denominacion FROM fin_clasificador_items WHERE codigo = per.item_codigo LIMIT 1)) AS item_nombre,
                per.cc_codigo,
                COALESCE(p.cc_nombre, (SELECT nombre FROM fin_centros_costo WHERE codigo = per.cc_codigo LIMIT 1)) AS cc_nombre,
                COALESCE(p.programado, 0) AS monto_programado,
                COALESCE(e.compromiso, 0) AS monto_compromiso,
                COALESCE(e.devengado, 0) AS monto_devengado,
                COALESCE(e.pagado, 0) AS monto_pagado,
                COALESCE(e.deuda_flotante, 0) AS monto_deuda_flotante,
                -- Variaciones
                (COALESCE(p.programado, 0) - COALESCE(e.devengado, 0)) AS variacion_absoluta,
                CASE 
                    WHEN COALESCE(p.programado, 0) > 0 
                    THEN (COALESCE(e.devengado, 0) / p.programado) * 100 
                    ELSE NULL 
                END AS porcentaje_ejecucion
            FROM periodos per
            LEFT JOIN planificados p ON per.anio = p.anio AND per.mes = p.mes AND per.item_codigo = p.item_codigo AND (per.cc_codigo = p.cc_codigo OR (per.cc_codigo IS NULL AND p.cc_codigo IS NULL))
            LEFT JOIN ejecutados e ON per.anio = e.anio AND per.mes = e.mes AND per.item_codigo = e.item_codigo AND (per.cc_codigo = e.cc_codigo OR (per.cc_codigo IS NULL AND e.cc_codigo IS NULL))
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_fin_plan_vs_ejecucion");
    }
};
