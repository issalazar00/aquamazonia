<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Warehouse;
use Faker\Generator as Faker;

$factory->define(Warehouse::class, function (Faker $faker) {
    return [
        'warehouse' => $faker->word(),
        'description' => $faker->sentence(),
        'state' => $faker->boolean()
    ];
});
