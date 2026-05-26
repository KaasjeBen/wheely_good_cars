@php
use Illuminate\Support\Str;

$featuredIds = collect($cars->items())->pluck('id')->shuffle()->take(max(1, min(2, $cars->count())))->all();

$brandPalettes = [
'volvo' => [
'stripe' => 'from-sky-500 to-sky-300',
'pill' => ['bg' => 'bg-sky-50', 'border' => 'border-sky-100', 'text' => 'text-sky-900'],
'tag' => ['bg' => 'bg-sky-50', 'border' => 'border-sky-100', 'text' => 'text-sky-900'],
'placeholder' => 'from-sky-50 via-slate-50 to-white',
],
'bmw' => [
'stripe' => 'from-blue-600 to-blue-300',
'pill' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'text' => 'text-blue-900'],
'tag' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'text' => 'text-blue-900'],
'placeholder' => 'from-blue-50 via-slate-50 to-white',
],
'audi' => [
'stripe' => 'from-rose-500 to-rose-300',
'pill' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-100', 'text' => 'text-rose-900'],
'tag' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-100', 'text' => 'text-rose-900'],
'placeholder' => 'from-rose-50 via-stone-50 to-white',
],
'mercedes' => [
'stripe' => 'from-emerald-500 to-emerald-300',
'pill' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'text' => 'text-emerald-900'],
'tag' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'text' => 'text-emerald-900'],
'placeholder' => 'from-emerald-50 via-stone-50 to-white',
],
'toyota' => [
'stripe' => 'from-amber-500 to-amber-300',
'pill' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-100', 'text' => 'text-amber-900'],
'tag' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-100', 'text' => 'text-amber-900'],
'placeholder' => 'from-amber-50 via-stone-50 to-white',
],
'ford' => [
'stripe' => 'from-indigo-500 to-indigo-300',
'pill' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-100', 'text' => 'text-indigo-900'],
'tag' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-100', 'text' => 'text-indigo-900'],
'placeholder' => 'from-indigo-50 via-stone-50 to-white',
],
'default' => [
'stripe' => 'from-stone-400 to-stone-200',
'pill' => ['bg' => 'bg-stone-50', 'border' => 'border-stone-200', 'text' => 'text-stone-900'],
'tag' => ['bg' => 'bg-stone-50', 'border' => 'border-stone-200', 'text' => 'text-stone-900'],
'placeholder' => 'from-stone-50 via-white to-white',
],
];
@endphp
<div class="cars-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
    @forelse ($cars as $car)
    @php
    $isFeatured = in_array($car->id, $featuredIds);
    $key = strtolower($car->make ?? '');
    $palette = $brandPalettes[$key] ?? $brandPalettes['default'];
    @endphp
    <a href="{{ route('cars.show', $car) }}" class="car-card group relative overflow-hidden rounded-2xl border border-stone-200 bg-white p-5 flex flex-col gap-4 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg {{ $isFeatured ? 'lg:col-span-2 lg:row-span-2' : '' }}">
        <div class="car-stripe absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r {{ $palette['stripe'] }}"></div>
        <div class="car-header relative flex items-start justify-between gap-3">
            <div>
                <p class="car-make text-sm text-stone-600">{{ $car->make }}</p>
                <h2 class="car-title text-xl md:text-2xl font-semibold leading-snug text-stone-900">{{ $car->display_title }}</h2>
                <p class="car-meta text-sm text-stone-600">{{ $car->year ?? 'Onbekend bouwjaar' }} • {{ number_format($car->mileage, 0, ',', '.') }} km</p>
            </div>
            <span class="car-price px-3 py-2 rounded-md {{ $palette['pill']['bg'] }} {{ $palette['pill']['text'] }} {{ $palette['pill']['border'] }} border text-sm font-semibold shadow-[0_1px_2px_rgba(0,0,0,0.05)]">€ {{ number_format($car->price, 0, ',', '.') }}</span>
        </div>
        <div class="car-placeholder relative h-40 md:h-44 {{ $isFeatured ? 'lg:h-64' : '' }} rounded-xl border border-stone-200 overflow-hidden flex items-center justify-center bg-gradient-to-br {{ $palette['placeholder'] }}">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_30%_25%,rgba(0,0,0,0.08),transparent_35%),radial-gradient(circle_at_70%_30%,rgba(0,0,0,0.05),transparent_32%)]"></div>
            <span class="car-placeholder-mark relative text-4xl font-black text-stone-300">WG</span>
        </div>
        <div class="car-tags flex flex-wrap gap-2">
            @foreach ($car->tags->take(4) as $tag)
            <span class="car-tag px-3 py-1 rounded-full {{ $palette['tag']['bg'] }} {{ $palette['tag']['border'] }} text-xs {{ $palette['tag']['text'] }}">{{ $tag->name }}</span>
            @endforeach
            @if ($car->tags->count() > 4)
            <span class="car-tag px-3 py-1 rounded-full {{ $palette['tag']['bg'] }} {{ $palette['tag']['border'] }} text-xs {{ $palette['tag']['text'] }}">+{{ $car->tags->count() - 4 }}</span>
            @endif
        </div>
        <div class="car-footer flex items-center justify-between text-sm text-stone-700">
            <span class="car-views">Bekeken {{ $car->views }}x</span>
            <span class="car-link group-hover:text-amber-700 transition-colors">Bekijk details →</span>
        </div>
    </a>
    @empty
    <div class="col-span-full rounded-2xl border border-dashed border-stone-300 bg-stone-50 text-center py-12 text-stone-600">
        Geen resultaten gevonden. Pas je zoekopdracht of filters aan.
    </div>
    @endforelse
</div>