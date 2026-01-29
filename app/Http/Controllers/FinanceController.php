<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function resumen(Request $request)
    {
        $anio = $request->get('anio', 2025);
        $mes = $request->get('mes');

        // If no month selected, get latest available
        if (!$mes) {
            $latest = DB::table('vw_fin_resumen_mensual')
                ->where('anio', $anio)
                ->orderBy('mes', 'desc')
                ->first();
            $mes = $latest ? $latest->mes : 1;
        }

        // 1. KPI Cards
        $monthly = DB::table('vw_fin_resumen_mensual')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->first();

        $ytd = DB::table('vw_fin_resumen_mensual')
            ->where('anio', $anio)
            ->where('mes', '<=', $mes)
            ->sum('total_devengado');

        $trend = DB::table('vw_fin_tendencia_mensual')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->first();

        // 2. Tables
        $subtitulos = DB::table('vw_fin_ejecucion_mes_subtitulo')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->orderBy('total_devengado', 'desc')
            ->limit(5)
            ->get();

        $items = DB::table('vw_fin_ranking_items')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('ranking', '<=', 10)
            ->orderBy('ranking')
            ->get();

        // 3. Alerts (Ratio > 1.1)
        $alerts = DB::table('vw_fin_alertas')
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where(function ($query) {
                $query->where('ratio_aceleracion', '>', 1.1)
                    ->orWhere('variacion_pct', '>', 30);
            })
            ->orderBy('ratio_aceleracion', 'desc')
            ->limit(5)
            ->get();

        // 4. Last Import Status (Context Header)
        $lastImport = \App\Models\ImportRun::latest()->first();

        return view('finance.resumen', compact('monthly', 'ytd', 'trend', 'subtitulos', 'items', 'anio', 'mes', 'alerts', 'lastImport'));
    }

    public function tendencia()
    {
        $data = DB::table('vw_fin_tendencia_mensual')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->limit(12)
            ->get();

        return view('finance.tendencia', compact('data'));
    }

    public function powerbi()
    {
        $url = env('POWER_BI_URL', 'https://app.powerbi.com/view?r=eyJrIjoi...'); // Placeholder
        return view('finance.powerbi', compact('url'));
    }
}
