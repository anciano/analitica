@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-[24px] font-semibold text-[--text-main]">Nuevos Registros de Datos</h1>
        <p class="text-[14px] text-[--text-muted]">Sube archivos Excel/CSV para validación y carga en el sistema.</p>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data" class="card space-y-6">
            @csrf

            <div class="p-4 bg-gray-50 rounded-lg border border-[--border-soft]">
                <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Dataset y
                    Versión</label>
                @if($versions->count() > 1)
                    <select name="dataset_version_id"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                        @foreach($versions as $version)
                            <option value="{{ $version->id }}" {{ $version->id == $defaultVersionId ? 'selected' : '' }}>
                                {{ $version->dataset->name }} ({{ $version->version }})
                            </option>
                        @endforeach
                    </select>
                @else
                    @php $version = $versions->first(); @endphp
                    <input type="hidden" name="dataset_version_id" value="{{ $version->id }}">
                    <div
                        class="px-4 py-2 bg-white rounded-lg border border-[--border-soft] text-[14px] font-medium text-[--text-main]">
                        {{ $version->dataset->name }} ({{ $version->version }})
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Año</label>
                    <input type="number" name="anio" value="{{ date('Y') }}" required min="2020" max="2030"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Mes</label>
                    <select name="mes"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-[--border-soft]">
                <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Archivo (Excel /
                    CSV)</label>
                <div
                    class="border-2 border-dashed border-[--border-soft] rounded-xl p-8 text-center hover:bg-gray-50/50 transition cursor-pointer relative">
                    <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    <div class="flex flex-col items-center">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            class="text-[--text-muted] mb-2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span class="text-[14px] text-[--text-main] font-medium">Haz clic para seleccionar o arrastra un
                            archivo</span>
                        <span class="text-[12px] text-[--text-muted] mt-1">Máximo 10MB (XLSX, CSV)</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="btn w-full justify-center py-3">Iniciar Procesamiento</button>
            </div>
        </form>
    </div>
@endsection