<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition(): array
    {
        $makes = [
            'Volvo',
            'BMW',
            'Audi',
            'Mercedes',
            'Volkswagen',
            'Peugeot',
            'Renault',
            'Opel',
            'Toyota',
            'Ford',
            'Kia',
            'Hyundai',
            'Mazda',
            'Skoda',
            'Seat',
            'Honda',
            'Nissan',
            'Citroen',
            'Mini',
            'Fiat',
        ];

        $make = $this->faker->randomElement($makes);
        $model = $this->faker->word();
        $year = $this->faker->numberBetween(2005, 2024);
        $mileage = $this->faker->numberBetween(20000, 240000);
        $price = $this->faker->numberBetween(1500, 55000);
        $license = strtoupper($this->faker->bothify('??-##-??'));
        $isSold = $this->faker->boolean(20);

        return [
            'user_id' => User::factory(),
            'title' => null,
            'make' => $make,
            'model' => ucfirst($model),
            'year' => $year,
            'mileage' => $mileage,
            'price' => $price,
            'status' => $isSold ? 'sold' : 'available',
            'sold_at' => $isSold ? $this->faker->dateTimeBetween('-6 months', 'now') : null,
            'description' => $this->faker->paragraphs(asText: true),
            'views' => $this->faker->numberBetween(0, 900),
            'license_plate' => $license,
            'image_path' => null,
        ];
    }
}
