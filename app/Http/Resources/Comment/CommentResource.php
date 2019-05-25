<?php

namespace App\Http\Resources\Comment;

use App\Entities\Claim;
use App\Entities\Comment;
use App\Entities\Photo;
use App\Entities\References\ClaimType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Arr;

class CommentResource extends Resource
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
        $comment = $this;

        //В ответ добавляется количество жалоб по каждому типу
        $claims = $comment->getClaims();
        $claimInfo = [];
        foreach ($claims as $claim) {
            //В массив добавляем ключ, соответствующий id типа жалобы,
            //значение (количество жалоб этого типа) инкрементится
            $claimInfo[$claim->getClaimType()->getId()] = Arr::get($claimInfo, $claim->getClaimType()->getId(), 0) + 1;
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
            'claims'     => $claimInfo,
        ];
    }
}
