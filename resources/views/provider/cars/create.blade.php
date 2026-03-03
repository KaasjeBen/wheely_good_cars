@extends('layouts.app')

@section('content')
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-500/10 backdrop-blur">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-orange-300 uppercase tracking-[0.3em] mb-2">Nieuwe aanbieding</p>
            <h1 class="text-3xl font-semibold leading-tight">Multistep formulier</h1>
            <p class="text-gray-300">Kenteken lookup (RDW), prijs, tags en foto upload. Progressbar voor voortgang.</p>
        </div>
        <a href="{{ route('provider.cars.index') }}" class="px-4 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15">Terug</a>
    </div>

    <div class="w-full bg-white/10 rounded-full h-3 mb-6">
        <div id="progress" class="h-3 rounded-full bg-gradient-to-r from-orange-400 to-pink-500" style="width: 25%"></div>
    </div>

    @if ($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/15 border border-red-500/30 text-red-100">
        <p class="font-semibold">Er ging iets mis:</p>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="car-form" method="POST" action="{{ route('provider.cars.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="step" data-step="1">
            <h2 class="text-xl font-semibold mb-3">Stap 1: Kenteken lookup</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Kenteken</label>
                    <div class="flex gap-2">
                        <input type="text" name="license_plate" id="license" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400/70" placeholder="XX-99-YY">
                        <button type="button" id="rdw-btn" class="px-4 py-3 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15">RDW</button>
                    </div>
                </div>
                <div class="text-sm text-gray-300">Na lookup vullen we merk/model/jaar als beschikbaar.</div>
            </div>
        </div>

        <div class="step hidden" data-step="2">
            <h2 class="text-xl font-semibold mb-3">Stap 2: Details</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Merk</label>
                    <input type="text" name="make" id="make" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Model</label>
                    <input type="text" name="model" id="model" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Bouwjaar</label>
                    <input type="number" name="year" id="year" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3" min="1950" max="{{ date('Y') }}">
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Kilometerstand</label>
                    <input type="number" name="mileage" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3" min="0" required>
                </div>
            </div>
        </div>

        <div class="step hidden" data-step="3">
            <h2 class="text-xl font-semibold mb-3">Stap 3: Prijs en status</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Vraagprijs (€)</label>
                    <input type="number" name="price" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3" min="0" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3">
                        <option value="available">Beschikbaar</option>
                        <option value="sold">Verkocht</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-200 mb-1">Omschrijving</label>
                <textarea name="description" rows="3" class="w-full rounded-lg bg-black/30 border border-white/10 px-4 py-3"></textarea>
            </div>
        </div>

        <div class="step hidden" data-step="4">
            <h2 class="text-xl font-semibold mb-3">Stap 4: Tags en foto</h2>
            <div>
                <p class="text-sm text-gray-300 mb-2">Selecteer tags</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-sm cursor-pointer select-none">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="accent-orange-400">
                        <span>{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm text-gray-200 mb-1">Foto upload (optioneel)</label>
                <input type="file" name="image" accept="image/*" class="text-sm text-gray-300">
            </div>
        </div>

        <div class="flex justify-between mt-4">
            <button type="button" id="prev" class="px-4 py-2 rounded-lg bg-white/10 border border-white/10 hover:bg-white/15">Vorige</button>
            <button type="button" id="next" class="px-4 py-2 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400">Volgende</button>
            <button type="submit" id="submit" class="hidden px-4 py-2 rounded-lg bg-green-500 text-black font-semibold hover:bg-green-400">Opslaan</button>
        </div>
    </form>
</div>

<script>
    const steps = Array.from(document.querySelectorAll('.step'));
    let current = 0;

    const updateStep = () => {
        steps.forEach((el, idx) => el.classList.toggle('hidden', idx !== current));
        document.querySelector('#progress').style.width = `${((current + 1) / steps.length) * 100}%`;
        document.querySelector('#prev').classList.toggle('hidden', current === 0);
        document.querySelector('#next').classList.toggle('hidden', current === steps.length - 1);
        document.querySelector('#submit').classList.toggle('hidden', current !== steps.length - 1);
    };

    document.querySelector('#next').addEventListener('click', () => {
        current = Math.min(current + 1, steps.length - 1);
        updateStep();
    });
    document.querySelector('#prev').addEventListener('click', () => {
        current = Math.max(current - 1, 0);
        updateStep();
    });
    updateStep();

    document.querySelector('#rdw-btn').addEventListener('click', () => {
        const plate = document.querySelector('#license').value;
        if (!plate) return;
        fetch(`{{ route('provider.rdw.lookup') }}?plate=${encodeURIComponent(plate)}`)
            .then(r => r.json())
            .then(data => {
                if (data.error) return;
                if (data.make) document.querySelector('#make').value = data.make;
                if (data.model) document.querySelector('#model').value = data.model;
                if (data.year) document.querySelector('#year').value = data.year;
            })
            .catch(() => {});
    });
</script>
@endsection