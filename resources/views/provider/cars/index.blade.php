@extends('layouts.app')

@section('content')
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-orange-300 uppercase tracking-[0.3em] mb-2">Mijn aanbiedingen</p>
            <h1 class="text-3xl font-semibold leading-tight">Beheer je aanbod</h1>
            <p class="text-gray-300">Status toggles zonder reload, prijs wijzigen en pdf genereren.</p>
        </div>
        <a href="{{ route('provider.cars.create') }}" class="inline-flex items-center px-4 py-3 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400 transition">+ Nieuwe auto</a>
    </div>

    @if (session('status'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-500/15 border border-green-500/30 text-green-100">{{ session('status') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-gray-200 border border-white/10 rounded-lg overflow-hidden">
            <thead class="bg-white/5">
                <tr>
                    <th class="text-left px-4 py-3">Auto</th>
                    <th class="text-left px-4 py-3">Prijs</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Tags</th>
                    <th class="text-left px-4 py-3">Views</th>
                    <th class="text-left px-4 py-3">Acties</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cars as $car)
                <tr class="border-t border-white/5">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $car->display_title }}</div>
                        <div class="text-xs text-gray-400">{{ $car->year ?? 'Onbekend' }} • {{ number_format($car->mileage, 0, ',', '.') }} km</div>
                    </td>
                    <td class="px-4 py-3">
                        <input data-id="{{ $car->id }}" type="number" class="price-input w-32 rounded bg-black/30 border border-white/10 px-3 py-2" value="{{ $car->price }}">
                    </td>
                    <td class="px-4 py-3">
                        <button data-id="{{ $car->id }}" data-status="{{ $car->status }}" class="status-btn px-3 py-2 rounded-lg border border-white/10 {{ $car->status === 'sold' ? 'bg-red-500/20 text-red-100' : 'bg-green-500/20 text-green-100' }}">
                            {{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1 text-xs">
                            @foreach ($car->tags->take(3) as $tag)
                            <span class="px-2 py-1 rounded bg-white/10 border border-white/10">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $car->views }}</td>
                    <td class="px-4 py-3 flex flex-wrap gap-2">
                        <a class="px-3 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15" href="{{ route('provider.cars.edit', $car) }}">Aanpassen</a>
                        <a class="px-3 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15" href="{{ route('provider.cars.pdf', $car) }}">PDF</a>
                        <form method="POST" action="{{ route('provider.cars.destroy', $car) }}" onsubmit="return confirm('Verwijder dit aanbod?')">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-2 rounded-lg bg-red-500/20 border border-red-500/40 text-red-100">Verwijder</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cars->links() }}
    </div>
</div>

<script>
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const priceInput = document.querySelector(`input.price-input[data-id="${id}"]`);
            fetch(`/provider/cars/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: btn.dataset.status === 'sold' ? 'available' : 'sold',
                        price: priceInput?.value || null,
                    })
                })
                .then(r => r.json())
                .then(data => {
                    btn.dataset.status = data.status;
                    btn.textContent = data.status === 'sold' ? 'Verkocht' : 'Beschikbaar';
                    btn.className = `status-btn px-3 py-2 rounded-lg border border-white/10 ${data.status === 'sold' ? 'bg-red-500/20 text-red-100' : 'bg-green-500/20 text-green-100'}`;
                    if (priceInput) priceInput.value = data.price.replaceAll('.', '');
                });
        });
    });
</script>
@endsection