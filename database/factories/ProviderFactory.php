<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Provider;
use Faker\Generator as Faker;

$factory->define(Provider::class, function (Faker $faker) {
    return [
        'provider' => $faker->company() ,
        'nit' => $faker->randomDigit() ,
        'tel' => $faker->phoneNumber() ,
        'address' => $faker->streetAddress() ,
        'state' => $faker->boolean()
    ];
});
