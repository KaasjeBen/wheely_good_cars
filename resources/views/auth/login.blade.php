@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <h1 class="text-3xl font-semibold mb-2">Inloggen</h1>
    <p class="text-gray-300 mb-6">Log in om je aanbiedingen te beheren.</p>
    <form method="POST" action="{{ route('login.perform') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-200 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" required>
            @error('email')<p class="text-sm text-red-300 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-200 mb-1">Wachtwoord</label>
            <input type="password" name="password" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" required>
            @error('password')<p class="text-sm text-red-300 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full inline-flex justify-center px-4 py-3 rounded-lg bg-green-500 text-black font-semibold hover:bg-green-700 transition">Log in</button>
        <p class="text-sm text-gray-300">Nog geen account? <a class="text-green-200 underline" href="{{ route('register.show') }}">Registreer</a></p>
    </form>
</div>
@endsection