<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Message;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\User::class)->create();
        },
        'receiver_id' => function () {
            return factory(\App\User::class)->create();
        },
        'message' => $faker->sentence,
        'is_archive' => rand(1, 2) % 2 == 0 ? true : false
    ];
});
