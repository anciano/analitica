@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center mb-8 text-[12px] text-[--text-muted]" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('programacion.index') }}"
                        class="hover:text-[--primary-blue] transition-colors">Programación</a>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mx-1 text-gray-300">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <a href="{{ route('programacion.planes.show', $plan->id) }}"
                        class="hover:text-[--primary-blue] transition-colors">{{ $plan->nombre }}</a>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mx-1 text-gray-300">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span class="font-medium text-[--text-main]">Asignar Ítem</span>
                </li>
            </ol>
        </div>

        <div class="card bg-white">
            <div class="mb-6">
                <h1 class="text-[22px] font-semibold text-[--text-main]">Asignar Ítem Presupuestario</h1>
                <p class="text-[13px] text-[--text-muted]">Asigne un monto anual a un clasificador y centro de costo para el
                    plan {{ $plan->anio }}.</p>
            </div>

            <form action="{{ route('programacion.planes.items.store', $plan->id) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <!-- Clasificador -->
                    <div>
                        <label for="clasificador_item_id"
                            class="block text-[13px] font-semibold text-[--text-main] mb-2 uppercase tracking-wide">Ítem del
                            Clasificador</label>
                        <div class="relative">
                            <select id="clasificador_item_id" name="clasificador_item_id"
                                class="w-full bg-gray-50 border border-[--border-soft] rounded-lg text-[14px] text-[--text-main] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] transition-all"
                                style="padding: 12px; appearance: none; width: 100%;" required>
                                <option value="">Seleccione un ítem...</option>
                                @foreach($clasificadores as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->codigo }} - {{ $cl->denominacion }} (Nivel
                                        {{ $cl->nivel }})
                                    </option>
                                @endforeach
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

                    <!-- Centro de Costo -->
                    <div>
                        <label for="centro_costo_id"
                            class="block text-[13px] font-semibold text-[--text-main] mb-2 uppercase tracking-wide">Centro
                            de Costo</label>
                        <div class="relative">
                            <select id="centro_costo_id" name="centro_costo_id"
                                class="w-full bg-gray-50 border border-[--border-soft] rounded-lg text-[14px] text-[--text-main] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] transition-all"
                                style="padding: 12px; appearance: none; width: 100%;" required>
                                <option value="">Seleccione un centro de costo...</option>
                                @foreach($centrosCosto as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->codigo }} - {{ $cc->nombre }}</option>
                                @endforeach
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

                    <!-- Monto Anual -->
                    <div>
                        <label for="monto_anual"
                            class="block text-[13px] font-semibold text-[--text-main] mb-2 uppercase tracking-wide">Monto
                            Anual ($)</label>
                        <input type="number" id="monto_anual" name="monto_anual"
                            class="w-full bg-gray-50 border border-[--border-soft] rounded-lg text-[14px] text-[--text-main] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] transition-all"
                            placeholder="0" min="0" required style="padding: 12px;">
                    </div>
                </div>

                <div class="pt-6 border-t border-[--border-soft] flex justify-end items-center gap-4">
                    <a href="{{ route('programacion.planes.show', $plan->id) }}"
                        class="text-[14px] font-medium text-[--text-muted] hover:text-[--text-main] transition">
                        Cancelar
                    </a>
                    <button type="submit" class="btn px-8">
                        Guardar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection