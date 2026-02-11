@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2 text-[12px] text-[--text-muted]">
                <a href="{{ route('programacion.index') }}" class="hover:text-[--primary-blue]">Programación</a>
                <span class="mx-2">/</span>
                <span class="font-medium text-[--text-main]">Centros de Costo</span>
            </div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Mantenedor de Centros de Costo</h1>
            <p class="text-[14px] text-[--text-muted]">Gestión de la estructura organizacional y niveles de dependencia</p>
        </div>
        <a href="{{ route('programacion.centros-costo.create') }}" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Centro de Costo
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-6 bg-gray-50/50">
        <form action="{{ route('programacion.centros-costo.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Buscar por nombre o código..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-[--primary-blue]/20 outline-none">
                    <svg class="absolute left-3 top-2.5 text-gray-400" width="18" height="18" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg>
                </div>
            </div>
            <select name="nivel" onchange="this.form.submit()"
                class="px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] outline-none">
                <option value="">Todos los niveles</option>
                @foreach([1,2,3,4,5] as $n)
                    <option value="{{ $n }}" {{ request('nivel') == $n ? 'selected' : '' }}>Nivel {{ $n }}</option>
                @endforeach
            </select>
            <select name="activo" onchange="this.form.submit()"
                class="px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] outline-none">
                <option value="">Todos los estados</option>
                <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
            </select>
            @if(request()->anyFilled(['q', 'nivel', 'activo']))
                <a href="{{ route('programacion.centros-costo.index') }}" class="px-4 py-2 text-[14px] text-[--primary-blue] hover:underline flex items-center">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <div class="card bg-white overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-[--text-muted] uppercase text-[11px] font-bold tracking-wider border-b border-[--border-soft]">
                        <th class="px-6 py-4">Código</th>
                        <th class="px-6 py-4">Centro de Costo / Jerarquía</th>
                        <th class="px-6 py-4">Nivel</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[--border-soft]">
                    @forelse($centros as $centro)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-mono text-[13px] font-bold text-[--text-main]">
                                {{ $centro->codigo }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center" style="margin-left: {{ ($centro->nivel - 1) * 24 }}px">
                                    @if($centro->nivel > 1)
                                        <svg class="text-gray-300 mr-2" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                    <div>
                                        <div class="text-[14px] text-[--text-main] font-medium">{{ $centro->nombre }}</div>
                                        @if($centro->parent)
                                            <div class="text-[11px] text-[--text-muted]">Depende de: {{ $centro->parent->nombre }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge" style="background: #f3f4f6; color: #4b5563; font-size: 11px;">Nivel {{ $centro->nivel }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide
                                            {{ $centro->activo ? 'bg-[--success]/10 text-[--success]' : 'bg-[--danger]/10 text-[--danger]' }}">
                                    {{ $centro->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('programacion.centros-costo.edit', $centro->id) }}" 
                                       class="text-[--primary-blue] hover:underline text-[13px] font-bold">Editar</a>
                                    
                                    <form action="{{ route('programacion.centros-costo.destroy', $centro->id) }}" method="POST" onsubmit="return confirm('¿Eliminar o desactivar este centro de costo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[--danger] hover:underline text-[13px] font-bold">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[--text-muted] italic">No se encontraron centros de costo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection