<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hospital Analitica') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#4F8DF5',
                        'primary-cyan': '#4FD1C5',
                        'primary-yellow': '#F6C85F',
                        'primary-red': '#F87171',
                        'bg-app': '#F5F7FA',
                        'bg-surface': '#FFFFFF',
                        'border-soft': '#E6ECF2',
                        'text-main': '#1F2937',
                        'text-muted': '#6B7280',
                        'sidebar-bg': '#0E1628',
                        'sidebar-icon': '#9AA4BF',
                        'sidebar-active': '#3B82F6',
                        'success': '#22C55E',
                        'warning': '#F59E0B',
                        'danger': '#EF4444',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-app: #F5F7FA;
            --bg-surface: #FFFFFF;
            --border-soft: #E6ECF2;
            --text-main: #1F2937;
            --text-muted: #6B7280;
            --sidebar-bg: #0E1628;
            --sidebar-icon: #9AA4BF;
            --sidebar-active: #3B82F6;
            --primary-blue: #4F8DF5;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-app);
            color: var(--text-main);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 240px;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1.25rem;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 1rem;
            overflow-y: auto;
        }

        .nav-group-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(154, 164, 191, 0.4);
            margin-bottom: 0.75rem;
            padding-left: 1rem;
            letter-spacing: 0.05em;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--sidebar-icon);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active {
            color: white;
            background: var(--sidebar-active);
        }

        .nav-submenu {
            margin-left: 2.75rem;
            margin-bottom: 0.75rem;
            padding-left: 0;
            border-left: 1px solid rgba(154, 164, 191, 0.1);
        }

        .nav-sub-item {
            display: block;
            padding: 0.5rem 1rem;
            color: var(--sidebar-icon);
            text-decoration: none;
            font-size: 0.8125rem;
            transition: color 0.2s;
        }

        .nav-sub-item:hover,
        .nav-sub-item.active {
            color: white;
        }

        /* Main Content area */
        .content-wrapper {
            flex: 1;
            margin-left: 240px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-bar {
            height: 64px;
            background: white;
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            color: var(--text-muted);
        }

        .main-container {
            flex: 1;
            padding: 2rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
        }

        .btn {
            background: var(--primary-blue);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <!-- Fixed Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                class="text-blue-500">
                <path d="M12 2v20M2 12h20M12 2l4 4-4 4-4-4 4-4z" />
            </svg>
            Analítica
        </a>

        <nav class="sidebar-nav">
            <div class="nav-group-label">General</div>
            <a href="{{ route('finance.resumen') }}"
                class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 3v18h18"></path>
                    <path d="M18 9l-6 6-4-4-5 5"></path>
                </svg>
                Finanzas
            </a>

            <div class="nav-group-label">Gestión de Datos</div>
            <div class="mb-2">
                <div
                    class="nav-item {{ request()->routeIs('imports.*') || request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Carga
                </div>
                <div class="nav-submenu">
                    <a href="{{ route('dashboard') }}"
                        class="nav-sub-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Historial</a>
                    <a href="{{ route('imports.create') }}"
                        class="nav-sub-item {{ request()->routeIs('imports.create') ? 'active' : '' }}">Nuevos
                        Registros</a>
                </div>
            </div>

            <div class="nav-group-label">Planificación</div>
            <div class="mb-2">
                <div class="nav-item {{ request()->routeIs('programacion.*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Presupuesto
                </div>
                <div class="nav-submenu">
                    <a href="{{ route('programacion.index') }}"
                        class="nav-sub-item {{ request()->routeIs('programacion.index') ? 'active' : '' }}">Planes
                        Anuales</a>
                    <a href="{{ route('programacion.clasificador.index') }}"
                        class="nav-sub-item {{ request()->routeIs('programacion.clasificador.*') ? 'active' : '' }}">Clasificador</a>
                    <a href="{{ route('programacion.centros-costo.index') }}"
                        class="nav-sub-item {{ request()->routeIs('programacion.centros-costo.*') ? 'active' : '' }}">Centros
                        de Costo</a>
                </div>
            </div>

            <a href="{{ route('finance.powerbi') }}" class="nav-item" style="color: var(--warning)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 20V10"></path>
                    <path d="M12 20V4"></path>
                    <path d="M6 20v-6"></path>
                </svg>
                Power BI
            </a>

            <div class="nav-group-label">Administración</div>
            <a href="{{ route('admin.users.index') }}"
                class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                </svg>
                Usuarios y Roles
            </a>
            @if(request()->routeIs('admin.*'))
                <div class="nav-submenu">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-sub-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Usuarios</a>
                    <a href="{{ route('admin.roles.index') }}"
                        class="nav-sub-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Roles</a>
                </div>
            @endif
        </nav>

        <div class="p-4 border-t border-white/5">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div
                    class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-[13px] font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-500 truncate">Administrador</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full py-2 rounded-lg border border-white/10 text-[12px] text-gray-400 hover:bg-white/5 hover:text-white transition">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <header class="top-bar">
            <div class="breadcrumbs">
                <span>Estás aquí</span>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"></path>
                </svg>
                <span class="text-[--text-main] font-medium">
                    @if(request()->routeIs('finance.*')) Finanzas
                    @elseif(request()->routeIs('admin.users.*')) Usuarios
                    @elseif(request()->routeIs('admin.roles.*')) Roles y Permisos
                    @elseif(request()->routeIs('programacion.*')) Programación
                    @elseif(request()->routeIs('imports.*') || request()->routeIs('dashboard')) Carga de Datos
                    @else Dashboard @endif
                </span>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-[12px] text-[--text-muted]">
                    {{ now()->translatedFormat('l, d \d\e F Y') }}
                </div>
            </div>
        </header>

        <main class="main-container">
            @if(session('success'))
                <div
                    class="mb-6 p-4 rounded-xl bg-[--success]/10 border border-[--success]/20 text-[--success] text-[13px] font-medium flex items-center">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        class="mr-2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div
                    class="mb-6 p-4 rounded-xl bg-[--danger]/10 border border-[--danger]/20 text-[--danger] text-[13px] font-medium flex items-center">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        class="mr-2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>