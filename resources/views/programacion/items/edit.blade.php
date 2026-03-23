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
                    <span class="font-medium text-[--text-main]">Editar Asignación</span>
                </li>
            </ol>
        </div>
        <h1 class="text-[26px] font-bold text-[--text-main] tracking-tight">Editar Asignación Presupuestaria</h1>
        <p class="text-[14px] text-[--text-muted] mt-1">{{ $item->clasificadorItem->codigo }} - {{ $item->clasificadorItem->denominacion }}</p>
    </div>

    <div class="">
        <form action="{{ route('programacion.planes.items.update', $item->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            @method('PUT')

            <!-- Left Column: Total & Info -->
            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="card p-6 bg-white border border-[--border-soft] shadow-sm">
                    <h3 class="text-[14px] font-bold text-[--text-main] uppercase tracking-wider mb-4">Información Base</h3>
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <label class="text-[10px] font-bold text-[--text-muted] uppercase tracking-wider block mb-1">Centro de Costo</label>
                            <div class="text-[12px] font-bold text-[--text-main]">{{ $item->centroCosto->nombre }}</div>
                        </div>
                        
                        <div>
                            <label for="monto_anual" class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Monto Anual Total ($)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                <input type="number" name="monto_anual" id="monto_anual" 
                                    value="{{ old('monto_anual', (int)$item->monto_anual) }}"
                                    class="w-full pl-8 pr-4 py-3 rounded-xl border border-[--border-soft] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] outline-none transition-all font-bold text-[18px]"
                                    placeholder="0" required min="0">
                            </div>
                            @error('monto_anual')
                                <p class="text-[--danger] text-[11px] mt-2 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="button" id="btn_distribuir_auto"
                            class="w-full py-2.5 px-4 rounded-xl border border-[--primary-blue] text-[--primary-blue] text-[11px] font-bold uppercase tracking-wide hover:bg-[--primary-blue]/5 transition-all flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v10l4.5 4.5"/><circle cx="12" cy="12" r="10"/></svg>
                            Auto-distribuir (1/12)
                        </button>
                    </div>
                </div>

                <!-- Status Card -->
                <div id="status_card" class="card p-6 bg-white border border-[--border-soft] shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[14px] font-bold text-[--text-main] uppercase tracking-wider">Validación</h3>
                        <span id="validation_icon" class="text-[--success]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-[13px]">
                            <span class="text-[--text-muted]">Suma Meses:</span>
                            <span id="sum_display" class="font-bold text-[--text-main]">$0</span>
                        </div>
                        <div class="flex justify-between text-[13px]">
                            <span class="text-[--text-muted]">Diferencia:</span>
                            <span id="diff_display" class="font-bold text-[--success]">$0</span>
                        </div>
                        <div id="diff_bar" class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div id="diff_progress" class="h-full bg-[--success] transition-all duration-300" style="width: 100%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" id="btn_submit"
                        class="w-full bg-[--primary-blue] text-white py-4 rounded-xl text-[13px] font-bold uppercase tracking-wide hover:opacity-90 transition-all shadow-lg shadow-[--primary-blue]/20 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('programacion.planes.show', $item->plan_id) }}" 
                        class="w-full py-3 text-center text-[12px] font-bold text-[--text-muted] uppercase tracking-wide hover:text-[--text-main] transition-colors">
                        Cancelar y Volver
                    </a>
                </div>
            </div>

            <!-- Right Column: Monthly Distribution Grid -->
            <div class="lg:col-span-2">
                <div class="card p-8 bg-white border border-[--border-soft] shadow-sm h-full">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-[18px] font-bold text-[--text-main]">Distribución Mensual</h2>
                        <span class="text-[11px] font-bold text-[--text-muted] uppercase tracking-widest bg-gray-100 px-3 py-1 rounded-full">Año {{ $item->plan->anio }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            $months = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                        @endphp

                        @foreach($months as $num => $month)
                            <div class="form-group flex flex-col gap-2">
                                <label for="mes_{{ $num }}" class="text-[11px] font-bold text-[--text-muted] uppercase tracking-wider pl-1">{{ $month }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[12px]">$</span>
                                    <input type="number" 
                                           name="meses[{{ $num }}]" 
                                           id="mes_{{ $num }}" 
                                           value="{{ old('meses.' . $num, (int)($mensualizaciones[$num] ?? 0)) }}"
                                           class="month-input w-full pl-6 pr-3 py-2.5 rounded-xl border border-[--border-soft] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] outline-none transition-all text-[14px] font-semibold"
                                           placeholder="0" required min="0">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('monto_anual');
    const monthInputs = document.querySelectorAll('.month-input');
    const sumDisplay = document.getElementById('sum_display');
    const diffDisplay = document.getElementById('diff_display');
    const statusCard = document.getElementById('status_card');
    const validationIcon = document.getElementById('validation_icon');
    const btnSubmit = document.getElementById('btn_submit');
    const btnAutoDist = document.getElementById('btn_distribuir_auto');
    const diffProgress = document.getElementById('diff_progress');

    function updateCalculations() {
        const totalTarget = parseFloat(totalInput.value) || 0;
        let currentSum = 0;
        
        monthInputs.forEach(input => {
            currentSum += parseFloat(input.value) || 0;
        });

        const diff = currentSum - totalTarget;
        
        // Formatear moneda
        const formatter = new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP',
            minimumFractionDigits: 0
        });

        sumDisplay.textContent = formatter.format(currentSum);
        diffDisplay.textContent = formatter.format(Math.abs(diff));
        
        if (Math.abs(diff) < 1.1) {
            diffDisplay.className = "font-bold text-[--success]";
            statusCard.classList.remove('border-[--danger]/20');
            statusCard.classList.add('border-[--success]/20');
            validationIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[--success]"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = "1";
            diffProgress.style.backgroundColor = "var(--success)";
            diffProgress.style.width = "100%";
        } else {
            diffDisplay.className = "font-bold text-[--danger]";
            statusCard.classList.remove('border-[--success]/20');
            statusCard.classList.add('border-[--danger]/20');
            validationIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[--danger]"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = "0.5";
            diffProgress.style.backgroundColor = "var(--danger)";
            const progress = totalTarget > 0 ? Math.min((currentSum / totalTarget) * 100, 100) : 0;
            diffProgress.style.width = progress + "%";
        }
    }

    monthInputs.forEach(input => {
        input.addEventListener('input', updateCalculations);
    });

    totalInput.addEventListener('input', updateCalculations);

    btnAutoDist.addEventListener('click', function() {
        const total = parseFloat(totalInput.value) || 0;
        const base = Math.floor(total / 12);
        const remainder = total % 12;

        monthInputs.forEach((input, index) => {
            const mNum = index + 1;
            let val = base;
            if (mNum <= remainder) val += 1;
            input.value = val;
        });
        
        updateCalculations();
    });

    // Iniciana
    updateCalculations();
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
.card {
    border-radius: 1.25rem;
}
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
</style>
@endsection
