@extends('layouts.app')

@section('content')
<section class="section-surface space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="pill w-fit">Nieuwe aanbieding</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">Multistep formulier</h1>
            <p class="mt-2 text-stone-600">Start met alleen het kenteken. De RDW-gegevens vullen we daarna automatisch aan.</p>
        </div>
        <a href="{{ route('provider.cars.index') }}" class="btn">Terug</a>
    </div>

    <div class="rounded-full border border-stone-200 bg-stone-100 p-1">
        <div id="progress" class="h-2 rounded-full bg-stone-900 transition-all duration-300" style="width: 25%"></div>
    </div>

    <div class="grid gap-3 md:grid-cols-4">
        @foreach ([['Kenteken','Start'],['Details','RDW aanvullen'],['Prijs & status','Klaarmaken'],['Tags & foto','Afronden']] as $index => $step)
        <div class="stepper-item rounded-2xl border border-stone-200 bg-white p-4 shadow-sm" data-step-label="{{ $index }}">
            <div class="flex items-center gap-3">
                <div data-step-circle class="flex h-9 w-9 items-center justify-center rounded-full border border-stone-200 bg-stone-50 text-sm font-semibold text-stone-700">{{ $index + 1 }}</div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-stone-500">Stap {{ $index + 1 }}</p>
                    <p data-step-title class="text-sm font-semibold text-stone-900">{{ $step[0] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if ($errors->any())
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900">
        <p class="font-semibold">Er ging iets mis</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div id="step-message" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900"></div>

    <form id="car-form" method="POST" action="{{ route('provider.cars.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="step rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" data-step="0">
            <h2 class="text-lg font-semibold text-stone-900">Kenteken</h2>
            <p class="mt-1 text-sm text-stone-600">Voer alleen het kenteken in en laat de RDW de basisinvulling doen.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto]">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="license">Kenteken</label>
                    <input type="text" name="license_plate" id="license" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3 focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="XX-99-YY">
                </div>
                <div class="flex items-end">
                    <button type="button" id="rdw-btn" class="btn btn-primary h-[46px] w-full md:w-auto">RDW ophalen</button>
                </div>
            </div>
            <p id="rdw-status" class="mt-3 text-sm text-stone-600"></p>
        </div>

        <div class="step hidden rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" data-step="1">
            <h2 class="text-lg font-semibold text-stone-900">Details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="make">Merk</label>
                    <input type="text" name="make" id="make" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="model">Model</label>
                    <input type="text" name="model" id="model" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="year">Bouwjaar</label>
                    <input type="number" name="year" id="year" min="1950" max="{{ date('Y') }}" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="mileage">Kilometerstand</label>
                    <input type="number" name="mileage" id="mileage" min="0" required class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3">
                </div>
            </div>
        </div>

        <div class="step hidden rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" data-step="2">
            <h2 class="text-lg font-semibold text-stone-900">Prijs & status</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="price">Vraagprijs (€)</label>
                    <input type="number" name="price" id="price" min="0" required class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-stone-700" for="status">Status</label>
                    <select name="status" id="status" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3">
                        <option value="available">Beschikbaar</option>
                        <option value="sold">Verkocht</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="mb-2 block text-sm font-semibold text-stone-700" for="description">Omschrijving</label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-stone-300 bg-stone-50 px-4 py-3"></textarea>
            </div>
        </div>

        <div class="step hidden rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" data-step="3">
            <h2 class="text-lg font-semibold text-stone-900">Tags & foto</h2>
            <p class="mt-1 text-sm text-stone-600">Kies tags als laatste stap. Foto uploaden mag nu al, zodat de auto direct compleet is.</p>
            <div class="mt-4">
                <p class="mb-2 text-sm font-semibold text-stone-700">Tags</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                    <label class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-stone-50 px-3 py-2 text-sm cursor-pointer select-none hover:bg-white">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="accent-stone-900">
                        <span>{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="mt-5">
                <label class="mb-2 block text-sm font-semibold text-stone-700" for="image">Foto upload</label>
                <input type="file" name="image" id="image" accept="image/*" class="block w-full rounded-lg border border-stone-300 bg-stone-50 px-4 py-3 text-sm">
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <button type="button" id="prev" class="btn">Vorige</button>
            <button type="button" id="next" class="btn btn-primary">Volgende</button>
            <button type="submit" id="submit" class="btn btn-primary hidden">Opslaan</button>
        </div>
    </form>
</section>

<script>
    (() => {
        const steps = Array.from(document.querySelectorAll('.step'));
        const stepLabels = Array.from(document.querySelectorAll('[data-step-label]'));
        const progress = document.querySelector('#progress');
        const prevBtn = document.querySelector('#prev');
        const nextBtn = document.querySelector('#next');
        const submitBtn = document.querySelector('#submit');
        const stepMessage = document.querySelector('#step-message');
        const licenseInput = document.querySelector('#license');
        const rdwBtn = document.querySelector('#rdw-btn');
        const rdwStatus = document.querySelector('#rdw-status');
        const makeInput = document.querySelector('#make');
        const modelInput = document.querySelector('#model');
        const yearInput = document.querySelector('#year');
        const mileageInput = document.querySelector('#mileage');
        const priceInput = document.querySelector('#price');
        const statusSelect = document.querySelector('#status');
        let current = 0;

        const setStepMessage = (message) => {
            stepMessage.textContent = message;
            stepMessage.classList.toggle('hidden', !message);
        };

        const paintStepper = () => {
            stepLabels.forEach((label, index) => {
                const circle = label.querySelector('[data-step-circle]');
                const isActive = index === current;
                const isDone = index < current;

                label.classList.toggle('border-stone-900', isActive);
                label.classList.toggle('bg-stone-50', isActive || isDone);

                if (isActive) {
                    circle.className = 'flex h-9 w-9 items-center justify-center rounded-full border border-stone-900 bg-stone-900 text-sm font-semibold text-white';
                } else if (isDone) {
                    circle.className = 'flex h-9 w-9 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 text-sm font-semibold text-emerald-700';
                } else {
                    circle.className = 'flex h-9 w-9 items-center justify-center rounded-full border border-stone-200 bg-stone-50 text-sm font-semibold text-stone-700';
                }
            });
        };

        const updateStep = () => {
            steps.forEach((step, index) => step.classList.toggle('hidden', index !== current));
            progress.style.width = `${((current + 1) / steps.length) * 100}%`;
            prevBtn.classList.toggle('invisible', current === 0);
            nextBtn.classList.toggle('hidden', current === steps.length - 1);
            submitBtn.classList.toggle('hidden', current !== steps.length - 1);
            paintStepper();
            setStepMessage('');

            const firstField = steps[current].querySelector('input, select, textarea');
            if (firstField) {
                firstField.focus();
            }
        };

        const goTo = (index) => {
            current = Math.min(Math.max(index, 0), steps.length - 1);
            updateStep();
        };

        const validateStep = () => {
            const missing = [];

            if (current === 1) {
                if (!makeInput.value.trim()) missing.push('Merk');
                if (!modelInput.value.trim()) missing.push('Model');
                if (!mileageInput.value.trim()) missing.push('Kilometerstand');
            }

            if (current === 2) {
                if (!priceInput.value.trim()) missing.push('Vraagprijs');
                if (!statusSelect.value.trim()) missing.push('Status');
            }

            if (missing.length) {
                setStepMessage(`Vul in: ${missing.join(', ')}`);
                return false;
            }

            return true;
        };

        const fillFromPlate = async () => {
            const plate = licenseInput.value.trim();

            if (!plate) {
                rdwStatus.textContent = 'Vul eerst een kenteken in.';
                return;
            }

            rdwStatus.textContent = 'RDW-gegevens ophalen...';
            rdwBtn.disabled = true;

            try {
                const response = await fetch(`{{ route('provider.rdw.lookup') }}?plate=${encodeURIComponent(plate)}`, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await response.json();

                if (!response.ok || data.error) {
                    throw new Error(data.error || 'Geen match gevonden');
                }

                if (data.make) makeInput.value = data.make;
                if (data.model) modelInput.value = data.model;
                if (data.year) yearInput.value = data.year;

                rdwStatus.textContent = data.source === 'ovi' ? 'OVI-match gevonden.' : 'RDW-match gevonden.';
                goTo(1);
            } catch (error) {
                rdwStatus.textContent = 'Geen match gevonden. Controleer het kenteken.';
            } finally {
                rdwBtn.disabled = false;
            }
        };

        nextBtn.addEventListener('click', () => {
            if (!validateStep()) {
                return;
            }

            goTo(current + 1);
        });

        prevBtn.addEventListener('click', () => goTo(current - 1));
        rdwBtn.addEventListener('click', fillFromPlate);
        licenseInput.addEventListener('blur', () => licenseInput.value.trim() && fillFromPlate());
        licenseInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                fillFromPlate();
            }
        });

        updateStep();
    })();
</script>
@endsection