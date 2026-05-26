@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="pill w-fit">Tag statistieken</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">Gebruik per tag</h1>
            <p class="mt-2 text-stone-600">Overzicht van totaal, verkocht en beschikbaar per eigenschap.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn">Dashboard</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-stone-600">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Tag</th>
                    <th class="px-4 py-3 text-left font-semibold">Totaal</th>
                    <th class="px-4 py-3 text-left font-semibold">Verkocht</th>
                    <th class="px-4 py-3 text-left font-semibold">Beschikbaar</th>
                    <th class="px-4 py-3 text-left font-semibold">Verdeling</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                @foreach ($tags as $tag)
                <tr>
                    <td class="px-4 py-4 font-medium text-stone-900">{{ $tag->name }}</td>
                    <td class="px-4 py-4 text-stone-700">{{ $tag->total_usage }}</td>
                    <td class="px-4 py-4 text-stone-700">{{ $tag->sold_usage }}</td>
                    <td class="px-4 py-4 text-stone-700">{{ $tag->available_usage }}</td>
                    <td class="px-4 py-4">
                        <div class="h-2 rounded-full bg-stone-100 overflow-hidden">
                            @php
                            $total = max(1, $tag->total_usage);
                            $soldWidth = ($tag->sold_usage / $total) * 100;
                            $availableWidth = ($tag->available_usage / $total) * 100;
                            @endphp
                            <div class="flex h-2 w-full">
                                <div class="bg-rose-500" data-width="{{ $soldWidth }}"></div>
                                <div class="bg-emerald-500" data-width="{{ $availableWidth }}"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<script>
    (() => {
        document.querySelectorAll('[data-width]').forEach((element) => {
            element.style.width = `${element.dataset.width}%`;
        });
    })();
</script>
@endsection