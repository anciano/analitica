@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex items-center mb-6 text-[12px] text-[--text-muted]" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('finance.resumen') }}" class="hover:text-[--primary-blue] transition-colors">Finanzas</a>
            </li>
            <li class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-1 text-gray-300"><polyline points="9 18 15 12 9 6"></polyline></svg>
                <span class="font-medium text-[--text-main]">Control Presupuestario</span>
            </li>
        </ol>
    </div>

    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-[26px] font-bold text-[--text-main] tracking-tight">Control Presupuestario</h1>
            <p class="text-[14px] text-[--text-muted] mt-1">Seguimiento de ejecución real vs programación de metas versionadas.</p>
        </div>
        
        <!-- Filtros Rápidos -->
        <form action="{{ route('finance.control') }}" method="GET" class="flex items-center gap-3 bg-white p-2 rounded-xl border border-[--border-soft] shadow-sm">
            <select name="plan_id" onchange="this.form.submit()" class="text-[12px] font-bold text-[--text-main] bg-gray-50 border-none rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-[--primary-blue]/20">
                @foreach($planes as $p)
                    <option value="{{ $p->id }}" {{ $selectedPlan->id == $p->id ? 'selected' : '' }}>
                        {{ $p->nombre }} (v{{ $p->version }}) - {{ strtoupper($p->estado) }}
                    </option>
                @endforeach
            </select>

            <select name="mes" onchange="this.form.submit()" class="text-[12px] font-bold text-[--text-main] bg-gray-50 border-none rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-[--primary-blue]/20">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $mesCorte == $m ? 'selected' : '' }}>
                        {{ date("F", mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>

            <select name="centro_costo_id" onchange="this.form.submit()" class="text-[12px] font-bold text-[--text-main] bg-gray-50 border-none rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-[--primary-blue]/20">
                <option value="">Todos los CC</option>
                @foreach($centrosCosto as $cc)
                    <option value="{{ $cc->id }}" {{ $centroCostoId == $cc->id ? 'selected' : '' }}>{{ $cc->nombre }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- Métricas Principales (Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card bg-white p-6 border border-[--border-soft] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-[--primary-blue]/5 rounded-lg text-[--primary-blue]">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider">Programado Anual</span>
        </div>
        <div class="text-[22px] font-bold text-[--text-main]">${{ number_format($metrics['anual_total'], 0, ',', '.') }}</div>
        <div class="text-[11px] text-[--text-muted] mt-1">Presupuesto total asignado año {{ $anio }}</div>
    </div>

    <div class="card bg-white p-6 border border-[--border-soft] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-[--warning]/5 rounded-lg text-[--warning]">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v10l4.5 4.5"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <span class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider">Programado Acumulado</span>
        </div>
        <div class="text-[22px] font-bold text-[--text-main]">${{ number_format($metrics['progr_acum'], 0, ',', '.') }}</div>
        <div class="text-[11px] text-[--text-muted] mt-1">Meta a {{ date("F", mktime(0, 0, 0, $mesCorte, 10)) }}</div>
    </div>

    <div class="card bg-white p-6 border border-[--border-soft] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-[--success]/5 rounded-lg text-[--success]">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <span class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider">Ejecutado Real</span>
        </div>
        <div class="text-[22px] font-bold text-[--text-main]">${{ number_format($metrics['exec_acum'], 0, ',', '.') }}</div>
        <div class="flex items-center gap-1.5 mt-1">
            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="h-full bg-[--success]" style="width: {{ min($metrics['porcentaje'], 100) }}%"></div>
            </div>
            <span class="text-[11px] font-bold text-[--success]">{{ number_format($metrics['porcentaje'], 1) }}%</span>
        </div>
    </div>

    <div class="card bg-white p-6 border border-[--border-soft] shadow-sm">
        @php $isOver = $metrics['desviacion'] > 0; @endphp
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 {{ $isOver ? 'bg-[--danger]/5 text-[--danger]' : 'bg-[--success]/5 text-[--success]' }} rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <span class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider">Desviación Acumulada</span>
        </div>
        <div class="text-[22px] font-bold {{ $isOver ? 'text-[--danger]' : 'text-[--success]' }}">
            {{ $isOver ? '+' : '' }}${{ number_format($metrics['desviacion'], 0, ',', '.') }}
        </div>
        <div class="text-[11px] text-[--text-muted] mt-1">{{ $isOver ? 'Sobre-ejecución detectada' : 'Bajo-ejecución controlada' }}</div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Tabla Jerárquica -->
    <div class="xl:col-span-2">
        <div class="card bg-white overflow-hidden p-0 border border-[--border-soft] shadow-sm">
            <div class="px-6 py-4 border-b border-[--border-soft] bg-gray-50/30 flex justify-between items-center">
                <h3 class="text-[15px] font-bold text-[--text-main]">Desglose por Clasificador / Ítem</h3>
                <span class="text-[11px] font-medium text-[--text-muted]">Versión Seleccionada: <b>v{{ $selectedPlan->version }}</b></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 text-[--text-muted] uppercase text-[10px] font-bold tracking-wider border-b border-[--border-soft]">
                            <th class="px-6 py-4">Clasificador</th>
                            <th class="px-6 py-4 text-right">Progr. Anual</th>
                            <th class="px-6 py-4 text-right">Progr. Acum.</th>
                            <th class="px-6 py-4 text-right">Ejec. Real</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[--border-soft]">
                        @foreach($tree as $node)
                            @php 
                                $desvPer = $node->programado_acumulado > 0 ? (($node->ejecutado_acumulado - $node->programado_acumulado) / $node->programado_acumulado) * 100 : 0;
                                $statusClass = $desvPer > 15 ? 'bg-[--danger]/15 text-[--danger]' : ($desvPer > 5 ? 'bg-[--warning]/15 text-[--warning]' : 'bg-[--success]/15 text-[--success]');
                                $statusDot = $desvPer > 15 ? 'bg-[--danger]' : ($desvPer > 5 ? 'bg-[--warning]' : 'bg-[--success]');
                                $indent = ($node->nivel - 1) * 1.5;
                                $isHidden = $node->nivel > 1;
                                $hasChildren = $node->children_count > 0;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition tree-row {{ $hasChildren ? 'font-bold bg-gray-50/20' : '' }}" 
                                data-id="{{ $node->id }}" 
                                data-parent="{{ $node->parent_id ?? '' }}"
                                style="{{ $isHidden ? 'display: none;' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-2" style="padding-left: {{ $indent }}rem;">
                                        @if($hasChildren)
                                            <div class="toggle-container cursor-pointer p-1 -m-1" data-node-id="{{ $node->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" 
                                                    class="mt-0.5 text-[--primary-blue] toggle-icon transition-transform duration-200">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-[14px] h-[14px]"></div> <!-- Espaciador para ítems hoja -->
                                        @endif
                                        <div>
                                            <div class="text-[13px] text-[--text-main]">{{ $node->codigo }}</div>
                                            <div class="text-[10px] text-[--text-muted] font-medium">{{ $node->denominacion }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-[13px] text-[--text-main]">${{ number_format($node->programado_anual, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-[13px] text-[--text-main]">${{ number_format($node->programado_acumulado, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-[13px] font-bold text-[--text-main]">${{ number_format($node->ejecutado_acumulado, 0, ',', '.') }}</div>
                                    <div class="text-[9px] font-bold {{ $desvPer > 0 ? 'text-[--danger]' : 'text-[--success]' }}">
                                        {{ $desvPer > 0 ? '+' : '' }}{{ number_format($desvPer, 1) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider flex items-center gap-1.5 {{ $statusClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                            {{ $desvPer > 15 ? 'Crítico' : ($desvPer > 5 ? 'Alerta' : 'OK') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráficos y Rankings -->
    <div class="flex flex-col gap-8">
        <div class="card bg-white p-6 border border-[--border-soft] shadow-sm">
            <h3 class="text-[14px] font-bold text-[--text-main] uppercase tracking-wider mb-6">Tendencia Acumulada</h3>
            <div class="h-[200px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="card bg-white p-6 border border-[--border-soft] shadow-sm">
            <h3 class="text-[14px] font-bold text-[--text-main] uppercase tracking-wider mb-6">Top Desviaciones por Ítem</h3>
            <div class="space-y-4">
                @php 
                    $ranking = collect($tree)->where('nivel', 5)->sortByDesc(function($n) {
                        return $n->programado_acumulado > 0 ? ($n->ejecutado_acumulado - $n->programado_acumulado) / $n->programado_acumulado : 0;
                    })->take(5);
                @endphp

                @foreach($ranking as $rank)
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-[12px]">
                            <span class="font-bold text-[--text-main] truncate pr-4">{{ $rank->codigo }}</span>
                            @php $d = $rank->programado_acumulado > 0 ? (($rank->ejecutado_acumulado - $rank->programado_acumulado) / $rank->programado_acumulado) * 100 : 0; @endphp
                            <span class="font-bold text-[--danger]">+{{ number_format($d, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-[--danger] h-full rounded-full" style="width: {{ min($d, 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('tbody');
    
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            const toggle = e.target.closest('.toggle-container');
            if (!toggle) return;

            const nodeId = toggle.getAttribute('data-node-id');
            const icon = toggle.querySelector('.toggle-icon');
            const isExpanding = !icon.classList.contains('rotate-90');

            // Toggle Icon state
            icon.classList.toggle('rotate-90');

            // Toggle children
            toggleRecursive(nodeId, isExpanding);
        });
    }

    function toggleRecursive(parentId, show) {
        const children = document.querySelectorAll(`tr[data-parent="${parentId}"]`);
        
        children.forEach(child => {
            const childId = child.getAttribute('data-id');
            const childToggle = child.querySelector('.toggle-icon');
            
            if (show) {
                child.style.display = 'table-row';
                // Only show grandchildren if the child itself is expanded
                if (childToggle && childToggle.classList.contains('rotate-90')) {
                    toggleRecursive(childId, true);
                }
            } else {
                child.style.display = 'none';
                // Always hide descendants when parent is collapsed
                toggleRecursive(childId, false);
            }
        });
    }

    // Chart trend logic
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    const labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'].slice(0, {{ $mesCorte }});
    
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Programado',
                    data: labels.map((_, i) => {{ $metrics['progr_acum'] }} * (i+1) / {{ $mesCorte }}),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Ejecutado',
                    data: labels.map((_, i) => {{ $metrics['exec_acum'] }} * (i+1) / {{ $mesCorte }}),
                    borderColor: '#10b981',
                    borderWidth: 3,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { display: false },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#6b7280' }
                }
            }
        }
    });
});
</script>

<style>
:root {
    --primary-blue: #2563eb;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border-soft: #f3f4f6;
}
.card { border-radius: 1.25rem; }
.tree-row:hover { cursor: pointer; }
</style>
@endsection
