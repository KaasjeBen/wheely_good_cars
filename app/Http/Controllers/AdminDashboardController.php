<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        $this->ensureAdmin();

        return view('admin.dashboard');
    }

    public function metrics()
    {
        $this->ensureAdmin();
        $today = Carbon::today();

        $totalCars = Car::count();
        $sold = Car::where('status', 'sold')->count();
        $todayOffered = Car::whereDate('created_at', $today)->count();
        $providers = User::where('role', 'provider')->count();
        $viewsToday = Car::whereDate('updated_at', $today)->sum('views');
        $avgPerProvider = $providers > 0 ? round($totalCars / $providers, 2) : 0;

        return [
            'totalCars' => $totalCars,
            'sold' => $sold,
            'todayOffered' => $todayOffered,
            'providers' => $providers,
            'viewsToday' => $viewsToday,
            'avgPerProvider' => $avgPerProvider,
        ];
    }

    public function tagUsage()
    {
        $this->ensureAdmin();

        $tags = Tag::withCount(['cars as total_usage', 'cars as sold_usage' => function ($q) {
            $q->where('status', 'sold');
        }])->get();

        return view('admin.tag-usage', compact('tags'));
    }

    public function suspicious()
    {
        $this->ensureAdmin();
        $now = Carbon::now();
        $oneYearAgo = $now->copy()->subYear();

        $users = User::where('role', 'provider')->get()->filter(function (User $user) use ($oneYearAgo) {
            $cars = $user->cars()->with('tags')->get();
            if ($cars->isEmpty()) {
                return false;
            }

            $reasons = [];
            if (!$user->phone) {
                $reasons[] = 'Geen telefoonnummer';
            }

            if ($cars->where('year', '<', now()->year - 15)->where('mileage', '<', 50000)->count() > 0) {
                $reasons[] = 'Oude auto met lage km-stand';
            }

            $sameDaySold = $cars->where('status', 'sold')->groupBy(fn($c) => optional($c->sold_at)->format('Y-m-d'))->filter(function ($group) {
                return $group->count() > 3 && $group->every(fn($c) => $c->price > 10000);
            });
            if ($sameDaySold->isNotEmpty()) {
                $reasons[] = 'Meerdere duurdere auto\'s dezelfde dag verkocht';
            }

            if ($cars->every(fn($c) => $c->price < 1000)) {
                $reasons[] = 'Alleen aanbiedingen onder €1000';
            }

            if ($cars->every(fn($c) => $c->tags->isEmpty())) {
                $reasons[] = 'Geen tags gebruikt';
            }

            $lastOffer = $cars->max('created_at');
            if (!$lastOffer || $lastOffer < $oneYearAgo) {
                $reasons[] = 'Al een jaar geen nieuwe auto aangeboden';
            }

            return count($reasons) > 0;
        })->map(function (User $user) use ($oneYearAgo) {
            $cars = $user->cars()->with('tags')->get();
            $reasons = [];
            if (!$user->phone) {
                $reasons[] = 'Geen telefoonnummer';
            }
            if ($cars->where('year', '<', now()->year - 15)->where('mileage', '<', 50000)->count() > 0) {
                $reasons[] = 'Oude auto met lage km-stand';
            }
            $sameDaySold = $cars->where('status', 'sold')->groupBy(fn($c) => optional($c->sold_at)->format('Y-m-d'))->filter(function ($group) {
                return $group->count() > 3 && $group->every(fn($c) => $c->price > 10000);
            });
            if ($sameDaySold->isNotEmpty()) {
                $reasons[] = 'Meerdere duurdere auto\'s dezelfde dag verkocht';
            }
            if ($cars->every(fn($c) => $c->price < 1000)) {
                $reasons[] = 'Alleen aanbiedingen onder €1000';
            }
            if ($cars->every(fn($c) => $c->tags->isEmpty())) {
                $reasons[] = 'Geen tags gebruikt';
            }
            $lastOffer = $cars->max('created_at');
            if (!$lastOffer || $lastOffer < $oneYearAgo) {
                $reasons[] = 'Al een jaar geen nieuwe auto aangeboden';
            }

            return [
                'user' => $user,
                'cars' => $cars,
                'reasons' => $reasons,
            ];
        });

        return view('admin.suspicious', ['providers' => $users]);
    }

    private function ensureAdmin(): void
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
    }
}
