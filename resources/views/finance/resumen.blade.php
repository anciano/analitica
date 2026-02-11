@extends('layouts.app')

@section('content')
    <style>
        :root {
            /* Basic Colors */
            --bg-app: #F5F7FA;
            --bg-surface: #FFFFFF;
            --border-soft: #E6ECF2;
            --text-main: #1F2937;
            --text-muted: #6B7280;

            /* Sidebar Colors (Reference) */
            --sidebar-bg: #0E1628;
            --sidebar-icon: #9AA4BF;
            --sidebar-active: #3B82F6;

            /* Semantic Colors */
            --primary-blue: #4F8DF5;
            --primary-cyan: #4FD1C5;
            --primary-yellow: #F6C85F;
            --primary-red: #F87171;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;

            /* Fonts */
            --font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-app);
            color: var(--text-main);
            font-family: var(--font-family);
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Card Styles */
        .sing-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            padding: 20px;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .kpi-delta {
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .text-success {
            color: var(--success);
        }

        .text-danger {
            color: var(--danger);
        }

        /* Table Styles */
        .sing-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sing-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-soft);
        }

        .sing-table td {
            padding: 16px;
            font-size: 14px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-soft);
            vertical-align: middle;
        }

        .sing-table tr:last-child td {
            border-bottom: none;
        }

        .sing-table tr:hover td {
            background-color: #F9FAFB;
            /* Slightly grey on hover */
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-level-1 {
            background: #E0F2FE;
            color: #0369A1;
        }

        /* Light blue */
        .badge-level-2 {
            background: #F3E8FF;
            color: #7E22CE;
        }

        /* Light purple */

        /* Inputs */
        .sing-select {
            background: var(--bg-surface);
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            cursor: pointer;
        }

        .sing-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 2px rgba(79, 141, 245, 0.2);
        }
    </style>

    @php
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
    @endphp

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="dashboard-title">Resumen de Gastos</h1>
            <p style="color: var(--text-muted); font-size: 14px;">
                Vista consolidada del periodo: 
                <span style="font-weight: 600; color: var(--text-main)">
                    {{ $meses[$mes] }} {{ $anio }}
                </span>
            </p>
            @if(isset($lastImport))
                <div style="font-size: 12px; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <span style="color: var(--text-muted);">Última carga:</span>
                    <span style="font-weight: 500;">
                        {{ $lastImport->version->dataset->name ?? 'N/A' }} 
                        ({{ $lastImport->anio }}-{{ str_pad($lastImport->mes, 2, '0', STR_PAD_LEFT) }})
                    </span>
                    <span class="badge" style="font-size: 10px; padding: 2px 6px; {{ $lastImport->status === 'completed' ? 'background: #dcfce7; color: #16a34a;' : 'background: #fee2e2; color: #ef4444;' }}">
                        {{ strtoupper($lastImport->status) }}
                    </span>
                    <span style="color: var(--text-muted);">{{ $lastImport->created_at->diffForHumans() }}</span>
                </div>
            @endif
        </div>
        <div class="flex gap-3">
            <form action="{{ route('finance.resumen') }}" method="GET" class="flex gap-3">
                <select name="mes" class="sing-select" onchange="this.form.submit()">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                            {{ $meses[$m] }}
                        </option>
                    @endforeach
                </select>
                <select name="anio" class="sing-select" onchange="this.form.submit()">
                    <option value="2025" {{ $anio == 2025 ? 'selected' : '' }}>2025</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Alerts Table (Lo que requiere atención) -->
    @if(isset($alerts) && count($alerts) > 0)
        <div class="sing-card mb-8 p-0">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 style="font-size: 16px; font-weight: 600;">⚠️ Lo que requiere atención</h2>
                    <p style="font-size: 13px; color: var(--text-muted);">Ítems con aceleración de gasto inusual (>1.1x
                        promedio)</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="sing-table">
                    <thead>
                        <tr>
                            <th>Ítem</th>
                            <th class="text-right">Gasto Mes</th>
                            <th class="text-right">Promedio 3 Meses</th>
                            <th class="text-center">Ratio Aceleración</th>
                            <th class="text-right">Variación MoM</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                            <tr>
                                <td>
                                    <div style="font-weight: 500;">{{ $alert->concepto }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">
                                        {{ $alert->subtitulo }}.{{ $alert->item }}</div>
                                </td>
                                <td class="text-right">$ {{ number_format($alert->devengado_mes, 0, ',', '.') }}</td>
                                <td class="text-right text-muted">$ {{ number_format($alert->promedio_3_meses, 0, ',', '.') }}</td>
                                <td class="text-center" style="font-weight: 600;">
                                    {{ number_format($alert->ratio_aceleracion, 2) }}x
                                </td>
                                <td class="text-right {{ $alert->variacion_pct > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $alert->variacion_pct > 0 ? '+' : '' }}{{ round($alert->variacion_pct, 1) }}%
                                </td>
                                <td class="text-center">
                                    @if($alert->ratio_aceleracion > 1.25 || $alert->variacion_pct > 30)
                                        <span class="badge" style="background: #fee2e2; color: #ef4444;">CRÍTICO</span>
                                    @else
                                        <span class="badge" style="background: #ffedd5; color: #f97316;">ALERTA</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Monthly Devengado -->
        <div class="sing-card">
            <div class="card-label">DEVENGADO DEL MES</div>
            <div class="kpi-value">$ {{ number_format($monthly->total_devengado ?? 0, 0, ',', '.') }}</div>
            <div class="kpi-delta {{ ($trend->variacion_pct ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                <span>{{ ($trend->variacion_pct ?? 0) > 0 ? '↑' : '↓' }}</span>
                <span>{{ abs(round($trend->variacion_pct ?? 0, 1)) }}%</span>
                <span style="color: var(--text-muted); font-weight: 400; margin-left: 4px;">vs mes anterior</span>
            </div>
        </div>

        <!-- YTD -->
        <div class="sing-card">
            <div class="card-label">ACUMULADO ANUAL</div>
            <div class="kpi-value">$ {{ number_format($ytd ?? 0, 0, ',', '.') }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                Desde Enero hasta {{ $meses[$mes] }}
            </div>
        </div>

        <!-- Ejecución -->
        <div class="sing-card">
            <div class="card-label">EJECUCIÓN PRESUPUESTARIA</div>
            <div class="kpi-value">{{ round($monthly->porcentaje_ejecucion ?? 0, 1) }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                <div class="bg-blue-500 h-1.5 rounded-full"
                    style="width: {{ min($monthly->porcentaje_ejecucion ?? 0, 100) }}%"></div>
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                Sobre presupuesto vigente
            </div>
        </div>

        <!-- Proyección -->
        <div class="sing-card">
            <div class="card-label">PROYECCIÓN ANUAL</div>
            <div class="kpi-value">$ {{ number_format(($ytd / max($mes, 1)) * 12, 0, ',', '.') }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                Estimación lineal
            </div>
        </div>
    </div>

    <!-- Hierarchy & Ranking -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

        <!-- Nivel 1: Subtítulos -->
        <div class="col-span-1">
            <div class="sing-card p-0 h-full">
                <div class="p-5 border-b border-gray-100">
                    <h2 style="font-size: 16px; font-weight: 600;">Nivel 1: Subtítulos</h2>
                    <p style="font-size: 13px; color: var(--text-muted);">Gasto agrupado por subtítulo</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="sing-table">
                        <thead>
                            <tr>
                                <th>Subtítulo</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subtitulos as $sub)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="badge" style="background: #eff6ff; color: #1e40af;">S{{ $sub->subtitulo }}</span>
                                            <div style="font-size: 12px; color: var(--text-muted);">
                                                {{ round(($sub->total_devengado / ($monthly->total_devengado ?: 1)) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-semibold">
                                        $ {{ number_format($sub->total_devengado, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ranking Hierárquico (Niveles 3, 4, 5) -->
        <div class="col-span-2">
            <div class="sing-card p-0 h-full">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 style="font-size: 16px; font-weight: 600;">Ranking de Gastos Operativos</h2>
                        <p style="font-size: 13px; color: var(--text-muted);">Análisis detallado de ítems y asignaciones (Niveles 3+)</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="sing-table">
                        <thead>
                            <tr>
                                <th>Código / Concepto</th>
                                <th>Nivel</th>
                                <th class="text-right">Gasto</th>
                                <th class="text-right">% s/Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ranking as $item)
                                @php
                                    $nivel = 1;
                                    $codeClean = preg_replace('/[^0-9]/', '', $item->codigo_completo ?? '');
                                    $codeLen = strlen($codeClean);
                                    if ($codeLen <= 2) $nivel = 1;
                                    elseif ($codeLen <= 4) $nivel = 2;
                                    elseif ($codeLen <= 7) $nivel = 3;
                                    elseif ($codeLen <= 10) $nivel = 4;
                                    else $nivel = 5;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="font-mono text-[12px] text-[--primary-blue] font-bold">{{ $item->codigo_completo }}</div>
                                        <div style="font-weight: 500; font-size: 13px;">{{ Str::limit($item->concepto, 45) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #f3f4f6; color: #4b5563; font-size: 10px;">{{ $nivel }}</span>
                                    </td>
                                    <td class="text-right font-semibold">
                                        $ {{ number_format($item->total_devengado, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            {{ round(($item->total_devengado / ($monthly->total_devengado ?: 1)) * 100, 2) }}%
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Analysis (Merged from Tendencia) -->
    <div class="sing-card p-0 mb-8">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 style="font-size: 16px; font-weight: 600;">Histórico de Tendencia</h2>
                <p style="font-size: 13px; color: var(--text-muted);">Comparativa de los últimos 12 meses de ejecución</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="sing-table">
                <thead>
                    <tr>
                        <th>Mes / Año</th>
                        <th class="text-right">Gasto Devengado</th>
                        <th class="text-right">Variación ($)</th>
                        <th class="text-right">Variación (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $row)
                        <tr>
                            <td style="font-weight: 500">
                                {{ $meses[$row->mes] }} {{ $row->anio }}
                            </td>
                            <td class="text-right">
                                $ {{ number_format($row->total_devengado, 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                <span style="font-weight: 500; {{ $row->variacion_monto > 0 ? 'color: var(--danger);' : 'color: var(--success);' }}">
                                    {{ $row->variacion_monto > 0 ? '+' : '' }}{{ number_format($row->variacion_monto, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-right">
                                @if($row->variacion_pct != 0)
                                    <span class="badge" style="{{ $row->variacion_pct > 0 ? 'background: #fee2e2; color: #ef4444;' : 'background: #dcfce7; color: #16a34a;' }}">
                                        {{ $row->variacion_pct > 0 ? '↑' : '↓' }} {{ abs(round($row->variacion_pct, 1)) }}%
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection