@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2 text-[12px] text-[--text-muted]">
                <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
                <span class="mx-2">/</span>
                <span class="font-medium text-[--text-main]">Clasificador Presupuestario</span>
            </div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Mantenedor de Clasificador</h1>
            <p class="text-[14px] text-[--text-muted]">Gestión de la estructura jerárquica de ingresos y gastos</p>
        </div>
        <a href="{{ route('programacion.clasificador.create') }}" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Ítem
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card bg-white mb-6">
        <form action="{{ route('programacion.clasificador.index') }}" method="GET"
            class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por código o denominación..."
                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue]">
            </div>
            <div class="w-40">
                <select name="nivel"
                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue]">
                    <option value="">Todos los niveles</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('nivel') == $i ? 'selected' : '' }}>Nivel {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-40">
                <select name="anio"
                    class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:outline-none focus:border-[--primary-blue]">
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}" {{ request('anio', 2026) == $anio ? 'selected' : '' }}>Año {{ $anio }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="bg-[--primary-blue] text-white px-6 py-2 rounded-lg text-[14px] font-semibold hover:opacity-90 transition">
                Filtrar
            </button>
            @if(request()->anyFilled(['search', 'nivel', 'anio']))
                <a href="{{ route('programacion.clasificador.index') }}"
                    class="text-[13px] text-[--text-muted] hover:underline">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Listado -->
    <div class="card bg-white overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-[--text-muted] uppercase text-[11px] font-bold tracking-wider border-b border-[--border-soft]">
                        <th class="px-6 py-4">Código</th>
                        <th class="px-6 py-4">Denominación</th>
                        <th class="px-6 py-4">Nivel</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[--border-soft]">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-mono text-[13px] font-bold text-[--text-main]">
                                {{ $item->codigo }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[14px] text-[--text-main] font-medium">{{ $item->denominacion }}</div>
                                @if($item->parent)
                                    <div class="text-[11px] text-[--text-muted]">Depende de: {{ $item->parent->codigo }} -
                                        {{ Str::limit($item->parent->denominacion, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600">
                                    Nivel {{ $item->nivel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                            {{ $item->activo ? 'bg-[--success]/10 text-[--success]' : 'bg-[--danger]/10 text-[--danger]' }}">
                                    {{ $item->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('programacion.clasificador.edit', $item->id) }}"
                                        class="text-[--primary-blue] hover:underline text-[13px] font-bold">Editar</a>
                                    <form action="{{ route('programacion.clasificador.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('¿Está seguro de desactivar este ítem?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-[--danger] hover:underline bg-transparent !p-0 !border-none cursor-pointer text-[13px] font-bold">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[--text-muted] italic">No se encontraron ítems
                                con los filtros aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[--border-soft]">
            {{ $items->links() }}
        </div>
    </div>
@endsection