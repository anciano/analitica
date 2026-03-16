@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-[--text-main]">Prueba de Modelos Activos</h1>
    <p class="text-[--text-muted] text-sm mt-1">Simula el envío de datos de un paciente para probar la respuesta del motor predictivo.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Formulario de Entrada -->
    <div class="bg-white rounded-xl border border-[--border-color] p-6 shadow-sm">
        <h3 class="font-semibold text-[--text-main] mb-6 flex items-center gap-2">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Datos del Paciente
        </h3>
        
        <form id="predictionForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[--text-muted] uppercase mb-1">Sexo</label>
                    <select name="sexo" class="w-full bg-[--bg-main] border border-[--border-color] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="Mujer">Mujer</option>
                        <option value="Hombre">Hombre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[--text-muted] uppercase mb-1">Edad</label>
                    <input type="number" name="edad" value="65" class="w-full bg-[--bg-main] border border-[--border-color] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[--text-muted] uppercase mb-1">Diagnóstico Principal (CIE-10)</label>
                <input type="text" name="diagnostico_principal" value="J13" placeholder="Ej: J13" class="w-full bg-[--bg-main] border border-[--border-color] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[--text-muted] uppercase mb-1">Diagnósticos Secundarios (Separados por coma)</label>
                <input type="text" name="diagnosticos_secundarios" value="I10, E11.9" placeholder="Ej: I10, E11.9" class="w-full bg-[--bg-main] border border-[--border-color] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[--text-muted] uppercase mb-1">Procedimiento Principal (CIE-9)</label>
                <input type="text" name="procedimiento_principal" value="93.96" placeholder="Ej: 93.96" class="w-full bg-[--bg-main] border border-[--border-color] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[--text-muted] uppercase mb-1">Procedimientos Secundarios</label>
                <input type="text" name="procedimientos_secundarios" value="99.21" placeholder="Ej: 99.21" class="w-full bg-[--bg-main] border border-[--border-color] rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 mt-6">
                <span>Ejecutar Predicción</span>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </button>
        </form>
    </div>

    <!-- Resultados -->
    <div class="space-y-6">
        <div id="resultCard" class="bg-white rounded-xl border border-[--border-color] p-6 shadow-sm hidden">
            <h3 class="font-semibold text-[--text-main] mb-6 flex items-center gap-2">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Respuesta del Motor
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- GRD Probable -->
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="text-[10px] uppercase font-bold text-blue-600 mb-1">GRD Probable</div>
                    <div id="res_grd_code" class="text-lg font-bold text-blue-900">-</div>
                    <div id="res_grd_desc" class="text-xs text-blue-700 line-clamp-1">-</div>
                    <div id="res_grd_conf_container" class="mt-2 h-1.5 w-full bg-blue-200 rounded-full overflow-hidden">
                        <div id="res_grd_conf_bar" class="h-full bg-blue-600" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Estancia Esperada -->
                <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="text-[10px] uppercase font-bold text-green-600 mb-1">Estancia Esperada</div>
                    <div id="res_estancia" class="text-lg font-bold text-green-900">- días</div>
                    <div id="res_estancia_rango" class="text-xs text-green-700">-</div>
                </div>

                <!-- Peso Estimado -->
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="text-[10px] uppercase font-bold text-purple-600 mb-1">Peso Estimado</div>
                    <div id="res_peso" class="text-lg font-bold text-purple-900">-</div>
                    <div id="res_peso_rango" class="text-xs text-purple-700">-</div>
                </div>
            </div>

            <!-- Valorización Estimada -->
            <div class="mb-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-amber-600 mb-1">Valorización Estimada</div>
                        <div id="res_val_total" class="text-2xl font-bold text-amber-900">$ 0</div>
                        <div id="res_val_rango" class="text-xs text-amber-700">Rango: $ 0 - $ 0</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] uppercase font-bold text-amber-600 mb-1">Precio Base HRC</div>
                        <div id="res_val_base" class="text-sm font-bold text-amber-800">$ 0</div>
                    </div>
                </div>
                <div class="pt-3 border-t border-amber-200 grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-amber-600">Valor Día (Sugerido)</div>
                        <div id="res_val_dia" class="text-base font-bold text-amber-900">$ 0 / día</div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-amber-600">Rango Diario</div>
                        <div id="res_val_dia_rango" class="text-sm text-amber-700 font-medium">$ 0 - $ 0</div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border border-[--border-color]">
                <div class="text-[10px] uppercase font-bold text-[--text-muted] mb-3">Respuesta JSON (Raw)</div>
                <pre id="res_json" class="text-[10px] text-gray-700 font-mono overflow-auto max-h-48"></pre>
            </div>

            <div id="res_versions" class="mt-4 flex flex-wrap gap-2">
                <!-- Modelo tags here -->
            </div>
        </div>

        <div id="emptyState" class="bg-gray-50 rounded-xl border-2 border-dashed border-[--border-color] p-12 text-center">
            <svg class="mx-auto text-gray-300 mb-4" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.394l-1.154.91a2 2 0 01-3.145-1.17l-.145-1.17a6 6 0 00-6 6v.5a.5.5 0 01-.5.5h-1a.5.5 0 01-.5-.5v-.5a6 6 0 016-6h.5a.5.5 0 01.5.5v1a.5.5 0 01-.5.5h-.5a6 6 0 00-6 6v.5a.5.5 0 01-.5.5h-1a.5.5 0 01-.5-.5v-.5a6 6 0 016-6h.5a.5.5 0 01.5.5v1z"/></svg>
            <p class="text-[--text-muted] text-sm italic">Configura los datos y presiona "Ejecutar Predicción" para ver los resultados.</p>
        </div>
    </div>
</div>

<script>
const formatCLP = new Intl.NumberFormat('es-CL', {
    style: 'currency',
    currency: 'CLP',
    minimumFractionDigits: 0
});

document.getElementById('predictionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    const originalText = btn.innerHTML;
    
    try {
        btn.disabled = true;
        btn.innerHTML = '<span>Procesando...</span>';
        
        const formData = new FormData(e.target);
        const response = await fetch('{{ route("ml.test.predict") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        // UI Updates
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('resultCard').classList.remove('hidden');
        
        // 1. GRD
        document.getElementById('res_grd_code').innerText = data.grd_probable.codigo;
        document.getElementById('res_grd_desc').innerText = data.grd_probable.descripcion;
        const confidencePercent = (data.grd_probable.confianza * 100).toFixed(0);
        document.getElementById('res_grd_conf_bar').style.width = confidencePercent + '%';
        
        // 2. Estancia
        document.getElementById('res_estancia').innerText = data.estancia_estimada.esperada_dias + ' días';
        document.getElementById('res_estancia_rango').innerText = 'Rango: ' + data.estancia_estimada.rango_dias.join('-') + ' días';
        
        // 3. Peso
        document.getElementById('res_peso').innerText = data.peso_estimado.valor;
        document.getElementById('res_peso_rango').innerText = 'Rango: ' + data.peso_estimado.rango.join(' - ');
        
        // 4. Valorización
        const val = data.valorizacion_estimada;
        document.getElementById('res_val_total').innerText = formatCLP.format(val.monto_total);
        document.getElementById('res_val_rango').innerText = `Rango: ${formatCLP.format(val.monto_rango[0])} - ${formatCLP.format(val.monto_rango[1])}`;
        document.getElementById('res_val_base').innerText = formatCLP.format(val.precio_base_grd_hospital);
        document.getElementById('res_val_dia').innerText = formatCLP.format(val.valor_dia) + ' / día';
        document.getElementById('res_val_dia_rango').innerText = `${formatCLP.format(val.valor_dia_rango[0])} - ${formatCLP.format(val.valor_dia_rango[1])}`;

        document.getElementById('res_json').innerText = JSON.stringify(data, null, 2);
        
        const versionsEl = document.getElementById('res_versions');
        versionsEl.innerHTML = '';
        Object.entries(data.modelos_utilizados).forEach(([key, val]) => {
            const span = document.createElement('span');
            span.className = 'px-2 py-0.5 bg-gray-100 text-[10px] font-bold text-gray-600 rounded border border-gray-200 uppercase';
            span.innerText = key + ': ' + val;
            versionsEl.appendChild(span);
        });

    } catch (error) {
        console.error(error);
        alert('Error al ejecutar predicción');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>
@endsection
