<?php

namespace Database\Factories;

use App\Department;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = (new \Faker\Factory())::create();

        return [
            'name' => $faker->jobTitle,
            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
        ];
    }
}
