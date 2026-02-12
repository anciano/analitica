<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Hospital Analítica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#4F8DF5',
                        'bg-app': '#F5F7FA',
                        'text-main': '#1F2937',
                        'text-muted': '#6B7280',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #F5F7FA;
            background-image:
                radial-gradient(at 0% 0%, rgba(79, 141, 245, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 141, 245, 0.05) 0px, transparent 50%);
        }

        .login-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            border: 1px solid #E6ECF2;
        }

        .form-input {
            transition: all 0.2s;
            border: 1px solid #E6ECF2;
        }

        .form-input:focus {
            border-color: #4F8DF5;
            box-shadow: 0 0 0 3px rgba(79, 141, 245, 0.1);
            outline: none;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-max-w-[400px]">
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-blue text-white mb-4 shadow-lg shadow-primary-blue/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <h1 class="text-[28px] font-bold text-text-main tracking-tight">Hospital Analítica</h1>
            <p class="text-text-muted text-[15px]">Gestión Presupuestaria y Control</p>
        </div>

        <div class="login-card bg-white rounded-3xl p-8 md:p-10 w-full max-w-[400px] mx-auto">
            @if(session('error'))
                <div
                    class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[13px] font-medium flex items-center">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        class="mr-2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.local') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[12px] font-bold text-text-muted uppercase tracking-wider mb-2">RUT de
                        Usuario</label>
                    <div class="relative">
                        <input type="text" name="username" placeholder="12345678" required
                            class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 text-text-main text-[15px] placeholder:text-gray-400">
                    </div>
                    <p class="mt-1.5 text-[11px] text-text-muted">Sin puntos ni guión (ej: 17056535)</p>
                </div>

                <div>
                    <label
                        class="block text-[12px] font-bold text-text-muted uppercase tracking-wider mb-2">Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 text-text-main text-[15px] placeholder:text-gray-400">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-primary-blue hover:bg-blue-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-md shadow-primary-blue/20 active:transform active:scale-[0.98]">
                        Iniciar Sesión
                    </button>
                </div>

                @if($errors->has('login'))
                    <div class="p-3 rounded-lg bg-red-50 text-red-500 text-[12px] text-center font-medium">
                        {{ $errors->first('login') }}
                    </div>
                @endif
            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-[13px] text-text-muted">
                &copy; {{ date('Y') }} Hospital Analítica · <span class="font-medium text-primary-blue">Aysén</span>
            </p>
        </div>
    </div>
</body>

</html>