@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center gap-2 text-[12px] text-[--text-muted] mb-2">
            <a href="{{ route('imports.index') }}" class="hover:text-[--primary-blue]">Carga de Datos</a>
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"></path>
            </svg>
            <span class="font-medium text-[--text-main]">Editar Carga #{{ $import->id }}</span>
        </div>
        <h1 class="text-[24px] font-semibold text-[--text-main]">Ajustar Periodo de Carga</h1>
        <p class="text-[14px] text-[--text-muted]">Cambie el año o mes asociado a esta carga de datos.</p>
    </div>

    <div class="max-w-xl">
        <div class="card mb-6 bg-blue-50/30 border-blue-100">
            <div class="flex gap-3">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    class="text-blue-500 shrink-0">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <div class="text-[13px] text-blue-800 leading-relaxed">
                    <b>Nota:</b> Al cambiar el periodo, se actualizarán automáticamente todos los registros de ejecución
                    fact asociados a esta carga (#{{ $import->id }}). Esto afectará directamente a los gráficos y
                    reportes financieros.
                </div>
            </div>
        </div>

        <form action="{{ route('imports.update', $import) }}" method="POST" class="card space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Año
                        Destino</label>
                    <input type="number" name="target_anio" value="{{ old('target_anio', $import->target_anio) }}" required
                        min="2020" max="2030"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                    @error('target_anio') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Mes
                        Destino</label>
                    <select name="target_mes"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ old('target_mes', $import->target_mes) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('target_mes') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-[--border-soft] flex justify-end gap-3">
                <a href="{{ route('imports.index') }}"
                    class="px-6 py-2 text-[14px] font-bold text-[--text-muted] hover:bg-gray-100 transition rounded-lg">Cancelar</a>
                <button type="submit" class="btn">Actualizar Periodo</button>
            </div>
        </form>
    </div>
@endsection