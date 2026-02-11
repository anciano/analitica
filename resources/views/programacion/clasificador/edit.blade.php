@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2 text-[12px] text-[--text-muted]">
                <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
                <span class="mx-2">/</span>
                <a href="{{ route('programacion.clasificador.index') }}" class="hover:text-[--primary-blue]">Clasificador</a>
                <span class="mx-2">/</span>
                <span class="font-medium text-[--text-main]">Editar Ítem</span>
            </div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Editar Ítem: {{ $item->codigo }}</h1>
            <p class="text-[14px] text-[--text-muted]">Modifique la denominación o el estado del ítem presupuestario</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="card bg-white">
                <form action="{{ route('programacion.clasificador.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[13px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Código</label>
                                <input type="text" name="codigo" value="{{ old('codigo', $item->codigo) }}" required
                                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] bg-gray-50 text-[14px] focus:outline-none focus:border-[--primary-blue] @error('codigo') border-red-500 @enderror">
                                @error('codigo') <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p> @enderror
                                <p class="text-[11px] text-[--text-muted] mt-1">Si cambia el código, la jerarquía se recalculará al guardar.</p>
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Año de Vigencia</label>
                                <select name="anio_vigencia" required
                                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue]">
                                    @foreach($anios as $anio)
                                        <option value="{{ $anio }}" {{ old('anio_vigencia', $item->anio_vigencia) == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[13px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Denominación</label>
                            <input type="text" name="denominacion" value="{{ old('denominacion', $item->denominacion) }}" required
                                class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue] @error('denominacion') border-red-500 @enderror">
                            @error('denominacion') <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg border border-[--border-soft]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[13px] font-bold text-[--text-main]">Nivel Actual: {{ $item->nivel }}</div>
                                    <div class="text-[11px] text-[--text-muted]">Calculado automáticamente según el código.</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[13px] font-bold text-[--text-main]">Padre: {{ $item->parent ? $item->parent->codigo : 'Raíz' }}</div>
                                    <div class="text-[11px] text-[--text-muted]">{{ $item->parent ? Str::limit($item->parent->denominacion, 20) : 'Sin padre' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $item->activo) ? 'checked' : '' }}
                                class="w-4 h-4 text-[--primary-blue] border-gray-300 rounded focus:ring-[--primary-blue]">
                            <label for="activo" class="text-[14px] font-medium text-[--text-main]">Ítem Activo</label>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-[--border-soft] flex justify-between items-center">
                        <button type="button" onclick="window.confirm('¿Eliminar definitivamente? Esta acción no se recomienda si existen datos históricos.') && document.getElementById('delete-form').submit()"
                            class="text-[--danger] text-[12px] font-bold uppercase tracking-wide hover:underline">Eliminar Permanentemente</button>
                        
                        <div class="flex gap-3">
                            <a href="{{ route('programacion.clasificador.index') }}" 
                                class="px-6 py-2 rounded-lg text-[14px] font-bold text-[--text-muted] hover:bg-gray-50 transition uppercase tracking-wide">Cancelar</a>
                            <button type="submit" 
                                class="bg-[--primary-blue] text-white px-8 py-2 rounded-lg text-[14px] font-bold hover:opacity-90 transition uppercase tracking-wide">
                                Actualizar Ítem
                            </button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('programacion.clasificador.destroy', $item->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <div>
            <div class="card bg-white">
                <h3 class="text-[15px] font-bold text-[--text-main] mb-4">Información de Auditoría</h3>
                <div class="space-y-4">
                    <div>
                        <div class="text-[11px] font-bold text-[--text-muted] uppercase tracking-wider">Creado el</div>
                        <div class="text-[14px] text-[--text-main]">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-[--text-muted] uppercase tracking-wider">Última Modificación</div>
                        <div class="text-[14px] text-[--text-main]">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
