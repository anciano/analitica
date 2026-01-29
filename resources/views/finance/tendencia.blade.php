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

        .dashboard-page-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
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
            margin-bottom: 24px;
        }

        .sing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
        }

        /* Badges */
        .delta-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .delta-positive {
            background: #fee2e2;
            color: #ef4444;
        }

        /* Red bg for increases in cost? Or context dependent? Usually cost increase is red */
        .delta-negative {
            background: #dcfce7;
            color: #16a34a;
        }

        /* Green bg for cost decrease */

        .value-positive {
            color: #ef4444;
        }

        .value-negative {
            color: #16a34a;
        }

        .suggestion-box {
            background: rgba(79, 141, 245, 0.08);
            /* Primary blue with opacity */
            border: 1px dashed var(--primary-blue);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 24px;
        }
    </style>

    @php
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
    @endphp

    <div class="mb-8">
        <h1 class="dashboard-page-title">Tendencia de Gasto</h1>
        <p style="color: var(--text-muted); font-size: 14px;">Análisis comparativo de los últimos 12 meses.</p>
    </div>

    <div class="sing-card p-0">
        <div class="overflow-x-auto">
            <table class="sing-table">
                <thead>
                    <tr>
                        <th class="p-4">Mes / Año</th>
                        <th class="text-right p-4">Devengado Mensual</th>
                        <th class="text-right p-4">Variación vs Anterior ($)</th>
                        <th class="text-right p-4">Variación vs Anterior (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td class="p-4" style="font-weight: 500">
                                {{ $meses[$row->mes] ?? '' }} {{ $row->anio }}
                            </td>
                            <td class="p-4 text-right">
                                $ {{ number_format($row->total_devengado, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-right">
                                <span
                                    class="{{ $row->variacion_monto > 0 ? 'value-positive' : ($row->variacion_monto < 0 ? 'value-negative' : '') }}">
                                    {{ $row->variacion_monto > 0 ? '+' : '' }}
                                    {{ number_format($row->variacion_monto, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                @if($row->variacion_pct != 0)
                                    <span class="delta-badge {{ $row->variacion_pct > 0 ? 'delta-positive' : 'delta-negative' }}">
                                        {{ $row->variacion_pct > 0 ? '↑' : '↓' }} {{ abs(round($row->variacion_pct ?? 0, 1)) }}%
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

    <div class="suggestion-box">
        💡 Sugerencia: En la fase de integración con Power BI, estos datos se visualizarán con gráficos interactivos de
        líneas y barras.
    </div>
@endsection