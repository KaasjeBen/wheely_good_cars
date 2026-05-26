<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarView;
use Illuminate\Support\Facades\Schema;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        $this->ensureAdmin();

        return view('admin.dashboard', [
            'metrics' => $this->buildMetrics(),
        ]);
    }

    public function metrics()
    {
        $this->ensureAdmin();

        return $this->buildMetrics();
    }

    public function tagUsage()
    {
        $this->ensureAdmin();

        $tags = Tag::query()
            ->withCount([
                'cars as total_usage',
                'cars as sold_usage' => fn($query) => $query->where('status', 'sold'),
                'cars as available_usage' => fn($query) => $query->where('status', 'available'),
            ])
            ->orderByDesc('total_usage')
            ->orderBy('name')
            ->get();

        return view('admin.tag-usage', compact('tags'));
    }

    public function suspicious()
    {
        $this->ensureAdmin();
        $providers = User::where('role', 'provider')
            ->with(['cars.tags'])
            ->get()
            ->map(function (User $user) {
                $cars = $user->cars;
                $reasons = $this->suspiciousReasons($user, $cars);

                if (count($reasons) === 0) {
                    return null;
                }

                return [
                    'user' => $user,
                    'cars' => $cars,
                    'reasons' => $reasons,
                    'reason_count' => count($reasons),
                ];
            })
            ->filter()
            ->sortByDesc('reason_count')
            ->values();

        return view('admin.suspicious', ['providers' => $providers]);
    }

    private function buildMetrics(): array
    {
        $today = Carbon::today();
        $providers = User::where('role', 'provider')->count();
        $totalCars = Car::count();
        $sold = Car::where('status', 'sold')->count();
        $available = max(0, $totalCars - $sold);
        $todayOffered = Car::whereDate('created_at', $today)->count();
        $viewsToday = Schema::hasTable('car_views') ? CarView::whereDate('created_at', $today)->count() : 0;
        $avgPerProvider = $providers > 0 ? round($totalCars / $providers, 2) : 0;

        $dailyViews = collect(range(6, 0))->map(function (int $offset) {
            $date = Carbon::today()->subDays($offset);

            return [
                'label' => $date->format('D'),
                'views' => Schema::hasTable('car_views') ? CarView::whereDate('created_at', $date)->count() : 0,
                'offers' => Car::whereDate('created_at', $date)->count(),
            ];
        });

        return [
            'totalCars' => $totalCars,
            'sold' => $sold,
            'available' => $available,
            'todayOffered' => $todayOffered,
            'providers' => $providers,
            'viewsToday' => $viewsToday,
            'avgPerProvider' => $avgPerProvider,
            'dailyViews' => $dailyViews,
        ];
    }

    private function suspiciousReasons(User $user, Collection $cars): array
    {
        $reasons = [];
        $oneYearAgo = Carbon::now()->subYear();

        if (!$user->phone) {
            $reasons[] = 'Geen telefoonnummer';
        }

        if ($cars->contains(fn($car) => (int) $car->year <= now()->year - 15 && (int) $car->mileage < 50000)) {
            $reasons[] = 'Oude auto met lage km-stand';
        }

        $sameDaySold = $cars->where('status', 'sold')->filter(function ($car) {
            return $car->sold_at && $car->created_at && $car->sold_at->isSameDay($car->created_at) && $car->price > 10000;
        })->groupBy(fn($car) => $car->sold_at?->format('Y-m-d'));

        if ($sameDaySold->contains(fn($group) => $group->count() > 3)) {
            $reasons[] = 'Meer dan 3 auto\'s direct verkocht';
        }

        if ($cars->isNotEmpty() && $cars->every(fn($car) => (float) $car->price < 1000)) {
            $reasons[] = 'Alleen aanbiedingen onder €1000';
        }

        if ($cars->isNotEmpty() && $cars->every(fn($car) => $car->tags->isEmpty())) {
            $reasons[] = 'Geen tags gebruikt';
        }

        $lastOffer = $cars->max('created_at');
        if (!$lastOffer || Carbon::parse($lastOffer)->lt($oneYearAgo)) {
            $reasons[] = 'Al een jaar geen nieuwe auto aangeboden';
        }

        return $reasons;
    }

    private function ensureAdmin(): void
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }
    }
}
