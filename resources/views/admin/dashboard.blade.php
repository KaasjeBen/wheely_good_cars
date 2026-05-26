@extends('layouts.app')

@section('content')
@php
$metrics = $metrics ?? [];
@endphp
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl">
            <p class="pill w-fit">Realtime dashboard</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">Kantoor scherm</h1>
            <p class="mt-2 text-stone-600">Ververs elke 10 seconden met aanbod, verkocht, vandaag, aanbieders, views en gemiddelde voorraad per aanbieder.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="btn" href="{{ route('admin.tag-usage') }}">Tag gebruik</a>
            <a class="btn" href="{{ route('admin.suspicious') }}">Opvallende aanbieders</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" id="metric-grid">
        @foreach ([
        'totalCars' => 'Totaal aanbod',
        'sold' => 'Verkocht',
        'available' => 'Beschikbaar',
        'todayOffered' => 'Vandaag toegevoegd',
        'providers' => 'Aanbieders',
        'viewsToday' => 'Views vandaag',
        'avgPerProvider' => 'Gemiddeld per aanbieder',
        ] as $key => $label)
        @php $value = $metrics[$key] ?? 0; @endphp
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-stone-500">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-stone-900" data-key="{{ $key }}">{{ $value }}</p>
                </div>
                <span class="pill">Live</span>
            </div>
            <div class="mt-4 h-2 rounded-full bg-stone-100">
                <div class="metric-bar h-2 rounded-full bg-stone-900" data-bar-for="{{ $key }}" data-width="{{ isset($metrics['totalCars']) && $metrics['totalCars'] > 0 ? min(100, ($value / max($metrics['totalCars'], 1)) * 100) : 0 }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-stone-500">Trend laatste 7 dagen</p>
                    <h2 class="mt-1 text-xl font-semibold text-stone-900">Views en aanbiedingen</h2>
                </div>
            </div>
            <div id="trend-chart" class="mt-5 space-y-3">
                @foreach (($metrics['dailyViews'] ?? []) as $day)
                <div class="grid grid-cols-[3rem_1fr_4rem] items-center gap-3 text-sm">
                    <span class="text-stone-500">{{ $day['label'] }}</span>
                    <div class="flex h-9 items-center gap-2 rounded-full bg-stone-50 px-3">
                        <div class="h-2 rounded-full bg-stone-900" data-trend-views="{{ min(100, ($day['views'] ?? 0) * 10) }}"></div>
                        <div class="h-2 rounded-full bg-stone-300" data-trend-offers="{{ min(100, ($day['offers'] ?? 0) * 10) }}"></div>
                    </div>
                    <span class="text-right text-stone-600">{{ $day['views'] ?? 0 }}/{{ $day['offers'] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm space-y-4">
            <div>
                <p class="text-sm font-semibold text-stone-500">Snelle context</p>
                <h2 class="mt-1 text-xl font-semibold text-stone-900">Aanbodmix</h2>
            </div>
            @foreach ([
            ['label' => 'Verkocht', 'key' => 'sold', 'tone' => 'bg-rose-500'],
            ['label' => 'Beschikbaar', 'key' => 'available', 'tone' => 'bg-emerald-500'],
            ['label' => 'Vandaag toegevoegd', 'key' => 'todayOffered', 'tone' => 'bg-amber-500'],
            ['label' => 'Views vandaag', 'key' => 'viewsToday', 'tone' => 'bg-stone-900'],
            ] as $item)
            <div>
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="text-stone-600">{{ $item['label'] }}</span>
                    <span class="font-semibold text-stone-900" data-key="{{ $item['key'] }}">{{ $metrics[$item['key']] ?? 0 }}</span>
                </div>
                <div class="h-2 rounded-full bg-stone-100">
                    <div class="h-2 rounded-full {{ $item['tone'] }}" data-bar-for="{{ $item['key'] }}" data-width="{{ isset($metrics['totalCars']) && $metrics['totalCars'] > 0 ? min(100, (($metrics[$item['key']] ?? 0) / max($metrics['totalCars'], 1)) * 100) : 0 }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    (() => {
        const metricsUrl = `{{ route('admin.metrics') }}`;

        const applyWidths = (root = document) => {
            root.querySelectorAll('[data-width]').forEach((element) => {
                element.style.width = `${element.dataset.width}%`;
            });

            root.querySelectorAll('[data-trend-views]').forEach((element) => {
                element.style.width = `${element.dataset.trendViews}%`;
            });

            root.querySelectorAll('[data-trend-offers]').forEach((element) => {
                element.style.width = `${element.dataset.trendOffers}%`;
            });
        };

        const renderTrend = (days) => {
            const trendChart = document.querySelector('#trend-chart');
            if (!trendChart || !Array.isArray(days)) {
                return;
            }

            trendChart.innerHTML = days.map((day) => `
                <div class="grid grid-cols-[3rem_1fr_4rem] items-center gap-3 text-sm">
                    <span class="text-stone-500">${day.label}</span>
                    <div class="flex h-9 items-center gap-2 rounded-full bg-stone-50 px-3">
                        <div class="h-2 rounded-full bg-stone-900" style="width:${Math.min(100, (day.views || 0) * 10)}%"></div>
                        <div class="h-2 rounded-full bg-stone-300" style="width:${Math.min(100, (day.offers || 0) * 10)}%"></div>
                    </div>
                    <span class="text-right text-stone-600">${day.views || 0}/${day.offers || 0}</span>
                </div>
            `).join('');
        };

        const updateMetrics = async () => {
            const response = await fetch(metricsUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();

            Object.entries(data).forEach(([key, value]) => {
                const metric = document.querySelector(`[data-key="${key}"]`);
                if (metric && typeof value !== 'object') {
                    metric.textContent = value;
                }
            });

            renderTrend(data.dailyViews);
            applyWidths();
        };

        applyWidths();
        updateMetrics();
        window.setInterval(updateMetrics, 10000);
    })();
</script>
@endsection