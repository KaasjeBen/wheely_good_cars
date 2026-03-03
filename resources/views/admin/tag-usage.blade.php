@extends('layouts.app')

@section('content')
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-orange-300 uppercase tracking-[0.3em] mb-2">Tag statistieken</p>
            <h1 class="text-3xl font-semibold leading-tight">Gebruik per tag</h1>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15">Dashboard</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-gray-200 border border-white/10 rounded-lg overflow-hidden">
            <thead class="bg-white/5">
                <tr>
                    <th class="text-left px-4 py-3">Tag</th>
                    <th class="text-left px-4 py-3">Totaal gebruikt</th>
                    <th class="text-left px-4 py-3">Verkochte auto's</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tags as $tag)
                <tr class="border-t border-white/5">
                    <td class="px-4 py-3">{{ $tag->name }}</td>
                    <td class="px-4 py-3">{{ $tag->total_usage }}</td>
                    <td class="px-4 py-3">{{ $tag->sold_usage }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection