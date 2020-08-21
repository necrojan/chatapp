<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Pool;
use Faker\Generator as Faker;

$factory->define(Pool::class, function (Faker $faker) {
    return [
        'client_id' => function () {
            return factory(\App\Client::class)->create();
        }
    ];
});
