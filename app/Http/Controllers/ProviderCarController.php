<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class ProviderCarController extends Controller
{
    public function index()
    {
        $cars = Car::with('tags')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('provider.cars.index', compact('cars'));
    }

    public function create()
    {
        $tags = Tag::orderBy('name')->get();
        return view('provider.cars.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'license_plate' => ['nullable', 'string', 'max:20'],
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1950', 'max:' . date('Y')],
            'mileage' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,sold'],
            'description' => ['nullable', 'string'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $car = new Car($data);
        $car->user_id = Auth::id();
        $car->sold_at = $data['status'] === 'sold' ? now() : null;

        if ($request->hasFile('image')) {
            $car->image_path = $request->file('image')->store('cars', 'public');
        }

        $car->save();
        $car->tags()->sync($data['tags'] ?? []);

        return redirect()->route('provider.cars.index')->with('status', 'Auto aangemaakt.');
    }

    public function edit(Car $car)
    {
        $this->authorizeOwner($car);
        $tags = Tag::orderBy('name')->get();
        return view('provider.cars.edit', compact('car', 'tags'));
    }

    public function update(Request $request, Car $car)
    {
        $this->authorizeOwner($car);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,sold'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'description' => ['nullable', 'string'],
        ]);

        $car->fill($data);
        $car->sold_at = $data['status'] === 'sold' ? now() : null;
        if ($data['status'] === 'available') {
            $car->sold_at = null;
        }
        $car->save();
        $car->tags()->sync($data['tags'] ?? []);

        return back()->with('status', 'Aanbod bijgewerkt.');
    }

    public function updateStatus(Request $request, Car $car)
    {
        $this->authorizeOwner($car);

        $data = $request->validate([
            'status' => ['required', 'in:available,sold'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (isset($data['price'])) {
            $car->price = $data['price'];
        }
        $car->status = $data['status'];
        $car->sold_at = $data['status'] === 'sold' ? now() : null;
        if ($data['status'] === 'available') {
            $car->sold_at = null;
        }
        $car->save();

        return response()->json([
            'status' => $car->status,
            'price' => (float) $car->price,
            'price_label' => '€ ' . number_format($car->price, 0, ',', '.'),
        ]);
    }

    public function destroy(Car $car)
    {
        $this->authorizeOwner($car);
        $car->delete();
        return back()->with('status', 'Aanbod verwijderd.');
    }

    public function pdf(Car $car)
    {
        $this->authorizeOwner($car);
        if (!class_exists(Pdf::class)) {
            return back()->with('status', 'PDF pakket ontbreekt (barryvdh/laravel-dompdf).');
        }

        $pdf = Pdf::loadView('provider.cars.pdf', ['car' => $car->load('tags', 'user')]);
        return $pdf->download('auto-' . $car->id . '.pdf');
    }

    public function rdw(Request $request)
    {
        $plate = strtoupper(str_replace('-', '', $request->query('plate', '')));
        if ($plate === '') {
            return response()->json(['error' => 'Geen kenteken'], 400);
        }

        $ovi = Http::timeout(8)->get('https://ovi.rdw.nl/RestApi/VehicleSearch', [
            'Kenteken' => $plate,
        ]);

        if ($ovi->successful() && $ovi->json()) {
            $payload = $ovi->json();
            $vehicle = is_array($payload) && isset($payload[0]) ? $payload[0] : $payload;

            $make = $vehicle['Merk'] ?? $vehicle['merk'] ?? null;
            $model = $vehicle['Handelsbenaming'] ?? $vehicle['handelsbenaming'] ?? null;

            $yearRaw = $vehicle['DatumEersteToelating'] ?? $vehicle['DatumTenaamstelling'] ?? $vehicle['datum_tenaamstelling'] ?? null;
            $year = $yearRaw ? (int) substr((string) $yearRaw, 0, 4) : null;

            if ($make || $model || $year) {
                return [
                    'make' => $make,
                    'model' => $model,
                    'year' => $year,
                    'source' => 'ovi',
                ];
            }
        }

        $fallback = Http::timeout(8)->get('https://opendata.rdw.nl/resource/m9d7-ebf2.json', [
            'kenteken' => $plate,
        ]);

        if ($fallback->failed() || $fallback->json() === []) {
            return response()->json(['error' => 'Geen RDW match'], 404);
        }

        $data = $fallback->json()[0];

        return [
            'make' => $data['merk'] ?? null,
            'model' => $data['handelsbenaming'] ?? null,
            'year' => isset($data['datum_tenaamstelling']) ? (int) substr($data['datum_tenaamstelling'], 0, 4) : null,
            'source' => 'opendata',
        ];
    }

    private function authorizeOwner(Car $car): void
    {
        if ($car->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
