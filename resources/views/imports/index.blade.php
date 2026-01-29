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

            /* Semantic Colors (Sing App Palette) */
            --primary-blue: #4F8DF5;
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

        .sing-table tr:hover td {
            background-color: #F9FAFB;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-completed {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-failed {
            background: #fee2e2;
            color: #ef4444;
        }

        .status-processing {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-pending {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-action {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
        }

        .btn-action:hover {
            text-decoration: underline;
        }
    </style>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="dashboard-page-title">Historial de Registros</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Monitoreo de procesos y trazabilidad de datos.</p>
        </div>
    </div>

    <div class="sing-card p-0">
        <div class="overflow-x-auto">
            <table class="sing-table">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">ID</th>
                        <th>Dataset / Periodo</th>
                        <th>Estado</th>
                        <th>Registros (Val/Err)</th>
                        <th>Fecha</th>
                        <th style="padding-right: 24px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imports as $import)
                        <tr>
                            <td style="padding-left: 24px; color: var(--text-muted);">
                                #{{ $import->id }}
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $import->version->dataset->name }}</div>
                                <div style="font-size: 13px; color: var(--text-muted);">
                                    {{ $import->anio }} - Mes {{ str_pad($import->mes, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($import->status) {
                                        'completed' => 'status-completed',
                                        'failed' => 'status-failed',
                                        'processing' => 'status-processing',
                                        default => 'status-pending'
                                    };

                                    $statusLabel = match ($import->status) {
                                        'completed' => 'Completado',
                                        'failed' => 'Fallido',
                                        'processing' => 'Procesando',
                                        'pending' => 'Pendiente',
                                        default => strtoupper($import->status)
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-main);">{{ $import->valid_rows ?? 0 }}</span>
                                <span style="color: var(--border-soft); margin: 0 4px;">/</span>
                                <span style="color: var(--danger); font-weight: 500;">{{ $import->error_rows ?? 0 }}</span>
                            </td>
                            <td style="color: var(--text-muted);">
                                {{ $import->created_at->diffForHumans() }}
                            </td>
                            <td style="padding-right: 24px;">
                                <a href="{{ route('imports.show', $import) }}" class="btn-action">Ver Detalle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 20px; border-top: 1px solid var(--border-soft);">
            {{ $imports->links() }}
        </div>
    </div>
@endsection