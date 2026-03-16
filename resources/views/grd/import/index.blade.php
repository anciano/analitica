@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-[--text-main]">Historial de Carga GRD</h1>
        <p class="text-[--text-muted] text-sm mt-1">Gestión de egresos hospitalarios (2018-2025)</p>
    </div>
    <a href="{{ route('grd.import.create') }}" class="btn">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
        Nueva Carga
    </a>
</div>

<div class="card overflow-hidden">
    <table class="sing-table">
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Archivo</th>
                <th>Estado</th>
                <th>Filas</th>
                <th>Válidas</th>
                <th>Errores</th>
                <th>Fecha Carga</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($imports as $import)
                <tr>
                    <td>
                        <div class="font-medium">{{ $import->target_anio }}</div>
                        <div class="text-[11px] text-[--text-muted] uppercase">{{ \Carbon\Carbon::create()->month($import->target_mes)->translatedFormat('F') }}</div>
                    </td>
                    <td>
                        <div class="text-[13px] text-[--text-main] truncate max-w-[200px]" title="{{ $import->file_name }}">
                            {{ basename($import->file_name) }}
                        </div>
                    </td>
                    <td>
                        @php
                            $statusMap = [
                                'pending' => ['bg-gray-100', 'text-gray-600', 'Pendiente'],
                                'processing' => ['bg-blue-100', 'text-blue-600', 'Procesando'],
                                'validating' => ['bg-purple-100', 'text-purple-600', 'Validando'],
                                'upserting' => ['bg-orange-100', 'text-orange-600', 'Cargando'],
                                'completed' => ['bg-green-100', 'text-green-600', 'Completado'],
                                'failed' => ['bg-red-100', 'text-red-600', 'Fallido'],
                            ];
                            $st = $statusMap[$import->status] ?? $statusMap['pending'];
                        @endphp
                        <span class="badge {{ $st[0] }} {{ $st[1] }}">
                            {{ $st[2] }}
                        </span>
                    </td>
                    <td class="text-[13px] font-medium">{{ number_format($import->total_rows) }}</td>
                    <td class="text-[13px] text-green-600 font-medium">{{ number_format($import->valid_rows) }}</td>
                    <td class="text-[13px] text-red-600 font-medium">{{ number_format($import->error_rows) }}</td>
                    <td class="text-[12px] text-[--text-muted]">
                        {{ $import->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('grd.import.show', $import) }}" class="p-2 hover:bg-gray-100 rounded-lg text-gray-500" title="Ver Detalles">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('grd.import.destroy', $import) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta carga y todos sus registros asociados?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-lg text-red-400" title="Eliminar">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-[--text-muted]">
                        No hay cargas registradas para GRD.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-[--border-soft]">
        {{ $imports->links() }}
    </div>
</div>
@endsection
