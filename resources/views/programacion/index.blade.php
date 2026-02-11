@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Programación Presupuestaria</h1>
            <p class="text-[14px] text-[--text-muted]">Planificación financiera anual y por centros de costo</p>
        </div>
        <a href="{{ route('programacion.planes.create') }}" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Plan Anual
        </a>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card bg-white">
            <h3 class="text-[14px] font-semibold text-[--text-muted] uppercase tracking-wider mb-2">Total Programado
                {{ $latestApproved ? $latestApproved->anio : '2026' }}
            </h3>
            <p class="text-[28px] font-bold text-[--text-main]">${{ number_format($totalProgramado, 0, ',', '.') }}</p>
            <div class="mt-2 text-[12px] flex items-center text-[--text-muted]">
                <span class="bg-[--primary-blue]/10 text-[--primary-blue] px-2 py-0.5 rounded mr-2">Versión aprobada:
                    {{ $latestApproved ? 'v' . $latestApproved->version : 'N/A' }}</span>
            </div>
        </div>

        <div class="card bg-white">
            <h3 class="text-[14px] font-semibold text-[--text-muted] uppercase tracking-wider mb-2">Ejecución Devengada</h3>
            <p class="text-[28px] font-bold text-[--text-main]">${{ number_format($totalEjecutado, 0, ',', '.') }}</p>
            <div
                class="mt-2 text-[12px] flex items-center {{ $porcentajeGlobal > 100 ? 'text-[--danger]' : 'text-[--success]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                {{ number_format($porcentajeGlobal, 1) }}% de avance global
            </div>
        </div>

        <div class="card bg-white">
            <h3 class="text-[14px] font-semibold text-[--text-muted] uppercase tracking-wider mb-2">Centros de Costo</h3>
            <p class="text-[28px] font-bold text-[--text-main]">{{ $centrosConPresupuesto }}</p>
            <div class="mt-2 text-[12px] text-[--text-muted]">
                Unidades con presupuesto asignado y aprobado
            </div>
        </div>
    </div>

    <!-- Menú de Mantenedores -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="{{ route('programacion.clasificador.index') }}"
            class="card bg-white hover:border-[--primary-blue] transition group">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-[--primary-blue] group-hover:bg-[--primary-blue] group-hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-[--text-main]">Mantenedor de Clasificador</h3>
                    <p class="text-[12px] text-[--text-muted]">Gestionar ítems, niveles y jerarquías presupuestarias.</p>
                </div>
            </div>
        </a>

        <a href="{{ route('programacion.centros-costo.index') }}"
            class="card bg-white hover:border-[--primary-blue] transition group">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-[--text-main]">Centros de Costo</h3>
                    <p class="text-[12px] text-[--text-muted]">Administrar la estructura organizacional y unidades.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Listado de Planes -->
    <div class="card bg-white overflow-hidden p-0">
        <div class="px-6 py-4 border-b border-[--border-soft] flex justify-between items-center bg-gray-50/30">
            <h2 class="text-[15px] font-semibold text-[--text-main]">Historial de Planes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-[--text-muted] uppercase text-[11px] font-semibold border-b border-[--border-soft]">
                        <th class="px-6 py-3">Año / Versión</th>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Aprobado el</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[--border-soft]">
                    @forelse($planes as $plan)
                        <tr class="hover:bg-gray-50/50 transition text-[13px] text-[--text-main]">
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ $plan->anio }}</div>
                                <div class="text-[--primary-blue] text-[11px] font-medium">v{{ $plan->version }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $plan->nombre }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-0.5 rounded-full text-[11px] font-medium 
                                                            {{ $plan->estado === 'aprobado' ? 'bg-[--success]/10 text-[--success]' : ($plan->estado === 'borrador' ? 'bg-[--warning]/10 text-[--warning]' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($plan->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[--text-muted]">
                                {{ $plan->aprobado_at ? $plan->aprobado_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 flex-wrap">
                                    <a href="{{ route('programacion.planes.show', $plan->id) }}"
                                        class="text-[--primary-blue] hover:underline font-semibold">Ver</a>
                                    @if($plan->estado === 'borrador')
                                        <form action="{{ route('programacion.planes.aprobar', $plan->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-[--success] hover:underline bg-transparent !p-0 !border-none cursor-pointer text-[13px] font-bold">Aprobar</button>
                                        </form>
                                    @elseif($plan->estado === 'aprobado')
                                        <form action="{{ route('programacion.planes.versionar', $plan->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-[--primary-blue]/10 text-[--primary-blue] hover:bg-[--primary-blue]/20 px-3 py-1.5 rounded-lg !border-none cursor-pointer text-[11px] font-bold uppercase tracking-wider transition">Nueva
                                                Versión</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[--text-muted] italic">No hay planes registrados
                                aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection