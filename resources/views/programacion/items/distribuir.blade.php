@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
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
                    <a href="{{ route('programacion.planes.show', $item->plan_id) }}"
                        class="hover:text-[--primary-blue] transition-colors">{{ $item->plan->nombre }}</a>
                </li>
                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mx-1 text-gray-300">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span class="font-medium text-[--text-main]">Distribución Mensual</span>
                </li>
            </ol>
        </div>

        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-[24px] font-semibold text-[--text-main]">Distribución Mensual</h1>
                <p class="text-[14px] text-[--text-muted]">
                    {{ $item->clasificadorItem->codigo }} - {{ $item->clasificadorItem->denominacion }}
                    <span class="mx-2 text-gray-300">|</span>
                    {{ $item->centroCosto->nombre }}
                </p>
            </div>
            <div class="card bg-white p-4 flex flex-col items-end min-w-[200px]">
                <span class="text-[11px] text-[--text-muted] uppercase font-bold tracking-wider mb-1">Monto Anual a
                    Distribuir</span>
                <span
                    class="text-[22px] font-bold text-[--primary-blue]">${{ number_format($item->monto_anual, 0, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('programacion.planes.items.save-distribuir', $item->id) }}" method="POST"
            id="distribucionForm">
            @csrf

            <div class="card bg-white mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-[16px] font-semibold text-[--text-main]">Planificación por Mes</h2>
                    <button type="button" onclick="equidistribuir()"
                        class="text-[13px] font-medium text-[--primary-blue] hover:underline flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M2 12h20" />
                            <rect x="2" y="6" width="20" height="12" rx="2" />
                        </svg>
                        Distribución Automática (12 meses)
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @php
                        $mesesNom = [
                            1 => 'Enero',
                            2 => 'Febrero',
                            3 => 'Marzo',
                            4 => 'Abril',
                            5 => 'Mayo',
                            6 => 'Junio',
                            7 => 'Julio',
                            8 => 'Agosto',
                            9 => 'Septiembre',
                            10 => 'Octubre',
                            11 => 'Noviembre',
                            12 => 'Diciembre'
                        ];
                    @endphp

                    @foreach($mesesNom as $num => $nombre)
                        <div class="space-y-2">
                            <label for="mes_{{ $num }}"
                                class="block text-[12px] font-bold text-[--text-muted] uppercase tracking-wide">{{ $nombre }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[--text-muted] text-[14px]">$</span>
                                <input type="number" id="mes_{{ $num }}" name="meses[{{ $num }}]"
                                    value="{{ old('meses.' . $num, $mensualizaciones[$num] ?? 0) }}"
                                    class="mes-input w-full pl-7 pr-3 py-2 bg-gray-50 border border-[--border-soft] rounded-lg text-[14px] text-[--text-main] focus:ring-2 focus:ring-[--primary-blue]/20 focus:border-[--primary-blue] transition-all"
                                    required min="0" oninput="updateCalculos()">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Barra de Estado Bottom -->
            <div class="card bg-gray-50 border-t-2 border-[--primary-blue] sticky bottom-4 shadow-lg">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex gap-8">
                        <div>
                            <span class="block text-[11px] text-[--text-muted] font-bold uppercase mb-1">Total
                                Distribuido</span>
                            <span id="totalDistribuido" class="text-[18px] font-bold text-[--text-main]">$0</span>
                        </div>
                        <div>
                            <span class="block text-[11px] text-[--text-muted] font-bold uppercase mb-1">Diferencia</span>
                            <span id="diferencia" class="text-[18px] font-bold">$0</span>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('programacion.planes.show', $item->plan_id) }}"
                            class="text-[14px] font-medium text-[--text-muted] hover:text-[--text-main] px-4 py-2">
                            Cancelar
                        </a>
                        <button type="submit" id="btnGuardar"
                            class="btn px-8 disabled:opacity-50 disabled:cursor-not-allowed">
                            Guardar Distribución
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const MONTO_TOTAL_ANUAL = {{ $item->monto_anual }};

        function updateCalculos() {
            let total = 0;
            document.querySelectorAll('.mes-input').forEach(input => {
                total += parseFloat(input.value || 0);
            });

            const diferencia = MONTO_TOTAL_ANUAL - total;

            document.getElementById('totalDistribuido').innerText = '$' + new Intl.NumberFormat('es-CL').format(total);

            const diffElement = document.getElementById('diferencia');
            diffElement.innerText = '$' + new Intl.NumberFormat('es-CL').format(diferencia);

            if (Math.abs(diferencia) < 0.01) {
                diffElement.className = 'text-[18px] font-bold text-[--success]';
                document.getElementById('btnGuardar').disabled = false;
            } else {
                diffElement.className = 'text-[18px] font-bold text-[--danger]';
                document.getElementById('btnGuardar').disabled = true;
            }
        }

        function equidistribuir() {
            const porMes = Math.floor(MONTO_TOTAL_ANUAL / 12);
            const resto = MONTO_TOTAL_ANUAL % 12;

            document.querySelectorAll('.mes-input').forEach((input, index) => {
                // Ponemos el resto en el primer mes o lo que sobre
                if (index === 0) {
                    input.value = porMes + resto;
                } else {
                    input.value = porMes;
                }
            });
            updateCalculos();
        }

        // Inicializar
        updateCalculos();
    </script>
@endsection