@extends('layouts.app')

@section('content')
<section class="bg-white border border-stone-200 rounded-lg p-6 md:p-8 shadow-sm">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-stone-600 uppercase">Detailpagina</p>
            <h1 class="text-3xl md:text-4xl font-semibold leading-tight text-stone-900">{{ $car->display_title }}</h1>
            <p class="text-stone-700 mt-2">{{ $car->make }} • {{ $car->model }} • {{ $car->year ?? 'Bouwjaar onbekend' }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-stone-600">Vraagprijs</p>
            <p class="text-3xl font-semibold text-stone-900">€ {{ number_format($car->price, 0, ',', '.') }}</p>
            <p class="text-xs text-stone-600 mt-1">{{ number_format($car->mileage, 0, ',', '.') }} km</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 relative rounded-lg overflow-hidden border border-stone-200 bg-stone-100">
            @if ($car->image_path)
            <img src="{{ asset('storage/'.$car->image_path) }}" alt="{{ $car->display_title }}" class="w-full h-80 object-cover">
            @else
            <div class="flex items-center justify-center h-80 text-5xl font-black text-gray-200">WG</div>
            @endif
        </div>
        <div class="relative p-6 flex flex-col gap-3 bg-stone-50 border border-stone-200 rounded-lg">
            <p class="text-stone-800">Stel je {{ $car->make }} {{ $car->model }} voor op de showroom vloer.</p>
            <div class="flex items-center gap-3 text-sm text-stone-800">
                <span class="px-3 py-1 rounded-md bg-white border border-stone-200">Status: {{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}</span>
                <span class="px-3 py-1 rounded-md bg-white border border-stone-200">Views: {{ $car->views }}</span>
            </div>
        </div>
    </div>
    <div class="rounded-lg border border-stone-200 bg-stone-50 p-5 flex flex-col gap-3">
        <h2 class="text-xl font-semibold text-stone-900">Details</h2>
        <div class="grid grid-cols-2 gap-3 text-sm text-stone-800">
            <span class="text-stone-700">Merk</span><span>{{ $car->make }}</span>
            <span class="text-stone-700">Model</span><span>{{ $car->model }}</span>
            <span class="text-stone-700">Bouwjaar</span><span>{{ $car->year ?? 'Onbekend' }}</span>
            <span class="text-stone-700">Kilometerstand</span><span>{{ number_format($car->mileage, 0, ',', '.') }} km</span>
            <span class="text-stone-700">Aanbieder</span><span>{{ $car->user?->name ?? 'Onbekend' }}</span>
            <span class="text-stone-700">Vraagprijs</span><span>€ {{ number_format($car->price, 0, ',', '.') }}</span>
        </div>
        <div>
            <h3 class="text-sm text-stone-700 mb-2">Tags</h3>
            <div class="flex flex-wrap gap-2">
                @forelse ($car->tags as $tag)
                <span class="px-3 py-1 rounded-full bg-white text-stone-800 text-xs border border-stone-200">{{ $tag->name }}</span>
                @empty
                <span class="text-xs text-stone-500">Geen tags toegevoegd.</span>
                @endforelse
            </div>
        </div>
        @if ($car->description)
        <div>
            <h3 class="text-sm text-stone-700 mb-2">Omschrijving</h3>
            <p class="text-sm text-stone-800 leading-relaxed">{{ $car->description }}</p>
        </div>
        @endif
        <a href="{{ route('cars.index') }}" class="mt-auto inline-flex items-center justify-center px-4 py-2 rounded-md border border-stone-300 bg-white text-stone-900 font-semibold hover:bg-stone-50 transition">Terug naar aanbod</a>
    </div>
</section>
@endsection