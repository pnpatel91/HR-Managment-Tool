<?php

namespace Database\Factories;

use App\Branch;
use App\Company;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = (new \Faker\Factory())::create();

        return [
            'name' => $faker->name,
            'address' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'postcode' => $faker->postcode,
            'country' => $faker->country,
            //'latitude' => $faker->latitude($min = -90, $max = 90),
            //'longitude' => $faker->longitude($min = -180, $max = 180),
            'latitude' => '51.542411',
            'longitude' => '-0.2708499',
            'radius' => 1000,
            'company_id' => Company::inRandomOrder()->first()->id,
            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
