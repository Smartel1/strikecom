<?php

use App\Entities\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(User::class, function (Faker $faker, array $attributes) {
    return [
        'uuid'      => Arr::get($attributes, 'uuid', $faker->uuid),
        'name'      => Arr::get($attributes, 'name', $faker->name),
        'email'     => Arr::get($attributes, 'email', $faker->email),
        'admin'     => Arr::get($attributes, 'admin', true),
        'fcm'       => Arr::get($attributes, 'admin', $faker->word),
        'push'      => Arr::get($attributes, 'admin', $faker->boolean),
        'reward'    => Arr::get($attributes, 'admin', $faker->boolean),
        'imageUrl'  => Arr::get($attributes, 'admin', $faker->url),
    ];
});
