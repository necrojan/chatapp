<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CannedResponse;
use Faker\Generator as Faker;

$factory->define(CannedResponse::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\User::class)->create();
        },
        'key' => $faker->asciify('*******'),
        'message' => $faker->sentence,
        'is_personal' => rand(0, 1) % 2 == 0 ? true : false
    ];
});
