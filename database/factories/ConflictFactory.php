<?php

use Faker\Generator as Faker;
use Illuminate\Support\Arr;
use LaravelDoctrine\ORM\Facades\EntityManager;

$factory->define(\App\Entities\Conflict::class, function (Faker $faker, array $attributes) {

    //в $attributes приходят id связей. Здесь их превращаем в прокси-объекты
    $conflictReason = Arr::has($attributes, 'conflict_reason_id')
        ? EntityManager::getReference(
            'App\Entities\References\ConflictReason',
            $attributes['conflict_reason_id']
        ) : null;

    $conflictResult = Arr::has($attributes, 'conflict_result_id')
        ? EntityManager::getReference(
            'App\Entities\References\ConflictResult',
            $attributes['conflict_result_id']
        ) : null;

    $industry = Arr::has($attributes, 'industry_id')
        ? EntityManager::getReference(
            'App\Entities\References\Industry',
            $attributes['industry_id']
        ) : null;

    $region = Arr::has($attributes, 'region_id')
        ? EntityManager::getReference(
            'App\Entities\References\Region',
            $attributes['region_id']
        ) : null;

    return [
        'title_ru'       => Arr::get($attributes, 'title_ru', $faker->word),
        'title_en'       => Arr::get($attributes, 'title_en', $faker->word),
        'title_es'       => Arr::get($attributes, 'title_es', $faker->word),
        'latitude'       => Arr::get($attributes, 'latitude', $faker->randomNumber(7)),
        'longitude'      => Arr::get($attributes, 'longitude', $faker->randomNumber(7)),
        'company_name'   => Arr::get($attributes, 'company_name', $faker->word),
        'date_from'      => Arr::get($attributes, 'date_from', $faker->dateTime()),
        'date_to'        => Arr::get($attributes, 'date_to', $faker->dateTime()),
        'conflictReason' => $conflictReason,
        'conflictResult' => $conflictResult,
        'industry'       => $industry,
        'region'         => $region,
    ];
});
