@extends('layouts.app')

@section('content')
<section class="bg-white border border-stone-200 rounded-lg p-6 shadow-sm">
    <div class="relative overflow-hidden rounded-md border border-amber-100 bg-gradient-to-r from-white via-amber-50 to-white p-5 mb-6">
        <div class="absolute inset-y-0 right-0 w-20 bg-gradient-to-l from-amber-100/70 to-transparent"></div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 relative">
            <div class="flex-1">
                <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-semibold uppercase tracking-wide">Nieuw aanbod</p>
                <h1 class="text-3xl md:text-4xl font-semibold leading-tight text-stone-900 mt-2">Vind je volgende auto</h1>
                <p class="text-stone-700 mt-2">Zoek, filter op tags en blader zonder reload door het actuele aanbod.</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-stone-800 bg-white/80 border border-amber-100 rounded-md px-3 py-2 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $cars->total() }} auto's beschikbaar</span>
            </div>
        </div>
    </div>

    <div class="bg-stone-50 border border-stone-200 rounded-lg p-4 md:p-5 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 lg:items-center">
            <div class="flex-1">
                <label class="block text-sm text-stone-700 mb-1" for="search">Zoek in merk of model</label>
                <div class="relative">
                    <input id="search" name="q" value="{{ $search }}" class="w-full rounded-md bg-white border border-stone-300 px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-stone-700" placeholder="Bijv. Volvo, BMW, CX-5" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-stone-500">⌕</span>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm text-stone-700 mb-2">Filter op tags</p>
                <div class="flex flex-wrap gap-2" id="tag-filters">
                    @foreach ($tags as $tag)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-md bg-white border border-stone-200 text-sm cursor-pointer select-none">
                        <input type="checkbox" value="{{ $tag->id }}" {{ in_array($tag->id, $activeTags) ? 'checked' : '' }} class="accent-stone-800">
                        <span class="text-stone-800">{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex-none flex flex-col gap-2 text-sm text-stone-700">
                <button id="clear-filters" class="px-4 py-2 rounded-md border border-stone-200 bg-white hover:bg-stone-50">Reset</button>
                <div class="text-xs text-stone-500">Resultaten: <span id="result-count">{{ $cars->total() }}</span></div>
            </div>
        </div>
    </div>

    <div id="cars-grid">
        @include('cars.partials.grid', ['cars' => $cars])
    </div>

    <div id="pagination" class="mt-6">
        @include('cars.partials.pagination', ['cars' => $cars])
    </div>
</section>

<script>
    const searchInput = document.querySelector('#search');
    const tagFilters = document.querySelectorAll('#tag-filters input[type="checkbox"]');
    const clearFilters = document.querySelector('#clear-filters');
    const grid = document.querySelector('#cars-grid');
    const pagination = document.querySelector('#pagination');
    const resultCount = document.querySelector('#result-count');

    let fetchTimeout;

    const buildQuery = () => {
        const params = new URLSearchParams();
        const q = searchInput.value.trim();
        if (q.length) params.set('q', q);
        const tags = Array.from(tagFilters).filter(cb => cb.checked).map(cb => cb.value);
        tags.forEach(tag => params.append('tags[]', tag));
        return params;
    };

    const loadCars = (targetUrl = null) => {
        const params = buildQuery();
        const url = new URL(targetUrl ?? window.location.href);
        url.search = params.toString();

        fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                grid.innerHTML = data.grid;
                pagination.innerHTML = data.pagination;
                resultCount.textContent = data.count;
                window.history.replaceState({}, '', url.toString());
                bindPagination();
            });
    };

    const scheduleFetch = () => {
        clearTimeout(fetchTimeout);
        fetchTimeout = setTimeout(() => loadCars(), 200);
    };

    searchInput?.addEventListener('input', scheduleFetch);
    tagFilters.forEach(cb => cb.addEventListener('change', scheduleFetch));

    clearFilters?.addEventListener('click', () => {
        searchInput.value = '';
        tagFilters.forEach(cb => cb.checked = false);
        loadCars();
    });

    const bindPagination = () => {
        pagination.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();
                loadCars(link.href);
            });
        });
    };

    bindPagination();
</script>
@endsection