<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Wheely Good Cars') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui'],
                    },
                },
            },
        };
    </script>
    @endif
</head>

<body class="min-h-screen bg-gradient-to-b from-stone-100 via-white to-stone-50 text-gray-900 font-['Instrument Sans']">
    <header class="bg-white border-b border-stone-200/70 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-md bg-stone-900 text-white flex items-center justify-center text-sm font-semibold">WG</div>
                <div>
                    <p class="text-lg font-semibold text-stone-900">Wheely Good Cars</p>
                    <p class="text-sm text-stone-500">Tweedehands aanbod</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm text-stone-700">
                <a class="px-3 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50" href="{{ route('cars.index') }}">Publiek</a>
                @auth
                <a class="px-3 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50" href="{{ route('provider.cars.index') }}">Mijn aanbod</a>
                @if (auth()->user()->isAdmin())
                <a class="px-3 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-3 py-2 rounded-md bg-stone-900 text-white hover:bg-stone-800" type="submit">Logout</button>
                </form>
                @else
                <a class="px-3 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50" href="{{ route('login.show') }}">Login</a>
                <a class="px-3 py-2 rounded-md bg-stone-900 text-white hover:bg-stone-800" href="{{ route('register.show') }}">Registreren</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-stone-200/70 bg-white/90 mt-8">
        <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-stone-600">
            <span>© {{ date('Y') }} Ben Linders</span>
            <span class="text-stone-500">Eerlijke tweedehands auto's, dagelijks bijgewerkt.</span>
        </div>
    </footer>
</body>

</html>