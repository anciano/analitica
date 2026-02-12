@extends('layouts.app')

@section('content')
    <style>
        .dashboard-page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-main);
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
            font-weight: 600;
            font-size: 13px;
            transition: opacity 0.2s;
        }

        .btn-action:hover {
            opacity: 0.7;
            text-decoration: underline;
        }
    </style>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="dashboard-page-title">Cargas de Datos</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Monitoreo de procesos y trazabilidad de importación.</p>
        </div>
        <a href="{{ route('imports.create') }}" class="btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            Nueva Carga
        </a>
    </div>

    <div class="card p-0 overflow-hidden">
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
                                    {{ $import->target_anio }} - Mes {{ str_pad($import->target_mes, 2, '0', STR_PAD_LEFT) }}
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
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('imports.show', $import) }}" class="btn-action">Ver</a>
                                    <a href="{{ route('imports.edit', $import) }}" class="btn-action"
                                        style="color: var(--warning)">Editar</a>
                                    <form action="{{ route('imports.destroy', $import) }}" method="POST"
                                        onsubmit="return confirm('¿Está seguro de eliminar esta carga? Se borrarán todos los registros asociados.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action"
                                            style="color: var(--danger); background: none; border: none; cursor: pointer; padding: 0;">Eliminar</button>
                                    </form>
                                </div>
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