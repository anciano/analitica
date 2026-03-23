<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hospital Analitica') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
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
            width: 280px;
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
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1.125rem;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom subtle scrollbar */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .sidebar-nav:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(154, 164, 191, 0.4);
            margin-bottom: 0.5rem;
            margin-top: 1rem;
            padding-left: 0.75rem;
            letter-spacing: 0.05em;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            color: var(--sidebar-icon);
            text-decoration: none;
            font-size: 0.8125rem;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }

        /* Ensure containers don't flex siblings (vertical accordion) */
        div.nav-item {
            display: block !important;
            padding: 0 !important;
            background: transparent !important;
            margin-bottom: 0.5rem;
        }

        .nav-item i, .nav-item svg {
            flex-shrink: 0;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active:not(.has-submenu) {
            color: white;
            background: var(--sidebar-active);
        }

        .nav-item.has-submenu.active > .nav-link {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.625rem 1rem;
            color: var(--sidebar-icon);
            text-decoration: none;
            font-size: 0.8125rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.has-submenu .nav-submenu {
            display: none;
            padding: 0.25rem 0;
            margin-left: 1.25rem;
            margin-right: 0.5rem;
            margin-top: 0.125rem;
            margin-bottom: 0.5rem;
            border-left: 1px solid rgba(154, 164, 191, 0.1);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 0 0 8px 8px;
        }

        .nav-item.has-submenu.open .nav-submenu {
            display: block;
        }

        .nav-item.has-submenu.open .submenu-arrow {
            transform: rotate(180deg);
        }

        .nav-sub-item {
            display: block;
            padding: 0.5rem 0.875rem;
            color: var(--sidebar-icon);
            text-decoration: none;
            font-size: 0.75rem;
            transition: all 0.2s;
            border-radius: 6px;
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-sub-item:hover,
        .nav-sub-item.active {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Main Content area */
        .content-wrapper {
            flex: 1;
            padding-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
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

        /* Table Styles */
        .sing-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sing-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-soft);
            background: rgba(249, 250, 251, 0.5);
        }

        .sing-table td {
            padding: 16px;
            font-size: 14px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-soft);
            vertical-align: middle;
        }

        .sing-table tr:hover td {
            background-color: #F9FAFB;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Tom Select Overrides */
        .ts-control {
            border-radius: 8px !important;
            padding: 8px 12px !important;
            border: 1px solid var(--border-soft) !important;
            background-color: #F9FAFB !important;
            font-size: 14px !important;
            box-shadow: none !important;
        }

        .ts-wrapper.focus .ts-control {
            border-color: var(--primary-blue) !important;
            ring: 2px solid rgba(79, 141, 245, 0.2) !important;
            outline: none !important;
        }

        .ts-dropdown {
            border-radius: 8px !important;
            border: 1px solid var(--border-soft) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            margin-top: 4px !important;
            padding: 4px !important;
        }

        .ts-dropdown .active {
            background-color: var(--primary-blue) !important;
            color: white !important;
            border-radius: 4px !important;
        }

        .ts-dropdown .option {
            padding: 8px 12px !important;
            font-size: 14px !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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
            <div class="nav-group-label">Ejecución Presupuestaria</div>
            <div class="mb-2">
                <div class="nav-item has-submenu {{ request()->routeIs('finance.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link" onclick="toggleSubmenu(this)">
                        <div class="flex items-center gap-3">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 3v18h18"></path>
                                <path d="M18 9l-6 6-4-4-5 5"></path>
                            </svg>
                            <span>Finanzas</span>
                        </div>
                        <svg class="submenu-arrow transition-transform duration-200" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                    </a>
                    <div class="nav-submenu">
                        <a href="{{ route('finance.resumen') }}"
                            class="nav-sub-item {{ request()->routeIs('finance.resumen') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('finance.control') }}"
                            class="nav-sub-item {{ request()->routeIs('finance.control') ? 'active' : '' }}">Control Presupuestario</a>
                        <a href="{{ route('finance.tendencia') }}"
                            class="nav-sub-item {{ request()->routeIs('finance.tendencia') ? 'active' : '' }}">Tendencia Ejecución</a>
                    </div>
                </div>
            </div>

            <div class="nav-group-label">Gestión de Datos</div>
            <div class="mb-2">
            <div class="nav-item has-submenu {{ request()->routeIs('imports.*') || request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="javascript:void(0)" class="nav-link" onclick="toggleSubmenu(this)">
                    <div class="flex items-center gap-3">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Carga</span>
                    </div>
                    <svg class="submenu-arrow transition-transform duration-200" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="nav-submenu">
                    <a href="{{ route('dashboard') }}"
                        class="nav-sub-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Historial</a>
                    <a href="{{ route('imports.create') }}"
                        class="nav-sub-item {{ request()->routeIs('imports.create') ? 'active' : '' }}">Nuevos
                        Registros</a>
                </div>
            </div>
            </div>

            <div class="nav-group-label">Planificación</div>
            <div class="mb-2">
            <div class="nav-item has-submenu {{ request()->routeIs('programacion.*') ? 'active' : '' }}">
                <a href="javascript:void(0)" class="nav-link" onclick="toggleSubmenu(this)">
                    <div class="flex items-center gap-3">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Presupuesto</span>
                    </div>
                    <svg class="submenu-arrow transition-transform duration-200" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </a>
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
            </div>

            <div class="nav-group-label">GRD</div>
            <div class="mb-2">
            <div class="nav-item has-submenu {{ request()->routeIs('grd.*') ? 'active' : '' }}">
                <a href="javascript:void(0)" class="nav-link" onclick="toggleSubmenu(this)">
                    <div class="flex items-center gap-3">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                        <span>Egresos</span>
                    </div>
                    <svg class="submenu-arrow transition-transform duration-200" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="nav-submenu">
                    <a href="{{ route('grd.import.index') }}"
                        class="nav-sub-item {{ request()->routeIs('grd.import.index') ? 'active' : '' }}">Carga GRD</a>
                    <a href="{{ route('grd.import.create') }}"
                        class="nav-sub-item {{ request()->routeIs('grd.import.create') ? 'active' : '' }}">Nueva Carga</a>
                </div>
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

            <!-- ML / Predicción -->
            <div class="nav-group-label">Inteligencia Predictiva</div>
            <div class="mb-2">
                <div class="nav-item has-submenu {{ request()->is('ml*') ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link flex items-center justify-between" onclick="toggleSubmenu(this)">
                        <div class="flex items-center gap-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <span>Modelos ML</span>
                        </div>
                        <svg class="submenu-arrow transition-transform duration-200" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                    </a>
                    <div class="nav-submenu">
                        <a href="{{ route('ml.models.index') }}" 
                            class="nav-sub-item {{ request()->routeIs('ml.models.*') ? 'active' : '' }}">
                            Gobernanza de Modelos
                        </a>
                        <a href="{{ route('ml.test.index') }}" 
                            class="nav-sub-item {{ request()->routeIs('ml.test.*') ? 'active' : '' }}">
                            Prueba de Modelo
                        </a>
                    </div>
                </div>
            </div>

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
    <script>
        function toggleSubmenu(element) {
            const parent = element.closest('.nav-item');
            parent.classList.toggle('open');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto open active submenus
            document.querySelectorAll('.nav-sub-item.active').forEach(item => {
                const parent = item.closest('.nav-item');
                if (parent) {
                    parent.classList.add('open', 'active');
                }
            });
        });
    </script>
</body>

</html>