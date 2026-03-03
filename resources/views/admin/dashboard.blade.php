@extends('layouts.app')

@section('content')
<div class="bg-white border border-stone-200 rounded-lg p-6 shadow-sm">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-stone-600 uppercase">Realtime dashboard</p>
            <h1 class="text-2xl font-semibold leading-tight text-stone-900">Kantoor scherm</h1>
            <p class="text-stone-700">Ververs elke 10s: aanbod, verkocht, vandaag, aanbieders, views en gemiddeld per aanbieder.</p>
        </div>
        <div class="flex gap-2">
            <a class="px-3 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50" href="{{ route('admin.tag-usage') }}">Tag gebruik</a>
            <a class="px-3 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50" href="{{ route('admin.suspicious') }}">Opvallende aanbieders</a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-3" id="metrics">
        @foreach (['totalCars' => 'Totaal aanbod', 'sold' => 'Verkocht', 'todayOffered' => 'Vandaag toegevoegd', 'providers' => 'Aanbieders', 'viewsToday' => 'Views vandaag', 'avgPerProvider' => 'Gemiddeld per aanbieder'] as $key => $label)
        <div class="rounded-md border border-stone-200 bg-stone-50 p-4">
            <p class="text-sm text-stone-700">{{ $label }}</p>
            <p class="text-2xl font-semibold" data-key="{{ $key }}">--</p>
            <div class="mt-2 h-2 bg-white border border-stone-200 rounded overflow-hidden">
                <div class="inner h-2 bg-stone-700" style="width: 20%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    const fetchMetrics = () => {
        fetch(`{{ route('admin.metrics') }}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                Object.entries(data).forEach(([key, value]) => {
                    const el = document.querySelector(`[data-key="${key}"]`);
                    if (el) {
                        el.textContent = value;
                        const bar = el.parentElement.querySelector('.inner');
                        if (bar) bar.style.width = `${Math.min(100, (value / (data.totalCars || 1)) * 100)}%`;
                    }
                });
            });
    };
    fetchMetrics();
    setInterval(fetchMetrics, 10000);
</script>
@endsection