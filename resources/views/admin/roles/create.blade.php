@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center gap-2 text-[12px] text-[--text-muted] mb-2">
            <a href="{{ route('admin.roles.index') }}" class="hover:text-[--primary-blue]">Roles y Permisos</a>
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"></path>
            </svg>
            <span class="font-medium text-[--text-main]">Nuevo Rol</span>
        </div>
        <h1 class="text-[24px] font-semibold text-[--text-main]">Nuevo Rol</h1>
        <p class="text-[14px] text-[--text-muted]">Defina un nuevo perfil de acceso al sistema</p>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="card space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Nombre del
                        Rol</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej: Auditor Interno"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                    @error('name') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Identificador
                        (Slug)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="ej: auditor-interno"
                        class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] font-mono focus:ring-2 focus:ring-blue-500/20 outline-none">
                    @error('slug') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-[--border-soft]">
                <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-4">Capacidades
                    (Permisos)</label>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($permissions as $permission)
                        <label
                            class="flex items-center gap-3 p-3 rounded-lg border border-[--border-soft] cursor-pointer hover:bg-gray-50/50 transition">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <div>
                                <span class="block text-[13px] font-bold text-[--text-main]">{{ $permission->name }}</span>
                                <span class="text-[11px] text-[--text-muted] font-mono">{{ $permission->slug }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-6 flex justify-end gap-3">
                <a href="{{ route('admin.roles.index') }}"
                    class="px-6 py-2 text-[14px] font-bold text-[--text-muted] hover:bg-gray-100 transition rounded-lg">Cancelar</a>
                <button type="submit" class="btn">Guardar Rol</button>
            </div>
        </form>
    </div>
@endsection