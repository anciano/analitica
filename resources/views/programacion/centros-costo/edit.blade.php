@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center mb-2 text-[12px] text-[--text-muted]">
            <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
            <span class="mx-2">/</span>
            <a href="{{ route('programacion.centros-costo.index') }}" class="hover:text-[--primary-blue]">Centros de
                Costo</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-[--text-main]">Editar Centro</span>
        </div>
        <h1 class="text-[24px] font-semibold text-[--text-main]">Editar Centro de Costo</h1>
        <p class="text-[14px] text-[--text-muted]">Actualice la información o estado de la unidad</p>
    </div>

    <div class="max-w-2xl">
        <div class="card bg-white p-8">
            <form action="{{ route('programacion.centros-costo.update', $centro->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <span class="block text-[11px] font-bold text-[--text-muted] uppercase mb-1">Nivel Actual</span>
                            <span class="text-[16px] font-bold text-[--text-main]">Nivel {{ $centro->nivel }}</span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <span class="block text-[11px] font-bold text-[--text-muted] uppercase mb-1">Dependencia</span>
                            <span
                                class="text-[14px] font-bold text-[--text-main]">{{ $centro->parent->nombre ?? 'Ninguna (Raíz)' }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[--text-main] mb-2 uppercase tracking-wide">Código
                            del Centro</label>
                        <input type="text" name="codigo" value="{{ old('codigo', $centro->codigo) }}"
                            class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-[--primary-blue]/20 outline-none @error('codigo') border-[--danger] @enderror"
                            placeholder="Ej: 110101" required>
                        @error('codigo')
                            <p class="mt-1 text-[12px] text-[--danger]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[--text-main] mb-2 uppercase tracking-wide">Nombre /
                            Denominación</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $centro->nombre) }}"
                            class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-[--primary-blue]/20 outline-none @error('nombre') border-[--danger] @enderror"
                            placeholder="Ej: Unidad de Contabilidad" required>
                        @error('nombre')
                            <p class="mt-1 text-[12px] text-[--danger]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[--text-main] mb-2 uppercase tracking-wide">Estado
                            Operativo</label>
                        <select name="activo"
                            class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-[--primary-blue]/20 outline-none">
                            <option value="1" {{ old('activo', $centro->activo) ? 'selected' : '' }}>Activo (Permite
                                programación)</option>
                            <option value="0" {{ old('activo', $centro->activo) ? '' : 'selected' }}>Inactivo (Bloqueado)
                            </option>
                        </select>
                    </div>

                    <div class="pt-6 border-t border-[--border-soft] flex justify-between items-center">
                        <button type="button" @click="history.back()"
                            class="text-[14px] text-[--text-muted] hover:underline">
                            Volver atrás
                        </button>
                        <div class="flex gap-3">
                            <a href="{{ route('programacion.centros-costo.index') }}"
                                class="px-6 py-2 rounded-lg text-[14px] font-bold text-[--text-muted] hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="btn">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection