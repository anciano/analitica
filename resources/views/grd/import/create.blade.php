@extends('layouts.app')

@section('content')
<div class="mb-8">
    <a href="{{ route('grd.import.index') }}" class="text-sm text-blue-500 hover:underline flex items-center gap-1 mb-2">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 19l-7-7 7-7"></path>
        </svg>
        Volver al historial
    </a>
    <h1 class="text-2xl font-bold text-[--text-main]">Nueva Carga de Egresos GRD</h1>
    <p class="text-[--text-muted] text-sm mt-1">Sube el archivo Excel con los datos de egresos hospitalarios.</p>
</div>

<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('grd.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-[--text-main] mb-2">Versión del Dataset</label>
                <select name="dataset_version_id" class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] bg-[#F9FAFB] outline-none focus:ring-2 focus:ring-blue-500/20">
                    @foreach($versions as $version)
                        <option value="{{ $version->id }}" {{ $defaultVersionId == $version->id ? 'selected' : '' }}>
                            {{ $version->dataset->name }} - v{{ $version->version }}
                        </option>
                    @endforeach
                </select>
                @error('dataset_version_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-[--text-main] mb-2">Año</label>
                    <input type="number" name="anio" value="{{ date('Y') }}" min="2018" max="2030"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] bg-[#F9FAFB] outline-none focus:ring-2 focus:ring-blue-500/20">
                    @error('anio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[--text-main] mb-2">Mes</label>
                    <select name="mes" class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] bg-[#F9FAFB] outline-none focus:ring-2 focus:ring-blue-500/20">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    @error('mes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-semibold text-[--text-main] mb-2">Archivo Excel (.xlsx, .xls, .csv)</label>
                <div class="relative border-2 border-dashed border-[--border-soft] rounded-xl p-8 hover:bg-blue-50/50 transition-colors group">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-blue-600">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-[--text-main]">Suelta el archivo aquí o haz clic para buscar</p>
                        <p class="text-xs text-[--text-muted] mt-1">Máximo 20MB. Formatos compatibles: Excel, CSV.</p>
                    </div>
                </div>
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[--border-soft]">
                <a href="{{ route('grd.import.index') }}" class="px-6 py-2 rounded-lg text-sm font-medium text-[--text-muted] hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="btn px-8">
                    Iniciar Carga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
