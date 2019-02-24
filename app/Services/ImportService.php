<?php

namespace App\Services;


use App\Conflict;
use App\ConflictReason;
use App\ConflictResult;
use App\Event;
use App\EventStatus;
use App\EventType;
use App\Industry;
use App\MediaType;
use App\News;
use App\Region;
use App\User;

class ImportService
{
    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchConflictReasons($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $reason = ConflictReason::create([
                'name_ru' => array_get($item, 'name'),
                'name_en' => array_get($item, 'name_en'),
                'name_es' => array_get($item, 'name_es'),
            ]);

            $reason['_id'] = $item['_id'];

            $collection->push($reason);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchIndustries($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $industry = Industry::create([
                'name_ru' => array_get($item, 'name'),
                'name_en' => array_get($item, 'name_en'),
                'name_es' => array_get($item, 'name_es'),
            ]);

            $industry['_id'] = $item['_id'];

            $collection->push($industry);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchRegions($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $region = Region::create([
                'name_ru' => array_get($item, 'province_ru'),
                'name_en' => array_get($item, 'province_en'),
                'name_es' => array_get($item, 'province_es'),
            ]);

            $region['_id'] = $item['_id'];

            $collection->push($region);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchConflictResults($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $region = ConflictResult::create([
                'name_ru' => array_get($item, 'name'),
                'name_en' => array_get($item, 'name_en'),
                'name_es' => array_get($item, 'name_es'),
            ]);

            $region['_id'] = $item['_id'];

            $collection->push($region);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchEventStatuses($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $status = EventStatus::create([
                'name_ru' => array_get($item, 'name'),
                'name_en' => array_get($item, 'name_en'),
                'name_es' => array_get($item, 'name_es'),
            ]);

            $status['_id'] = $item['_id'];

            $collection->push($status);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchEventTypes($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $type = EventType::create([
                'name_ru' => array_get($item, 'name'),
                'name_en' => array_get($item, 'name_en'),
                'name_es' => array_get($item, 'name_es'),
            ]);

            $type['_id'] = $item['_id'];

            $collection->push($type);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchUsers($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $user = User::create([
                'uuid'      => array_get($item, 'uuid'),
                'name'      => array_get($item, 'name'),
                'email'     => array_get($item, 'emailOrNumber'),
                'fcm'       => array_get($item, 'tokenFCM'),
                'image_url' => array_get($item, 'image'),
                'admin'     => (boolean)array_get($item, 'admin'),
                'push'      => array_get($item, 'push'),
                'reward'    => (integer)array_get($item, 'reward'),
            ]);

            $user['_id'] = $item['_id'];
            $user['favourite_posts'] = $item['favorites'];

            $collection->push($user);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return \Illuminate\Support\Collection
     */
    public function fetchConflicts($json)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            if (!array_has($item, 'lat') or !array_has($item, 'lng')) {
                $uuid = $item['_id'];
                \Log::error("Не указаны координаты конфликта $uuid");
                continue;
            }

            $conflict = Conflict::create([
                'title'     => 'untitled',
                'date_from' => array_get($item, 'date_start'),
                'date_to'   => array_get($item, 'date_end'),
                'latitude'  => array_get($item, 'lat'),
                'longitude' => array_get($item, 'lng'),
            ]);

            $conflict['_id'] = $item['_id'];

            $collection->push($conflict);
        }

        return $collection;
    }

    /**
     * @param $json
     * @param $users
     * @param $conflicts
     * @param $eventStatuses
     * @param $eventTypes
     * @param $factories
     * @param $industries
     * @param $reasons
     * @param $results
     * @return \Illuminate\Support\Collection
     */
    public function fetchEvents($json, $users, $conflicts, $eventStatuses,
                                $eventTypes, $factories, $industries,
                                $reasons, $results)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            $uuid = $item['_id'];

            $disputUID = array_get($item, 'inDisput');

            if (!$disputUID) continue;

            $conflict = $conflicts->where('_id', $disputUID)->first();

            if (!$conflict) {
                \Log::error("Не удалось привязать конфликт события $uuid");
                continue;
            }

            $eventStatusId = null;

            if (array_get($item, 'status')) {
                $eventStatus = $eventStatuses->where('_id', array_get($item, 'status'))->first();

                if (!$eventStatus) {
                    \Log::error("Не удалось привязать статус события $uuid");
                    continue;
                }

                $eventStatusId = $eventStatus->id;
            }

            $eventTypeId = null;

            if (array_get($item, 'type')) {
                $eventType = $eventTypes->where('_id', array_get($item, 'type'))->first();

                if (!$eventType) {
                    \Log::error("Не удалось привязать тип события $uuid");
                    continue;
                }

                $eventTypeId = $eventType->id;
            }

            $userId = null;

            if (array_get($item, 'creator')) {
                $user = $users->where('_id', array_get($item, 'creator'))->first();

                if (!$user) {
                    \Log::error("Не удалось привязать пользователя к событию $uuid");
                } else {
                    $userId = $user->id;
                }
            }

            $event = Event::create([
                'title'           => array_get($item, 'name'),
                'content'         => array_get($item, 'content'),
                'date'            => array_get($item, 'date'),
                'views'           => array_get($item, 'count_view', 0),
                'source_link'     => array_get($item, 'link'),
                'conflict_id'     => $conflict->id,
                'event_status_id' => $eventStatusId,
                'event_type_id'   => $eventTypeId,
                'user_id'         => $userId,
            ]);

            $event['_id'] = $item['_id'];

            $collection->push($event);

            $imageUrls = (array) array_get($item, 'images');

            foreach ($imageUrls as $imageUrl) {
                $event->photos()->create([
                    'url'           => $imageUrl,
                ]);
            }

            $inn = array_get($item, 'inn');

            if ($inn) {
                $company = $factories->where('_id', $inn)->first();

                if (!$company) {
                    \Log::error("Не удалось привязать фабрику события $uuid к конфликту, но событие создано");
                } else  {
                    $event->conflict->company_name = $company['name'];
                }

            }

            $industry_id = array_get($item, 'industry');

            if ($industry_id) {
                $industry = $industries->where('_id', $industry_id)->first();

                if (!$industry) {
                    \Log::error("Не удалось привязать отрасль события $uuid к конфликту, но событие создано");
                } else {
                    $event->conflict->industry_id = $industry->id;
                }
            }

            $reason_id = array_get($item, 'cause');

            if ($reason_id) {
                $reason = $reasons->where('_id', $reason_id)->first();

                if (!$reason) {
                    \Log::error("Не удалось привязать причину события $uuid к конфликту, но событие создано");
                } else {
                    $event->conflict->conflict_reason_id = $reason->id;
                }
            }

            $result_id = array_get($item, 'result');

            if ($result_id) {
                $result = $results->where('_id', $result_id)->first();

                if (!$result) {
                    \Log::error("Не удалось привязать результат события $uuid к конфликту, но событие создано");
                } else {
                    $event->conflict->conflict_result_id = $result->id;
                }
            }

            $regionName = array_get($item, 'region');

            if ($regionName) {
                $region = $this->defineRegion($regionName);

                if (!$region) {
                    \Log::error("Не удалось привязать регион события $uuid к конфликту, но событие создано");
                } else {
                    $event->conflict->region_id = $region->id;
                }
            }

            $event->conflict->save();
        }

        return $collection;
    }

    /**
     * @param $json
     * @param $users
     * @return \Illuminate\Support\Collection
     */
    public function fetchNews($json, $users)
    {
        $collection = collect();

        foreach (json_decode($json, true) as $item) {

            if (array_has($item, 'inDisput')) continue;

            $uuid = $item['_id'];

            $userId = null;

            if (array_get($item, 'creator')) {
                $user = $users->where('_id', array_get($item, 'creator'))->first();

                if (!$user) {
                    \Log::error("Не удалось привязать пользователя к событию $uuid");
                } else {
                    $userId = $user->id;
                }
            }

            $news = News::create([
                'title'           => array_get($item, 'name'),
                'content'         => array_get($item, 'content'),
                'date'            => array_get($item, 'date'),
                'views'           => array_get($item, 'count_view', 0),
                'source_link'     => array_get($item, 'link'),
                'user_id'         => $userId,
            ]);

            $news['_id'] = $item['_id'];

            $collection->push($news);

            $imageUrls = (array) array_get($item, 'images');

            foreach ($imageUrls as $imageUrl) {
                $news->photos()->create([
                    'url'           => $imageUrl,
                ]);
            }
        }

       return $collection;
    }

    /**
     * @param $users
     * @param $events
     * @param $news
     */
    public function fetchFavourites($users, $events, $news)
    {
        foreach ($users as $user){
            foreach (array_get($user,'favourite_posts', []) as $postId) {
                $event = $events->where('_id', $postId)->first();

                if ($event) {

                    \DB::table('favourite_events')->insert([
                        'user_id'  => $user->id,
                        'event_id' => $event->id
                    ]);

                    continue;
                }

                $post = $news->where('_id', $postId)->first();

                if ($post) {

                  \DB::table('favourite_news')->insert([
                      'user_id'  => $user->id,
                      'news_id' => $post->id
                  ]);
                }
            }
        }
    }

    /**
     * @param $name
     * @return int id
     */
    private function defineRegion($name)
    {
        return Region::where('name_ru', 'ilike', $name)->first();
    }

    /**
     * @param $type
     * @return mixed
     */
    private function defineMediaTypeId($type)
    {
        if ($type === 0) return MediaType::whereName('youtube_link')->value('id');
        if ($type === 1) return MediaType::whereName('vk_link')->value('id');
        if ($type === -2) return MediaType::whereName('photo_url')->value('id');
        return MediaType::whereName('other')->value('id');
    }
}