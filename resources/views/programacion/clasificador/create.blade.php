@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2 text-[12px] text-[--text-muted]">
                <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
                <span class="mx-2">/</span>
                <a href="{{ route('programacion.clasificador.index') }}" class="hover:text-[--primary-blue]">Clasificador</a>
                <span class="mx-2">/</span>
                <span class="font-medium text-[--text-main]">Nuevo Ítem</span>
            </div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Crear Nuevo Ítem de Clasificador</h1>
            <p class="text-[14px] text-[--text-muted]">La jerarquía y el nivel se calcularán automáticamente según el código</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="card bg-white">
                <form action="{{ route('programacion.clasificador.store') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[13px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Código</label>
                                <input type="text" name="codigo" value="{{ old('codigo') }}" required
                                    placeholder="Ej: 2101001"
                                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue] @error('codigo') border-red-500 @enderror">
                                @error('codigo') <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p> @enderror
                                <p class="text-[11px] text-[--text-muted] mt-1">Solo números. El sistema determinará el nivel (1-5).</p>
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Año de Vigencia</label>
                                <select name="anio_vigencia" required
                                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue]">
                                    @foreach($anios as $anio)
                                        <option value="{{ $anio }}" {{ old('anio_vigencia', 2026) == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[13px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Denominación</label>
                            <input type="text" name="denominacion" value="{{ old('denominacion') }}" required
                                placeholder="Nombre completo del subtítulo o ítem"
                                class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue] @error('denominacion') border-red-500 @enderror">
                            @error('denominacion') <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="activo" value="1" id="activo" {{ old('activo', 1) ? 'checked' : '' }}
                                class="w-4 h-4 text-[--primary-blue] border-gray-300 rounded focus:ring-[--primary-blue]">
                            <label for="activo" class="text-[14px] font-medium text-[--text-main]">Ítem Activo (disponible para programación)</label>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-[--border-soft] flex justify-end gap-3">
                        <a href="{{ route('programacion.clasificador.index') }}" 
                            class="px-6 py-2 rounded-lg text-[14px] font-bold text-[--text-muted] hover:bg-gray-50 transition uppercase tracking-wide">Cancelar</a>
                        <button type="submit" 
                            class="bg-[--primary-blue] text-white px-8 py-2 rounded-lg text-[14px] font-bold hover:opacity-90 transition uppercase tracking-wide">
                            Guardar Ítem
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="card bg-gray-50 border-dashed border-2 border-gray-200">
                <h3 class="text-[15px] font-bold text-[--text-main] mb-4">Reglas de Jerarquía</h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-[--primary-blue] flex items-center justify-center text-[11px] font-bold shrink-0">1</span>
                        <div class="text-[13px] text-[--text-muted]">
                            <strong class="text-[--text-main]">Nivel 1 (Subtítulo)</strong>: Códigos de 2 dígitos (ej: 21).
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-[--primary-blue] flex items-center justify-center text-[11px] font-bold shrink-0">2</span>
                        <div class="text-[13px] text-[--text-muted]">
                            <strong class="text-[--text-main]">Nivel 2 (Ítem)</strong>: Códigos de 4 dígitos (ej: 2101).
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-[--primary-blue] flex items-center justify-center text-[11px] font-bold shrink-0">3</span>
                        <div class="text-[13px] text-[--text-muted]">
                            <strong class="text-[--text-main]">Nivel 3 (Asignación)</strong>: Códigos de 7 dígitos (ej: 2101001).
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-[--primary-blue] flex items-center justify-center text-[11px] font-bold shrink-0">4+</span>
                        <div class="text-[13px] text-[--text-muted]">
                            El sistema enlaza automáticamente al padre si el prefijo existe para el mismo año.
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
