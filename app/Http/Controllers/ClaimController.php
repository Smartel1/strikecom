<?php


namespace App\Http\Controllers;


use App\Entities\Comment;
use App\Entities\Interfaces\Commentable;
use App\Http\Requests\Claim\ClaimStoreRequest;
use App\Services\ClaimService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Facades\Auth;

class ClaimController
{
    protected $service;

    /**
     * ClaimController constructor.
     * @param $service
     */
    public function __construct(ClaimService $service)
    {
        $this->service = $service;
    }

    /**
     * Создать жалобу на комментарий
     * @param ClaimStoreRequest $request
     * @param $locale
     * @param Commentable $commentable
     * @param Comment $comment
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(ClaimStoreRequest $request, $locale, Commentable $commentable, Comment $comment)
    {
        $this->service->create($comment, Auth::user(), $request->claim_type_id);
    }
}