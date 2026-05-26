<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tags = collect([
            'Hybride',
            'Elektrisch',
            'Benzine',
            'Diesel',
            'Automaat',
            'Handgeschakeld',
            'Trekhaak',
            'Panoramadak',
            'Leder',
            'Stoelverwarming',
            'Navigatie',
            'Parkeersensoren',
            'Camera',
            'Cruise control',
            'Airco',
            'Youngtimer',
            'Compact',
            'Familiewagen',
            'Stadsauto',
            'SUV',
        ])->map(function (string $name) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        })->values();

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'phone' => '010-0000000',
                'password' => Hash::make('admin123'),
            ]
        );

        $providers = User::factory()
            ->count(150)
            ->create()
            ->values();

        $specialProviders = $providers->take(6);

        $this->seedSuspiciousProviders($specialProviders, $tags);
        $this->seedGeneralInventory($providers->slice(6)->values(), $tags, 250);

        $featuredUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Aanbieder',
                'phone' => '0612345678',
                'role' => 'provider',
                'password' => Hash::make('password'),
            ]
        );

        Car::factory()
            ->count(12)
            ->for($featuredUser)
            ->create()
            ->each(function (Car $car) use ($tags) {
                $car->tags()->sync($tags->random(rand(3, 6))->pluck('id'));
            });
    }

    private function seedGeneralInventory(Collection $providers, Collection $tags, int $count): void
    {
        Car::factory()
            ->count($count)
            ->make()
            ->each(function (Car $car) use ($providers, $tags): void {
                $car->user()->associate($providers->random());
                $car->save();
                $car->tags()->sync($tags->random(rand(1, 4))->pluck('id'));
            });
    }

    private function seedSuspiciousProviders(Collection $providers, Collection $tags): void
    {
        $now = now();

        $providers->values()->each(function (User $provider, int $index) use ($tags, $now): void {
            if ($index === 0) {
                $provider->forceFill(['phone' => null])->save();
                $this->createCars($provider, $tags, [
                    ['price' => 12950, 'year' => $now->year - 9, 'mileage' => 84000, 'status' => 'available'],
                    ['price' => 15450, 'year' => $now->year - 8, 'mileage' => 92000, 'status' => 'available'],
                ]);
                return;
            }

            if ($index === 1) {
                $this->createCars($provider, $tags, [
                    ['price' => 13950, 'year' => $now->year - 18, 'mileage' => 16800, 'status' => 'available'],
                    ['price' => 14950, 'year' => $now->year - 17, 'mileage' => 22400, 'status' => 'available'],
                ]);
                return;
            }

            if ($index === 2) {
                $this->createCars($provider, $tags, [
                    ['price' => 18950, 'year' => $now->year - 7, 'mileage' => 68000, 'status' => 'sold', 'sold_at' => $now],
                    ['price' => 19750, 'year' => $now->year - 7, 'mileage' => 69500, 'status' => 'sold', 'sold_at' => $now],
                    ['price' => 20500, 'year' => $now->year - 6, 'mileage' => 71000, 'status' => 'sold', 'sold_at' => $now],
                    ['price' => 21250, 'year' => $now->year - 6, 'mileage' => 72500, 'status' => 'sold', 'sold_at' => $now],
                ]);
                return;
            }

            if ($index === 3) {
                $this->createCars($provider, $tags, [
                    ['price' => 950, 'year' => $now->year - 15, 'mileage' => 184000, 'status' => 'available'],
                    ['price' => 799, 'year' => $now->year - 13, 'mileage' => 162000, 'status' => 'available'],
                    ['price' => 650, 'year' => $now->year - 11, 'mileage' => 149000, 'status' => 'available'],
                ]);
                return;
            }

            if ($index === 4) {
                $this->createCars($provider, $tags, [
                    ['price' => 11950, 'year' => $now->year - 10, 'mileage' => 99000, 'status' => 'available', 'tags' => []],
                    ['price' => 10950, 'year' => $now->year - 12, 'mileage' => 123000, 'status' => 'available', 'tags' => []],
                ]);
                return;
            }

            if ($index === 5) {
                $oldDate = $now->copy()->subYears(2);
                $this->createCars($provider, $tags, [
                    ['price' => 16950, 'year' => $now->year - 6, 'mileage' => 76000, 'status' => 'available', 'created_at' => $oldDate, 'updated_at' => $oldDate],
                    ['price' => 15950, 'year' => $now->year - 5, 'mileage' => 82000, 'status' => 'available', 'created_at' => $oldDate, 'updated_at' => $oldDate],
                ]);
            }
        });
    }

    private function createCars(User $provider, Collection $tags, array $definitions): void
    {
        foreach ($definitions as $definition) {
            $car = Car::factory()->for($provider)->create([
                'price' => $definition['price'],
                'year' => $definition['year'],
                'mileage' => $definition['mileage'],
                'status' => $definition['status'],
                'sold_at' => $definition['sold_at'] ?? null,
            ]);

            if (isset($definition['created_at']) || isset($definition['updated_at'])) {
                $car->forceFill([
                    'created_at' => $definition['created_at'] ?? $car->created_at,
                    'updated_at' => $definition['updated_at'] ?? $car->updated_at,
                ])->saveQuietly();
            }

            if (($definition['tags'] ?? null) === []) {
                $car->tags()->sync([]);
                continue;
            }

            $tagCount = $definition['tag_count'] ?? 2;
            $car->tags()->sync($tags->random(max(1, $tagCount))->pluck('id'));
        }
    }
}
