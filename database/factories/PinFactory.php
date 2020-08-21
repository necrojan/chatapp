<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Pin;
use App\User;
use Faker\Generator as Faker;

$factory->define(Pin::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create();
        },
        'text' => $faker->sentence
    ];
});
