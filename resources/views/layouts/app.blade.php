<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'API DAWG')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        pre { white-space: pre-wrap; word-break: break-word; }
        .json-block { font-family: ui-monospace, monospace; font-size: 0.8rem; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
    <nav class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-bold text-lg">
                <svg class="w-7 h-7" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="32" cy="32" r="30" fill="#fbbf24"/>
                    <path d="M14 28 L22 20 L30 26 L34 18 L42 26 L50 20 L50 30" stroke="#374151" stroke-width="3" fill="#374151" stroke-linecap="round"/>
                    <circle cx="32" cy="38" r="12" fill="#6b4226"/>
                    <rect x="22" y="34" width="20" height="8" rx="4" fill="#111"/>
                    <path d="M22 38 L14 38 M42 38 L50 38" stroke="#111" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="32" cy="48" r="2" fill="#fbbf24"/>
                    <path d="M24 30 Q32 34 40 30" stroke="#111" stroke-width="2" fill="none"/>
                </svg>
                API DAWG
            </a>
            <div class="flex items-center gap-4 text-sm">
                <a href="/" class="hover:text-slate-300">Dokumentasi</a>
                <a href="/manage" class="hover:text-slate-300">Kelola</a>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-slate-400 text-center text-xs py-3">
        🎤 API DAWG — Dokumentasi API
    </footer>
</body>
</html>
