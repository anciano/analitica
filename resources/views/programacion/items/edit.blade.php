@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center mb-6 text-[12px] text-[--text-muted]" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue] transition-colors">Programación</a>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-1 text-gray-300"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    <a href="{{ route('programacion.planes.show', $item->plan_id) }}" class="hover:text-[--primary-blue] transition-colors">{{ $item->plan->nombre }}</a>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-1 text-gray-300"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    <span class="font-medium text-[--text-main]">Editar Monto</span>
                </li>
            </ol>
        </div>
        <h1 class="text-[26px] font-bold text-[--text-main] tracking-tight">Editar Monto de Asignación</h1>
        <p class="text-[14px] text-[--text-muted] mt-1">{{ $item->clasificadorItem->codigo }} - {{ $item->clasificadorItem->denominacion }}</p>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('programacion.planes.items.update', $item->id) }}" method="POST" class="card p-8 bg-white">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl border border-[--border-soft]">
                    <div>
                        <label class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider block mb-1">Centro de Costo</label>
                        <div class="text-[13px] font-bold text-[--text-main]">{{ $item->centroCosto->nombre }}</div>
                        <div class="text-[11px] text-[--text-muted]">Cód: {{ $item->centroCosto->codigo }}</div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider block mb-1">Año Fiscal</label>
                        <div class="text-[13px] font-bold text-[--text-main]">{{ $item->plan->anio }}</div>
                    </div>
                </div>

                <div>
                    <label for="monto_anual" class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Nuevo Monto Anual ($)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                        <input type="number" name="monto_anual" id="monto_anual" 
                            value="{{ old('monto_anual', $item->monto_anual) }}"
                            class="w-full pl-8 pr-4 py-3 rounded-xl border border-[--border-soft] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] outline-none transition-all font-bold text-[16px]"
                            placeholder="0" required min="0">
                    </div>
                    @error('monto_anual')
                        <p class="text-[--danger] text-[12px] mt-2 font-medium">{{ $message }}</p>
                    @enderror
                    
                    @if($item->mensualizaciones->count() > 0)
                        <div class="mt-4 p-3 bg-[--warning]/10 border border-[--warning]/20 rounded-lg flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[--warning] shrink-0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <p class="text-[11px] text-[--warning] font-semibold leading-normal">
                                <span class="uppercase block mb-0.5">Atención:</span>
                                Al cambiar el monto anual, la distribución mensual actual será eliminada y deberá ser configurada nuevamente para cuadrar con el nuevo monto.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[--border-soft]">
                    <a href="{{ route('programacion.planes.show', $item->plan_id) }}" 
                        class="px-6 py-2.5 text-[12px] font-bold text-[--text-muted] uppercase tracking-wide hover:text-[--text-main] transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                        class="bg-[--primary-blue] text-white px-8 py-2.5 rounded-xl text-[12px] font-bold uppercase tracking-wide hover:opacity-90 transition shadow-lg shadow-[--primary-blue]/20">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
