<?php

use App\Entities\ClientVersion;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(ClientVersion::class, function (Faker $faker, array $attributes) {
    return [
        'required' => Arr::get($attributes, 'required', false),
        'version' => Arr::get($attributes, 'version', '1'),
        'client_id' => Arr::get($attributes, 'client_id', 'android'),
        'description_ru' => Arr::get($attributes, 'description_ru', $faker->word),
        'description_en' => Arr::get($attributes, 'description_en', $faker->word),
        'description_es' => Arr::get($attributes, 'description_es', $faker->word),
    ];
});
