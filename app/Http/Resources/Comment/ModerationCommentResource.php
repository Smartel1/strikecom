<?php

namespace App\Http\Resources\Comment;

use App\Entities\Comment;
use App\Entities\Photo;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ModerationCommentResource extends Resource
{
    /**
     * Структура комментария
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $comment Comment */
        $comment = $this->resource;

        //Сущность, к которой прикреплён комментарий
        $owner = null;
        //Технически можно прикрепить один коммент одновременно к нескольким сущностям.
        //Но на практике мы этого не ожидаем и выводим первую встретившуюся
        if ($comment->getEvents()->count() > 0) {
            $owner = ['entity' => 'event', 'id' => $comment->getEvents()->first()->getId()];
        } elseif ($comment->getNews()->count() > 0) {
            $owner = ['entity' => 'news', 'id' => $comment->getNews()->first()->getId()];
        }

        return [
            'id'         => $comment->getId(),
            'user'       => $comment->getUser() ? [
                'id'        => $comment->getUser()->getId(),
                'name'      => $comment->getUser()->getName(),
                'image_url' => $comment->getUser()->getImageUrl(),
                'email'     => $comment->getUser()->getEmail()
            ] : null,
            'content'    => $comment->getContent(),
            'created_at' => $comment->getCreatedAt(),
            'photos'     => $comment->getPhotos()
                ->map(function (Photo $photo) {
                    return $photo->getUrl();
                })
                ->getValues(),
            'claims'     => app(ClaimService::class)->getCommentClaimsCount($comment),
            'owner'      => $owner
        ];
    }
}
