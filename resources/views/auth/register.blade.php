@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <h1 class="text-3xl font-semibold mb-2">Account aanmaken</h1>
    <p class="text-gray-300 mb-6">Registreer als aanbieder en start met aanbieden.</p>
    <form method="POST" action="{{ route('register.perform') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-200 mb-1">Naam</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" required>
            @error('name')<p class="text-sm text-red-300 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-200 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" required>
            @error('email')<p class="text-sm text-red-300 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-200 mb-1">Telefoon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70">
            @error('phone')<p class="text-sm text-red-300 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-200 mb-1">Wachtwoord</label>
            <input type="password" name="password" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" required>
            @error('password')<p class="text-sm text-red-300 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-200 mb-1">Herhaal wachtwoord</label>
            <input type="password" name="password_confirmation" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" required>
        </div>
        <button type="submit" class="w-full inline-flex justify-center px-4 py-3 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400 transition">Account aanmaken</button>
        <p class="text-sm text-gray-300">Heb je al een account? <a class="text-orange-200 underline" href="{{ route('login.show') }}">Log in</a></p>
    </form>
</div>
@endsection