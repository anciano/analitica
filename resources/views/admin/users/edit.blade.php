@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center gap-2 text-[12px] text-[--text-muted] mb-2">
            <a href="{{ route('admin.users.index') }}" class="hover:text-[--primary-blue]">Gestión de Usuarios</a>
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"></path></svg>
            <span class="font-medium text-[--text-main]">Editar Usuario</span>
        </div>
        <h1 class="text-[24px] font-semibold text-[--text-main]">Editar Usuario</h1>
        <p class="text-[14px] text-[--text-muted]">Actualice los datos o privilegios de <b>{{ $user->name }}</b></p>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="card space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                    @error('name') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Nombre de Usuario (Login)</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] font-mono focus:ring-2 focus:ring-blue-500/20 outline-none">
                    @error('username') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] focus:ring-2 focus:ring-blue-500/20 outline-none">
                    @error('email') <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2 p-4 bg-gray-50 rounded-lg border border-dashed border-[--border-soft]">
                    <p class="text-[12px] text-[--text-muted] mb-4">Complete estos campos solo si desea cambiar la contraseña del usuario.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Nueva Contraseña</label>
                            <input type="password" name="password" 
                                   class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] bg-white focus:ring-2 focus:ring-blue-500/20 outline-none">
                        </div>
                        <div>
                            <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-2">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" 
                                   class="w-full px-4 py-2 rounded-lg border border-[--border-soft] text-[14px] bg-white focus:ring-2 focus:ring-blue-500/20 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[--border-soft]">
                <label class="block text-[12px] font-bold text-[--text-main] uppercase tracking-wide mb-4">Roles de Acceso</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-[--border-soft] cursor-pointer hover:bg-gray-50/50 transition {{ $user->hasRole($role->slug) ? 'bg-blue-50/30 border-blue-100' : '' }}">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                   {{ $user->hasRole($role->slug) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <div>
                                <span class="block text-[13px] font-bold text-[--text-main]">{{ $role->name }}</span>
                                <span class="text-[11px] text-[--text-muted]">{{ $role->slug }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-6 flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 text-[14px] font-bold text-[--text-muted] hover:bg-gray-100 transition rounded-lg">Cancelar</a>
                <button type="submit" class="btn">Guardar Cambios</button>
            </div>
        </form>
    </div>
@endsection
