@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-[--text-main]">Gobernanza de Modelos IA</h1>
    <p class="text-[--text-muted] text-sm mt-1">Administración y control de versiones de modelos predictivos GRD.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    @foreach($models as $model)
    <div class="bg-white rounded-xl border border-[--border-color] p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
            @if($model->activeVersion)
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Activo: {{ $model->activeVersion->version_tag }}</span>
            @else
                <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Sin versión activa</span>
            @endif
        </div>
        
        <h3 class="text-lg font-semibold text-[--text-main] mb-2">{{ $model->name }}</h3>
        <p class="text-sm text-[--text-muted] mb-6 line-clamp-2">{{ $model->description }}</p>
        
        <div class="flex items-center gap-4 mb-6">
            <div class="text-center flex-1">
                <div class="text-xs text-[--text-muted] uppercase font-semibold">Algoritmo</div>
                <div class="text-sm font-medium text-[--text-main]">{{ $model->activeVersion->algorithm ?? 'N/A' }}</div>
            </div>
            <div class="w-px h-8 bg-[--border-color]"></div>
            <div class="text-center flex-1">
                <div class="text-xs text-[--text-muted] uppercase font-semibold">Precisión</div>
                <div class="text-sm font-medium text-[--text-main]">
                    {{ isset($model->activeVersion->metrics['accuracy']) ? number_format($model->activeVersion->metrics['accuracy'] * 100, 1) . '%' : 'N/A' }}
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('ml.models.show', $model) }}" class="flex-1 text-center py-2 px-4 bg-[--bg-main] border border-[--border-color] text-[--text-main] rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Ver Versiones
            </a>
            <a href="#" class="py-2 px-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection
