@extends('layouts.app')

@section('content')
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-orange-300 uppercase tracking-[0.3em] mb-2">Aanbod aanpassen</p>
            <h1 class="text-3xl font-semibold leading-tight">{{ $car->display_title }}</h1>
            <p class="text-gray-300">Pas prijs, status, beschrijving en tags aan.</p>
        </div>
        <a href="{{ route('provider.cars.index') }}" class="px-4 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15">Terug</a>
    </div>

    @if (session('status'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/15 border border-green-500/30 text-green-100">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('provider.cars.update', $car) }}" class="space-y-4">
        @csrf
        @method('PATCH')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-200 mb-1">Vraagprijs (€)</label>
                <input type="number" name="price" value="{{ $car->price }}" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3" required>
            </div>
            <div>
                <label class="block text-sm text-gray-200 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3">
                    <option value="available" @selected($car->status === 'available')>Beschikbaar</option>
                    <option value="sold" @selected($car->status === 'sold')>Verkocht</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm text-gray-200 mb-1">Omschrijving</label>
            <textarea name="description" rows="3" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3">{{ $car->description }}</textarea>
        </div>
        <div>
            <p class="text-sm text-gray-300 mb-2">Tags</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-sm cursor-pointer select-none">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="accent-orange-400" {{ $car->tags->contains($tag) ? 'checked' : '' }}>
                    <span>{{ $tag->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
        <button type="submit" class="px-4 py-3 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400">Opslaan</button>
    </form>
</div>
@endsection