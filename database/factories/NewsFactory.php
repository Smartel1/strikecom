<?php

use App\Entities\News;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(News::class, function (Faker $faker, array $attributes) {
    return [
        'title_ru'     => Arr::get($attributes, 'title_ru', $faker->word),
        'title_en'     => Arr::get($attributes, 'title_en', $faker->word),
        'title_es'     => Arr::get($attributes, 'title_es', $faker->word),
        'content_ru'   => Arr::get($attributes, 'content_ru', $faker->word),
        'content_en'   => Arr::get($attributes, 'content_en', $faker->word),
        'content_es'   => Arr::get($attributes, 'content_es', $faker->word),
        'date'         => Arr::get($attributes, 'date', $faker->randomNumber(5)),
        'source_link'  => Arr::get($attributes, 'source_link', $faker->url),
    ];
});
