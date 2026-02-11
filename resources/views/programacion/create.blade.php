@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <nav class="flex mb-8 text-[12px] text-[--text-muted]" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-1">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span class="font-medium">Nuevo Plan</span>
                </li>
            </ol>
        </nav>

        <div class="card bg-white">
            <div class="mb-6">
                <h1 class="text-[22px] font-semibold text-[--text-main]">Crear Nuevo Plan Anual</h1>
                <p class="text-[13px] text-[--text-muted]">Defina los parámetros básicos para iniciar el proceso de
                    programación.</p>
            </div>

            <form action="{{ route('programacion.planes.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="anio"
                            class="block text-[13px] font-semibold text-[--text-main] mb-2 uppercase tracking-wide">Año
                            Fiscal</label>
                        <div class="relative">
                            <select id="anio" name="anio"
                                class="w-full bg-gray-50 border border-[--border-soft] rounded-lg text-[14px] text-[--text-main] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] transition-all"
                                style="padding: 12px; appearance: none;">
                                @for($i = date('Y'); $i <= date('Y') + 2; $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') + 1 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[--text-muted]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="nombre"
                            class="block text-[13px] font-semibold text-[--text-main] mb-2 uppercase tracking-wide">Nombre
                            del Plan</label>
                        <input type="text" id="nombre" name="nombre"
                            class="w-full bg-gray-50 border border-[--border-soft] rounded-lg text-[14px] text-[--text-main] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] transition-all"
                            placeholder="Ej: Presupuesto Inicial 2026" required style="padding: 12px;">
                        <p class="mt-2 text-[12px] text-[--text-muted]">Este nombre aparecerá en los reportes y tableros
                            comparativos.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-[--border-soft] flex justify-end items-center gap-4">
                    <a href="{{ route('programacion.index') }}"
                        class="text-[14px] font-medium text-[--text-muted] hover:text-[--text-main] transition">
                        Cancelar
                    </a>
                    <button type="submit" class="btn px-8">
                        Crear Plan (v1)
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection