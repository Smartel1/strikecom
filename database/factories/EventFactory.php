<?php

use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;
use LaravelDoctrine\ORM\Facades\EntityManager;

$factory->define(Event::class, function (Faker $faker, array $attributes) {

    //в $attributes приходят id связей. Здесь их превращаем в прокси-объекты
    $conflict = Arr::has($attributes, 'conflict_id')
        ? EntityManager::getReference(Conflict::class, $attributes['conflict_id'])
        : null;

    $eventStatus = Arr::has($attributes, 'event_status_id')
        ? EntityManager::getReference(EventStatus::class, $attributes['event_status_id'])
        : null;

    $eventType = Arr::has($attributes, 'event_type_id')
        ? EntityManager::getReference(EventType::class, $attributes['event_type_id'])
        : null;

    return [
        'conflict'     => $conflict,
        'published'    => Arr::get($attributes, 'published', true),
        'title_ru'     => Arr::get($attributes, 'title_ru', $faker->word),
        'title_en'     => Arr::get($attributes, 'title_en', $faker->word),
        'title_es'     => Arr::get($attributes, 'title_es', $faker->word),
        'content_ru'   => Arr::get($attributes, 'content_ru', $faker->word),
        'content_en'   => Arr::get($attributes, 'content_en', $faker->word),
        'content_es'   => Arr::get($attributes, 'content_es', $faker->word),
        'date'         => Arr::get($attributes, 'date', $faker->dateTime()),
        'source_link'  => Arr::get($attributes, 'source_link', $faker->url),
        'event_status' => $eventStatus,
        'event_type'   => $eventType,
    ];
});
