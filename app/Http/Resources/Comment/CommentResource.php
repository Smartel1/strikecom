<?php

namespace App\Http\Resources\Comment;

use App\Entities\Comment;
use App\Entities\Photo;
use Illuminate\Http\Resources\Json\Resource;

class CommentResource extends Resource
{
    /**
     * Структура комментария
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $comment Comment*/
        $comment = $this;

        return [
            'id'      => $comment->getId(),
            'user'    => $comment->getUser() ? [
                'id'     => $comment->getUser()->getId(),
                'name'   => $comment->getUser()->getName(),
                'email'  => $comment->getUser()->getEmail()
            ] : null,
            'content' => $comment->getContent(),
            'photos'  => $comment->getPhotos()
                ->map(function(Photo $photo){ return $photo->getUrl(); })
                ->getValues(),
        ];
    }
}
