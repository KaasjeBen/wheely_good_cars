@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="pill w-fit">Opvallende aanbieders</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">Review lijst</h1>
            <p class="mt-2 text-stone-600">Markeer aanbieders op basis van gedragspatronen die extra controle vragen.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn">Dashboard</a>
    </div>

    <div class="space-y-4">
        @forelse ($providers as $item)
        <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-stone-900">{{ $item['user']->name }}</h2>
                    <p class="mt-1 text-sm text-stone-600">{{ $item['user']->email }} @if($item['user']->phone) • {{ $item['user']->phone }} @endif</p>
                </div>
                <span class="pill bg-rose-50 text-rose-700 border-rose-200">Opvallend</span>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($item['reasons'] as $reason)
                <span class="pill">{{ $reason }}</span>
                @endforeach
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-3 text-sm text-stone-600">
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Aanbiedingen</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">{{ $item['cars']->count() }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Laatste aanbod</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">{{ optional($item['cars']->max('created_at'))->format('Y-m-d') }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-stone-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Tags gebruikt</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">{{ $item['cars']->sum(fn ($car) => $car->tags->count()) }}</p>
                </div>
            </div>
        </article>
        @empty
        <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 px-4 py-8 text-center text-stone-600">
            Geen opvallende aanbieders gevonden.
        </div>
        @endforelse
    </div>
</section>
@endsection