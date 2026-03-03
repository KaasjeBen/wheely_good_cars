<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tag;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->toString();
        $tagIds = collect($request->input('tags', []))->map(fn($id) => (int) $id)->filter()->values()->all();

        $cars = Car::with(['tags', 'user'])
            ->available()
            ->search($search)
            ->withTags($tagIds)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $tags = Tag::orderBy('name')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'grid' => view('cars.partials.grid', compact('cars'))->render(),
                'pagination' => view('cars.partials.pagination', compact('cars'))->render(),
                'count' => $cars->total(),
            ]);
        }

        return view('cars.index', [
            'cars' => $cars,
            'tags' => $tags,
            'search' => $search,
            'activeTags' => $tagIds,
        ]);
    }

    public function show(Car $car)
    {
        $car->increment('views');

        return view('cars.show', [
            'car' => $car->load(['tags', 'user']),
        ]);
    }
}
