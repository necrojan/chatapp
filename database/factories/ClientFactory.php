<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\User::class)->create();
        },
        'machine' => $faker->asciify('*******'),
        'is_verified' => rand(1, 2) % 2 == 0 ? true : false,
        'code' => mt_rand(111111, 999999)
    ];
});
