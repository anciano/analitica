@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center mb-2 text-[12px] text-[--text-muted]">
            <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
            <span class="mx-2">/</span>
            <a href="{{ route('programacion.centros-costo.index') }}" class="hover:text-[--primary-blue]">Centros de
                Costo</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-[--text-main]">Crear Nuevo</span>
        </div>
        <h1 class="text-[24px] font-semibold text-[--text-main]">Nuevo Centro de Costo</h1>
        <p class="text-[14px] text-[--text-muted]">Agregue una nueva unidad operativa o administrativa</p>
    </div>

    <div class="max-w-2xl">
        <div class="card bg-white p-8">
            <form action="{{ route('programacion.centros-costo.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-[13px] font-bold text-[--text-main] mb-2 uppercase tracking-wide">Código
                            del Centro</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}"
                            class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-[--primary-blue]/20 outline-none @error('codigo') border-[--danger] @enderror"
                            placeholder="Ej: 110101" required>
                        @error('codigo')
                            <p class="mt-1 text-[12px] text-[--danger]">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-[11px] text-[--text-muted]">
                            El nivel y la dependencia se calcularán automáticamente según el código ingresado.
                        </p>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[--text-main] mb-2 uppercase tracking-wide">Nombre /
                            Denominación</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-[--primary-blue]/20 outline-none @error('nombre') border-[--danger] @enderror"
                            placeholder="Ej: Unidad de Contabilidad" required>
                        @error('nombre')
                            <p class="mt-1 text-[12px] text-[--danger]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-6 border-t border-[--border-soft] flex justify-end gap-3">
                        <a href="{{ route('programacion.centros-costo.index') }}"
                            class="px-6 py-2 rounded-lg text-[14px] font-bold text-[--text-muted] hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="btn">
                            Guardar Centro de Costo
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="mt-8 p-6 bg-blue-50/50 border border-blue-100 rounded-xl">
            <h3 class="text-[14px] font-bold text-blue-900 mb-2">💡 Guía de Jerarquía</h3>
            <ul class="text-[13px] text-blue-800 space-y-2">
                <li>• <b>Nivel 1 (1 dígito):</b> Director / Institución (Ej: 1)</li>
                <li>• <b>Nivel 2 (3 dígitos):</b> Subdirecciones (Ej: 110)</li>
                <li>• <b>Nivel 3 (4 dígitos):</b> Departamentos (Ej: 1101)</li>
                <li>• <b>Nivel 4 (6 dígitos):</b> Unidades / Secciones (Ej: 110101)</li>
            </ul>
        </div>
    </div>
@endsection