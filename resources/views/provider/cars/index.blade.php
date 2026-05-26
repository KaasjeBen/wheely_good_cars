@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="pill w-fit">Mijn aanbiedingen</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">Beheer je aanbod</h1>
            <p class="mt-2 text-stone-600">Wijzig prijs of status direct, zonder page reload.</p>
        </div>
        <a href="{{ route('provider.cars.create') }}" class="btn btn-primary">Nieuwe auto</a>
    </div>

    @if (session('status'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">{{ session('status') }}</div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-stone-600">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Auto</th>
                    <th class="px-4 py-3 text-left font-semibold">Prijs</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Tags</th>
                    <th class="px-4 py-3 text-left font-semibold">Views</th>
                    <th class="px-4 py-3 text-left font-semibold">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                @foreach ($cars as $car)
                <tr class="align-top">
                    <td class="px-4 py-4">
                        <div class="font-semibold text-stone-900">{{ $car->display_title }}</div>
                        <div class="mt-1 text-xs text-stone-500">{{ $car->license_plate ?? 'Geen kenteken' }} • {{ $car->year ?? 'Onbekend' }} • {{ number_format($car->mileage, 0, ',', '.') }} km</div>
                    </td>
                    <td class="px-4 py-4">
                        <label class="sr-only" for="price-{{ $car->id }}">Vraagprijs</label>
                        <input id="price-{{ $car->id }}" data-role="price" data-id="{{ $car->id }}" type="number" min="0" step="1" class="w-36 rounded-lg border-stone-300 bg-stone-50 px-3 py-2 focus:border-stone-400 focus:ring-2 focus:ring-stone-200" value="{{ (int) $car->price }}">
                    </td>
                    <td class="px-4 py-4">
                        <button type="button" data-role="status" data-id="{{ $car->id }}" data-status="{{ $car->status }}" class="inline-flex items-center rounded-full border px-3 py-2 text-sm font-medium transition {{ $car->status === 'sold' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                            {{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}
                        </button>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-2">
                            @forelse ($car->tags->take(4) as $tag)
                            <span class="pill">{{ $tag->name }}</span>
                            @empty
                            <span class="text-sm text-stone-500">Geen tags</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-4 text-stone-700">{{ $car->views }}</td>
                    <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-2">
                            <a class="btn" href="{{ route('provider.cars.edit', $car) }}">Aanpassen</a>
                            <a class="btn" href="{{ route('provider.cars.pdf', $car) }}">PDF</a>
                            <form method="POST" action="{{ route('provider.cars.destroy', $car) }}" onsubmit="return confirm('Verwijder dit aanbod?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn" type="submit">Verwijder</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $cars->links() }}
    </div>
</section>

<script>
    (() => {
        const token = '{{ csrf_token() }}';

        const saveRow = async (id, status, priceInput, statusButton) => {
            const payload = {
                status,
                price: priceInput?.value || null,
            };

            statusButton.disabled = true;
            if (priceInput) {
                priceInput.disabled = true;
            }

            try {
                const response = await fetch(`{{ url('/provider/cars') }}/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    throw new Error('Opslaan mislukt');
                }

                const data = await response.json();
                statusButton.dataset.status = data.status;
                statusButton.innerHTML = data.status === 'sold' ? 'Verkocht' : 'Beschikbaar';
                statusButton.className = `inline-flex items-center rounded-full border px-3 py-2 text-sm font-medium transition ${data.status === 'sold' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;

                if (priceInput && typeof data.price === 'number') {
                    priceInput.value = data.price;
                }
            } catch (error) {
                window.alert(error.message || 'Opslaan mislukt');
            } finally {
                statusButton.disabled = false;
                if (priceInput) {
                    priceInput.disabled = false;
                }
            }
        };

        document.querySelectorAll('[data-role="status"]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const priceInput = document.querySelector(`[data-role="price"][data-id="${id}"]`);
                const nextStatus = button.dataset.status === 'sold' ? 'available' : 'sold';
                saveRow(id, nextStatus, priceInput, button);
            });
        });

        document.querySelectorAll('[data-role="price"]').forEach((input) => {
            const persist = () => {
                const id = input.dataset.id;
                const statusButton = document.querySelector(`[data-role="status"][data-id="${id}"]`);
                if (!statusButton) {
                    return;
                }

                saveRow(id, statusButton.dataset.status, input, statusButton);
            };

            input.addEventListener('blur', persist);
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    persist();
                }
            });
        });
    })();
</script>
@endsection