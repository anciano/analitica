@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Roles y Permisos</h1>
            <p class="text-[14px] text-[--text-muted]">Defina los perfiles de acceso y sus capacidades</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24" class="mr-2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            Nuevo Rol
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="card bg-white flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-[16px] font-bold text-[--text-main]">{{ $role->name }}</h3>
                        <code
                            class="text-[11px] text-[--primary-blue] bg-blue-50 px-1.5 py-0.5 rounded">{{ $role->slug }}</code>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                            class="p-1.5 hover:bg-gray-100 rounded-lg text-[--text-muted] transition">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                            onsubmit="return confirm('¿Está seguro de eliminar este rol?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-1.5 hover:bg-red-50 hover:text-red-500 rounded-lg text-[--text-muted] transition bg-transparent border-none">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex-1">
                    <p class="text-[12px] font-bold text-[--text-muted] uppercase tracking-wider mb-2">Permisos asignados:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($role->permissions as $permission)
                            <span
                                class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[11px] font-medium border border-gray-200">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-[12px] text-gray-400 italic">Sin permisos específicos</span>
                        @endforelse
                    </div>
                </div>

                <div
                    class="mt-6 pt-4 border-t border-[--border-soft] flex items-center justify-between text-[12px] text-[--text-muted]">
                    <span>{{ $role->users()->count() }} usuarios vinculados</span>
                </div>
            </div>
        @endforeach
    </div>
@endsection