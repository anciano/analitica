@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('imports.index') }}"
            style="color: var(--text-muted); font-size: 0.875rem; text-decoration: none;">&larr; Volver al listado</a>
        <h1 class="mb-2 mt-2">Detalle de Importación #{{ $import->id }}</h1>
        <p style="color: var(--text-muted)">{{ $import->version->dataset->name }} - Periodo
            {{ $import->target_anio }}/{{ str_pad($import->target_mes, 2, '0', STR_PAD_LEFT) }}
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem;">
        <div>
            <div class="card mb-4"
                style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                <div>
                    <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Total Filas</p>
                    <h2 style="margin: 0.5rem 0;">{{ $import->total_rows ?? 0 }}</h2>
                </div>
                <div>
                    <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Válidas</p>
                    <h2 style="margin: 0.5rem 0; color: #4ade80;">{{ $import->valid_rows ?? 0 }}</h2>
                </div>
                <div>
                    <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Con Error</p>
                    <h2 style="margin: 0.5rem 0; color: #fca5a5;">{{ $import->error_rows ?? 0 }}</h2>
                </div>
            </div>

            @if($import->errors->count() > 0)
                <div class="card">
                    <div class="flex justify-between items-center mb-4">
                        <h3 style="margin: 0;">Errores Detectados</h3>
                        {{-- No implementation of CSV export logic yet, just the button for UI --}}
                        <button class="logout-btn" style="padding: 0.5rem 1rem; font-size: 0.75rem;">Exportar Errores
                            (CSV)</button>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border); color: var(--text-muted); font-size: 0.75rem;">
                                <th style="padding: 0.75rem;">Fila</th>
                                <th style="padding: 0.75rem;">Columna</th>
                                <th style="padding: 0.75rem;">Mensaje</th>
                                <th style="padding: 0.75rem;">Valor Original</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($import->errors as $error)
                                <tr style="border-bottom: 1px solid var(--border); font-size: 0.8125rem;">
                                    <td style="padding: 0.75rem;">{{ $error->row_number }}</td>
                                    <td style="padding: 0.75rem;"><strong>{{ $error->column_name }}</strong></td>
                                    <td style="padding: 0.75rem; color: #fca5a5;">{{ $error->error_message }}</td>
                                    <td style="padding: 0.75rem; font-family: monospace; opacity: 0.7;">{{ $error->original_value }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card" style="text-align: center; padding: 4rem;">
                    <p style="color: #4ade80;">No se detectaron errores en esta importación.</p>
                </div>
            @endif
        </div>

        <div>
            <div class="card">
                <h4 style="margin-top: 0;">Resumen Técnico</h4>
                <div style="font-size: 0.8125rem;">
                    <p class="flex justify-between">
                        <span style="color: var(--text-muted)">Estado:</span>
                        <span>{{ strtoupper($import->status) }}</span>
                    </p>
                    <p class="flex justify-between">
                        <span style="color: var(--text-muted)">Iniciado:</span>
                        <span>{{ $import->created_at->format('H:i d/m/Y') }}</span>
                    </p>
                    @if($import->processed_at)
                        <p class="flex justify-between">
                            <span style="color: var(--text-muted)">Finalizado:</span>
                            <span>{{ $import->processed_at->format('H:i d/m/Y') }}</span>
                        </p>
                    @endif
                    <p class="flex justify-between">
                        <span style="color: var(--text-muted)">Usuario:</span>
                        <span>{{ $import->user->name }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection