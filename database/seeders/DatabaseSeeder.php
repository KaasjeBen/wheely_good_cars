<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tagNames = [
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
            'Cabrio',
            'Bedrijfswagen',
            'Lage km-stand',
            'Dealeronderhouden',
            'Sportief',
            'Zuinig',
        ];

        $tags = collect($tagNames)->map(function ($name) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        });

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'phone' => '010-0000000',
            'password' => Hash::make('admin123'),
        ]);

        $users = User::factory()->count(150)->create();

        $featuredUser = User::factory()->create([
            'name' => 'Test Aanbieder',
            'email' => 'test@example.com',
            'phone' => '0612345678',
        ]);

        $cars = Car::factory()
            ->count(250)
            ->make()
            ->each(function (Car $car) use ($users) {
                $car->user()->associate($users->random());
                $car->save();
            });

        $cars->each(function (Car $car) use ($tags) {
            $car->tags()->sync($tags->random(rand(2, 5))->pluck('id'));
        });

        Car::factory()
            ->count(12)
            ->for($featuredUser)
            ->create()
            ->each(function (Car $car) use ($tags) {
                $car->tags()->sync($tags->random(rand(3, 6))->pluck('id'));
            });
    }
}
