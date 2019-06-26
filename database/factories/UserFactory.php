<?php

use App\Entities\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(User::class, function (Faker $faker, array $attributes) {
    return [
        'uuid'      => Arr::get($attributes, 'uuid', $faker->uuid),
        'name'      => Arr::get($attributes, 'name', $faker->name),
        'email'     => Arr::get($attributes, 'email', $faker->email),
        'roles'     => Arr::get($attributes, 'roles', []),
        'fcm'       => Arr::get($attributes, 'admin', $faker->md5),
        'reward'    => Arr::get($attributes, 'reward', $faker->boolean),
        'imageUrl'  => Arr::get($attributes, 'imageUrl', $faker->url),
    ];
});
