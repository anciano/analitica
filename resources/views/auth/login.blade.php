<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hospital Analitica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image:
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.1) 0px, transparent 50%);
            color: var(--text);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 1.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 1.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        p {
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: #ffffff;
            color: #1f2937;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            width: 100%;
            box-sizing: border-box;
        }

        .google-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            background: #f9fafb;
        }

        .google-btn svg {
            width: 20px;
            height: 20px;
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h1>Hospital Analítica</h1>
        <p>Sistema de Registros y ETL</p>

        @if(session('error'))
            <div class="error-msg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.local') }}" method="POST" style="margin-bottom: 1.5rem; text-align: left;">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">RUT
                    (sin puntos ni guión)</label>
                <input type="text" name="username" placeholder="17056535" required
                    style="width: 100%; padding: 0.75rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text); box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label
                    style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required
                    style="width: 100%; padding: 0.75rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text); box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; font-weight: 600;">Entrar
                con mi cuenta</button>

            @if($errors->has('login'))
                <div class="error-msg" style="margin-top: 1rem; margin-bottom: 0;">
                    {{ $errors->first('login') }}
                </div>
            @endif
        </form>

        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; color: var(--text-muted);">
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
            <span style="font-size: 0.75rem;">O TAMBIÉN</span>
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
        </div>

        <a href="{{ route('auth.google') }}" class="google-btn">
            <svg viewBox="0 0 24 24">
                <path fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" />
                <path fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                <path fill="none" d="M1 1 23 23" />
            </svg>
            Continuar con Google
        </a>

        <div class="footer">
            Acceso exclusivo @saludaysen.cl
        </div>
    </div>
</body>

</html>