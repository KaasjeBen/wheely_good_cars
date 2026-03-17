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
    <style>
        {
            ! ! file_get_contents(resource_path('css/app.css')) ! !
        }
    </style>
    @endif
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <div class="brand">
                <div class="brand-mark">WG</div>
                <div>
                    <p class="brand-title">Wheely Good Cars</p>
                    <p class="brand-subtitle">Tweedehands aanbod</p>
                </div>
            </div>
            <div class="nav-actions">
                <a class="btn" href="{{ route('cars.index') }}">Publiek</a>
                @auth
                <a class="btn" href="{{ route('provider.cars.index') }}">Mijn aanbod</a>
                @if (auth()->user()->isAdmin())
                <a class="btn" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button class="btn btn-primary" type="submit">Logout</button>
                </form>
                @else
                <a class="btn" href="{{ route('login.show') }}">Login</a>
                <a class="btn btn-primary" href="{{ route('register.show') }}">Registreren</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="container main-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <p class="footer-note">© {{ date('Y') }} Ben Linders</p>
            <p class="footer-note">Eerlijke tweedehands auto's, dagelijks bijgewerkt.</p>
        </div>
    </footer>
</body>

</html>