@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="pill w-fit">Aanbod aanpassen</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">{{ $car->display_title }}</h1>
            <p class="mt-2 text-stone-600">Werk vraagprijs, status, omschrijving en tags bij.</p>
        </div>
        <a href="{{ route('provider.cars.index') }}" class="btn">Terug</a>
    </div>

    @if (session('status'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <form method="POST" action="{{ route('provider.cars.update', $car) }}" class="space-y-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            @csrf
            @method('PATCH')
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="price">Vraagprijs (€)</label>
                    <input id="price" type="number" name="price" value="{{ $car->price }}" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="status">Status</label>
                    <select id="status" name="status" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3">
                        <option value="available" @selected($car->status === 'available')>Beschikbaar</option>
                        <option value="sold" @selected($car->status === 'sold')>Verkocht</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-stone-700" for="description">Omschrijving</label>
                <textarea id="description" name="description" rows="4" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3">{{ $car->description }}</textarea>
            </div>

            <div>
                <p class="mb-2 text-sm font-semibold text-stone-700">Tags</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                    <label class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-stone-50 px-3 py-2 text-sm cursor-pointer select-none hover:bg-white">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="accent-stone-900" {{ $car->tags->contains($tag) ? 'checked' : '' }}>
                        <span>{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Opslaan</button>
        </form>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4">
                <p class="text-xs uppercase tracking-wide text-stone-500">Auto</p>
                <p class="mt-1 text-lg font-semibold text-stone-900">{{ $car->make }} {{ $car->model }}</p>
                <p class="text-sm text-stone-600">{{ $car->license_plate ?? 'Geen kenteken' }}</p>
            </div>

            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                <dt class="text-stone-500">Bouwjaar</dt>
                <dd class="text-stone-900">{{ $car->year ?? 'Onbekend' }}</dd>
                <dt class="text-stone-500">Kilometerstand</dt>
                <dd class="text-stone-900">{{ number_format($car->mileage, 0, ',', '.') }} km</dd>
                <dt class="text-stone-500">Views</dt>
                <dd class="text-stone-900">{{ $car->views }}</dd>
                <dt class="text-stone-500">Status</dt>
                <dd class="text-stone-900">{{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}</dd>
            </dl>

            <div>
                <p class="mb-2 text-sm font-semibold text-stone-700">Huidige tags</p>
                <div class="flex flex-wrap gap-2">
                    @forelse ($car->tags as $tag)
                    <span class="pill">{{ $tag->name }}</span>
                    @empty
                    <span class="text-sm text-stone-500">Geen tags geselecteerd.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection