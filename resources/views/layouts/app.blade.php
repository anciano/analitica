<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hospital Analitica') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --bg: #0f172a;
            --surface: rgba(30, 41, 59, 0.5);
            --border: rgba(255, 255, 255, 0.1);
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image: radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
            color: var(--text);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand {
            font-weight: 600;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--text);
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .card {
            background: var(--surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 2rem;
        }

        button,
        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        button:hover,
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .logout-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.2);
        }

        /* Utility classes */
        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        .items-center {
            align-items: center;
        }
    </style>
</head>

<body>
    <nav>
        <a href="{{ route('dashboard') }}" class="brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                <path d="M12 2v20M2 12h20M12 2l4 4-4 4-4-4 4-4z" />
            </svg>
            Analítica
        </a>
        <div class="nav-links">
            <a href="{{ route('finance.resumen') }}"
                class="{{ request()->routeIs('finance.resumen') ? 'active' : '' }}">Resumen</a>
            <a href="{{ route('finance.tendencia') }}"
                class="{{ request()->routeIs('finance.tendencia') ? 'active' : '' }}">Tendencia</a>
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Historial</a>
            <a href="{{ route('finance.powerbi') }}" class="{{ request()->routeIs('finance.powerbi') ? 'active' : '' }}"
                style="color: var(--primary-yellow); font-weight: 600;">Power BI</a>
            <a href="{{ route('imports.create') }}" class="btn">Nuevos Registros</a>
        </div>
        <div class="user-info">
            <span>{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">Salir</button>
            </form>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>
</body>

</html>