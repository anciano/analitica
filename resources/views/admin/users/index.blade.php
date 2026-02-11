@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-[24px] font-semibold text-[--text-main]">Gestión de Usuarios</h1>
            <p class="text-[14px] text-[--text-muted]">Administre los accesos y roles de los usuarios del sistema</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24" class="mr-2">
                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="19" y1="8" x2="19" y2="14"></line>
                <line x1="16" y1="11" x2="22" y2="11"></line>
            </svg>
            Nuevo Usuario
        </a>
    </div>

    <div class="card bg-white overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-[--text-muted] uppercase text-[11px] font-bold tracking-wider border-b border-[--border-soft]">
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Roles</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[--border-soft]">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-[14px] text-[--text-main] font-medium">{{ $user->name }}</div>
                                        <div class="text-[12px] text-[--text-muted] font-mono">{{ $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[14px] text-[--text-muted]">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($user->roles as $role)
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold border border-blue-100">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-[12px] text-gray-400 italic">Sin roles</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="text-[--primary-blue] hover:underline text-[13px] font-bold">Editar</a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-[--danger] hover:underline text-[13px] font-bold p-0 bg-transparent border-none">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection