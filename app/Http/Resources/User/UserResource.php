<?php

namespace App\Http\Resources\User;

use App\Entities\Event;
use App\Entities\News;
use App\Entities\User;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Структура ответа на запрос деталки пользователя
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $user User */
        $user = $this;

        $structure = [
            'id'               => $user->getId(),
            'name'             => $user->getName(),
            'uuid'             => $user->getUuid(),
            'email'            => $user->getEmail(),
            'fcm'              => $user->getFcm(),
            'roles'            => $user->getRoles(),
            'image_url'        => $user->getImageUrl(),
            'favourite_events' => $user->getFavouriteEvents()->map(function (Event $event) {
                return $event->getId();
            })->getValues(),
            'favourite_news'   => $user->getFavouriteNews()->map(function (News $news) {
                return $news->getId();
            })->getValues(),
        ];

        return $structure;
    }
}
