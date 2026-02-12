@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-start">
        <div>
            <div class="flex items-center mb-6 text-[12px] text-[--text-muted]" aria-label="Breadcrumb">
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
                        <span class="font-medium text-[--text-main]">{{ $plan->nombre }}</span>
                    </li>
                </ol>
            </div>
            <h1 class="text-[26px] font-bold text-[--text-main] tracking-tight">{{ $plan->nombre }} (v{{ $plan->version }})
            </h1>
            <div class="flex items-center gap-4 mt-2">
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold text-[--text-muted] uppercase tracking-wider">Año Fiscal</span>
                    <span class="text-[14px] font-semibold text-[--text-main]">{{ $plan->anio }}</span>
                </div>
                <span class="w-1.5 h-1.5 rounded-full bg-gray-200"></span>
                <span
                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide
                                            {{ $plan->estado === 'aprobado' ? 'bg-[--success]/10 text-[--success]' : ($plan->estado === 'borrador' ? 'bg-[--warning]/10 text-[--warning]' : 'bg-gray-100 text-gray-700') }}">
                    {{ $plan->estado }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($plan->estado === 'borrador')
                <a href="{{ route('programacion.planes.items.create', $plan->id) }}"
                    class="bg-white border border-[--border-soft] text-[--text-main] text-[12px] font-bold px-4 py-2 rounded-lg hover:bg-gray-50 transition uppercase tracking-wide">
                    Asignar Ítem / CC
                </a>
                <form action="{{ route('programacion.planes.aprobar', $plan->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-[--success] text-white text-[12px] font-bold px-6 py-2 rounded-lg hover:opacity-90 transition uppercase tracking-wide">
                        Aprobar Plan
                    </button>
                </form>
            @else
                <form action="{{ route('programacion.planes.versionar', $plan->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-[--primary-blue] text-white text-[12px] font-bold px-6 py-2 rounded-lg hover:opacity-90 transition uppercase tracking-wide">
                        Crear Nueva Versión
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Tabla de Ítems -->
    <div class="card bg-white overflow-hidden p-0">
        <div class="px-6 py-4 border-b border-[--border-soft] flex justify-between items-center bg-gray-50/30">
            <h2 class="text-[15px] font-semibold text-[--text-main]">Detalle de Asignaciones</h2>
            <div class="text-[13px] font-medium text-[--text-muted]">
                Total Planificado: <span
                    class="text-[--primary-blue] font-bold">${{ number_format($plan->items->sum('monto_anual'), 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="bg-gray-50/80 text-[--text-muted] uppercase text-[11px] font-bold tracking-wider border-b border-[--border-soft]">
                        <th class="px-6 py-4">Clasificador</th>
                        <th class="px-6 py-4">Centro de Costo</th>
                        <th class="px-6 py-4 text-right">Monto Anual</th>
                        <th class="px-6 py-4 text-right">Ejecutado</th>
                        <th class="px-6 py-4">Avance / Mensual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[--border-soft]">
                    @forelse($treeData as $node)
                        @php 
                            $cl = $node->clasificador;
                            $indent = ($cl->nivel - 1) * 2;
                            $isParent = !$node->is_leaf;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition {{ $isParent ? 'bg-gray-50/30' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-2" style="padding-left: {{ $indent }}rem;">
                                    @if($isParent)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-[--primary-blue]">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    @endif
                                    <div>
                                        <div class="text-[13px] {{ $isParent ? 'font-bold text-[--text-main]' : 'font-semibold text-[--text-main]' }}">
                                            {{ $cl->codigo }}
                                        </div>
                                        <div class="text-[11px] {{ $isParent ? 'text-[--text-main] font-medium' : 'text-[--text-muted]' }}">
                                            {{ $cl->denominacion }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(!$isParent && $node->items->count() > 0)
                                    @foreach($node->items as $item)
                                        <div class="text-[12px] font-medium text-[--text-main]">{{ $item->centroCosto->nombre }}</div>
                                        <div class="text-[10px] text-[--text-muted] mb-1">Cód: {{ $item->centroCosto->codigo }}</div>
                                    @endforeach
                                @elseif($isParent)
                                    <span class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider bg-gray-100 px-1.5 py-0.5 rounded">Agregado</span>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Sin asignación CC</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-[14px] {{ $isParent ? 'font-black text-[--text-main]' : 'font-bold text-[--text-main]' }}">
                                    ${{ number_format($node->monto_programado, 0, ',', '.') }}
                                </span>
                                @if($isParent)
                                    <div class="text-[9px] font-bold text-[--primary-blue] uppercase">Calculado</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-[14px] {{ $isParent ? 'font-black text-[--text-main]' : 'font-bold text-[--text-main]' }}">
                                    ${{ number_format($node->ejecutado, 0, ',', '.') }}
                                </div>
                                @php 
                                    $avance = $node->monto_programado > 0 ? ($node->ejecutado / $node->monto_programado) * 100 : 0;
                                @endphp
                                <div class="w-full bg-gray-100 rounded-full h-1 mt-1 overflow-hidden">
                                    <div class="h-full rounded-full {{ $avance > 90 ? 'bg-[--danger]' : ($avance > 50 ? 'bg-[--warning]' : 'bg-[--success]') }}"
                                        style="width: {{ min($avance, 100) }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="text-[11px] font-bold {{ $avance > 100 ? 'text-[--danger]' : 'text-[--text-muted]' }}">
                                        {{ number_format($avance, 1) }}%
                                    </div>
                                    @if(!$isParent && $node->items->count() > 0)
                                        @foreach($node->items as $item)
                                            @php $mesesCount = $item->mensualizaciones->count(); @endphp
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($mesesCount == 12)
                                                    <span class="text-[--success] text-[10px] font-bold flex items-center gap-0.5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                        Meses OK
                                                    </span>
                                                @else
                                                    <a href="{{ route('programacion.planes.items.distribuir', $item->id) }}" class="text-[10px] font-bold text-[--warning] hover:underline uppercase">
                                                        Distrib. ({{ $mesesCount }}/12)
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[--text-muted] italic text-[13px]">
                                Este plan aún no tiene ítemes asignados. Comience por agregar una asignación anual.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection