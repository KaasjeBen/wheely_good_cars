@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="rounded-2xl border border-stone-200 bg-gradient-to-br from-white via-stone-50 to-white p-6 md:p-8 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="pill w-fit">Publiek aanbod</p>
                <h1 class="mt-3 text-3xl md:text-4xl font-semibold tracking-tight text-stone-900">Vind snel een passende auto</h1>
                <p class="mt-2 text-stone-600">Zoek in merk of model, filter op tags en blader zonder pagina reload.</p>
            </div>
            <div class="stat">
                <span class="label">Beschikbaar</span>
                <span class="value text-2xl" id="result-count">{{ $cars->total() }}</span>
            </div>
        </div>
    </div>

    <form id="car-search-form" method="GET" action="{{ route('cars.index') }}" class="rounded-2xl border border-stone-200 bg-white p-4 md:p-5 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-[1.4fr_1fr_auto] lg:items-start">
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2" for="search">Zoek in merk of model</label>
                <div class="relative">
                    <input id="search" name="q" value="{{ $search }}" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3 pr-12 focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Bijv. Volvo, BMW, CX-5" autocomplete="off" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-stone-500">⌕</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Filter op tags</label>
                <div class="flex flex-wrap gap-2" id="tag-filters">
                    @foreach ($tags as $tag)
                    @php $inputId = 'tag-' . $tag->id; @endphp
                    <label for="{{ $inputId }}" class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-stone-50 px-3 py-2 text-sm cursor-pointer select-none transition hover:border-stone-300 hover:bg-white">
                        <input id="{{ $inputId }}" type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, $activeTags) ? 'checked' : '' }} class="accent-stone-900">
                        <span class="text-stone-800">{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-2 lg:flex-col lg:justify-end">
                <button type="submit" class="btn btn-primary">Toon resultaten</button>
                <a href="{{ route('cars.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </div>
    </form>

    <div class="flex items-center justify-between text-sm text-stone-600">
        <p id="results-meta">Resultaten op deze pagina: {{ $cars->count() }}</p>
        <p>Pagina {{ $cars->currentPage() }} van {{ $cars->lastPage() }}</p>
    </div>

    <div id="cars-grid">
        @include('cars.partials.grid', ['cars' => $cars])
    </div>

    <div id="pagination" class="mt-2">
        @include('cars.partials.pagination', ['cars' => $cars])
    </div>
</section>

<script>
    (() => {
        const form = document.querySelector('#car-search-form');
        const grid = document.querySelector('#cars-grid');
        const pagination = document.querySelector('#pagination');
        const resultCount = document.querySelector('#result-count');
        const resultsMeta = document.querySelector('#results-meta');
        const searchInput = document.querySelector('#search');
        const tagInputs = Array.from(form.querySelectorAll('input[type="checkbox"]'));
        let debounceId = null;

        const setBusy = (busy) => {
            form.querySelectorAll('input, button, a').forEach((el) => {
                if (el.closest('a')) {
                    el.dataset.busy = busy ? '1' : '0';
                    return;
                }

                el.disabled = busy;
            });
        };

        const updatePage = async (url = form.action) => {
            const target = new URL(url, window.location.origin);
            const params = new URLSearchParams(new FormData(form));
            target.search = params.toString();

            setBusy(true);

            try {
                const response = await fetch(target.toString(), {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Kan resultaten niet laden.');
                }

                const data = await response.json();
                grid.innerHTML = data.grid;
                pagination.innerHTML = data.pagination;
                resultCount.textContent = data.count;
                resultsMeta.textContent = `Resultaten gevonden: ${data.count}`;
                window.history.replaceState({}, '', target.toString());
            } catch (error) {
                resultsMeta.textContent = error.message;
            } finally {
                setBusy(false);
            }
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            updatePage();
        });

        searchInput.addEventListener('input', () => {
            window.clearTimeout(debounceId);
            debounceId = window.setTimeout(() => updatePage(), 250);
        });

        tagInputs.forEach((input) => {
            input.addEventListener('change', () => updatePage());
        });

        pagination.addEventListener('click', (event) => {
            const link = event.target.closest('a');

            if (!link) {
                return;
            }

            event.preventDefault();
            updatePage(link.href);
        });
    })();
</script>
@endsection