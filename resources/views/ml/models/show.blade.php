@extends('layouts.app')

@section('content')
<div class="mb-8">
    <a href="{{ route('ml.models.index') }}" class="text-sm text-blue-500 hover:underline flex items-center gap-1 mb-2">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Volver a Modelos
    </a>
    <h1 class="text-2xl font-bold text-[--text-main]">{{ $model->name }}</h1>
    <p class="text-[--text-muted] text-sm mt-1">Historial de versiones y métricas de desempeño.</p>
</div>

<div class="bg-white rounded-xl border border-[--border-color] overflow-hidden shadow-sm">
    <div class="p-6 border-b border-[--border-color] flex justify-between items-center">
        <h3 class="font-semibold text-[--text-main]">Versiones Disponibles</h3>
        <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            Registrar Nueva Versión
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[--bg-main] text-[--text-muted] text-xs uppercase font-semibold">
                    <th class="px-6 py-4">Versión</th>
                    <th class="px-6 py-4">Algoritmo</th>
                    <th class="px-6 py-4">Métricas</th>
                    <th class="px-6 py-4">Dataset</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[--border-color]">
                @forelse($versions as $version)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-[--text-main]">{{ $version->version_tag }}</div>
                        <div class="text-xs text-[--text-muted]">{{ $version->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-[--text-main]">{{ $version->algorithm }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            @if($version->metrics)
                                @foreach($version->metrics as $key => $value)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded border border-blue-100 uppercase">
                                        {{ $key }}: {{ is_numeric($value) ? number_format($value, 3) : $value }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-[--text-main]">
                        {{ $version->dataset_info['name'] ?? 'Local Hospital' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($model->active_version_id == $version->id)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">En Producción</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full uppercase">{{ $version->status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            @if($model->active_version_id != $version->id)
                            <form action="{{ route('ml.models.activate', $model) }}" method="POST">
                                @csrf
                                <input type="hidden" name="version_id" value="{{ $version->id }}">
                                <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Activar esta versión">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            @endif
                            <button class="p-2 text-[--text-muted] hover:bg-gray-100 rounded-lg transition-colors">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-[--text-muted]">No hay versiones registradas para este modelo.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
