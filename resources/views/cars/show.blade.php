@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-3xl">
            <p class="pill w-fit">Detailpagina</p>
            <h1 class="mt-3 text-3xl md:text-4xl font-semibold tracking-tight text-stone-900">{{ $car->display_title }}</h1>
            <p class="mt-2 text-stone-600">{{ $car->make }} • {{ $car->model }} • {{ $car->year ?? 'Bouwjaar onbekend' }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white px-5 py-4 shadow-sm text-right">
            <p class="text-xs uppercase tracking-wide text-stone-500">Vraagprijs</p>
            <p class="text-3xl font-semibold text-stone-900">€ {{ number_format($car->price, 0, ',', '.') }}</p>
            <p class="text-xs text-stone-500 mt-1">{{ number_format($car->mileage, 0, ',', '.') }} km • {{ $car->views }} views</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[1.5fr_1fr]">
        <div class="relative overflow-hidden rounded-2xl border border-stone-200 bg-stone-100 shadow-sm">
            @if ($car->image_path)
            <img src="{{ asset('storage/' . $car->image_path) }}" alt="{{ $car->display_title }}" class="h-full min-h-[22rem] w-full object-cover">
            @else
            <div class="flex min-h-[22rem] items-center justify-center bg-gradient-to-br from-stone-100 via-white to-stone-50 text-6xl font-black text-stone-200">WG</div>
            @endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm space-y-5">
            <div class="flex flex-wrap gap-2">
                <span class="pill">{{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}</span>
                <span class="pill">Aanbieder: {{ $car->user?->name ?? 'Onbekend' }}</span>
                <span class="pill">Views: {{ $car->views }}</span>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-500 mb-3">Details</h2>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <dt class="text-stone-500">Merk</dt>
                    <dd class="text-stone-900">{{ $car->make }}</dd>
                    <dt class="text-stone-500">Model</dt>
                    <dd class="text-stone-900">{{ $car->model }}</dd>
                    <dt class="text-stone-500">Bouwjaar</dt>
                    <dd class="text-stone-900">{{ $car->year ?? 'Onbekend' }}</dd>
                    <dt class="text-stone-500">Kilometerstand</dt>
                    <dd class="text-stone-900">{{ number_format($car->mileage, 0, ',', '.') }} km</dd>
                    <dt class="text-stone-500">Kenteken</dt>
                    <dd class="text-stone-900">{{ $car->license_plate ?? 'Onbekend' }}</dd>
                    <dt class="text-stone-500">Vraagprijs</dt>
                    <dd class="text-stone-900">€ {{ number_format($car->price, 0, ',', '.') }}</dd>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-stone-500 mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse ($car->tags as $tag)
                    <span class="pill">{{ $tag->name }}</span>
                    @empty
                    <span class="text-sm text-stone-500">Geen tags toegevoegd.</span>
                    @endforelse
                </div>
            </div>

            @if ($car->description)
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-stone-500 mb-2">Omschrijving</h3>
                <p class="text-sm leading-relaxed text-stone-700">{{ $car->description }}</p>
            </div>
            @endif

            <a href="{{ route('cars.index') }}" class="btn btn-primary w-full">Terug naar aanbod</a>
        </div>
    </div>
</section>

<div id="view-toast" class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-2xl border border-stone-200 bg-white p-4 shadow-xl shadow-stone-300/40">
    <div class="flex items-start gap-3">
        <div class="mt-0.5 h-3 w-3 rounded-full bg-emerald-500"></div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-stone-900">{{ $car->views }} klanten bekeken deze auto vandaag</p>
            <p class="mt-1 text-sm text-stone-600">Dit is een vaste melding met het huidige view-aantal als extra context.</p>
        </div>
        <button type="button" id="view-toast-close" class="text-stone-400 hover:text-stone-700" aria-label="Sluiten">×</button>
    </div>
</div>

<script>
    (() => {
        const toast = document.querySelector('#view-toast');
        const close = document.querySelector('#view-toast-close');
        let timer = window.setTimeout(() => {
            toast.classList.remove('hidden');
        }, 10000);

        close.addEventListener('click', () => {
            window.clearTimeout(timer);
            toast.classList.add('hidden');
        });
    })();
</script>
@endsection