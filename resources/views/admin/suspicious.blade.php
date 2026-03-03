@extends('layouts.app')

@section('content')
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-orange-300 uppercase tracking-[0.3em] mb-2">Opvallende aanbieders</p>
            <h1 class="text-3xl font-semibold leading-tight">Review lijst</h1>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15">Dashboard</a>
    </div>

    <div class="space-y-4">
        @forelse ($providers as $item)
        <div class="border border-white/10 rounded-xl p-4 bg-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-lg">{{ $item['user']->name }}</p>
                    <p class="text-sm text-gray-300">{{ $item['user']->email }} {{ $item['user']->phone ? '• '.$item['user']->phone : '' }}</p>
                </div>
                <span class="px-3 py-1 rounded-lg bg-red-500/20 text-red-100 border border-red-500/40">Opvallend</span>
            </div>
            <div class="mt-3 text-sm text-gray-200 flex flex-wrap gap-2">
                @foreach ($item['reasons'] as $reason)
                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/10">{{ $reason }}</span>
                @endforeach
            </div>
            <div class="mt-3 text-sm text-gray-300">Aanbiedingen: {{ $item['cars']->count() }} | Laatste aanbod: {{ optional($item['cars']->max('created_at'))->format('Y-m-d') }}</div>
        </div>
        @empty
        <p class="text-gray-300">Geen opvallende aanbieders gevonden.</p>
        @endforelse
    </div>
</div>
@endsection