<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Requests\User\SubscribeRequest;
use App\Http\Requests\User\UserShowRequest;
use App\Http\Resources\User\UserResource;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * RefController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Получить информацию о текущем аутентифицированном пользователе
     * @param UserShowRequest $request
     * @param $locale
     * @return array
     * @throws AuthenticationException
     */
    public function show(UserShowRequest $request, $locale)
    {
        if (!Auth::check()) throw new AuthenticationException();

        return UserResource::make(Auth::user())->toArray(null);
    }

    /**
     * @param SubscribeRequest $request
     * @param $locale
     * @throws AuthenticationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function subscribe(SubscribeRequest $request, $locale)
    {
        if (!Auth::check()) throw new AuthenticationException();
        /** @var User $user */
        $user = Auth::user();

        if ($request->state) {
            $user->setPush(true);
            $user->setFcm($request->fcm);
        } else {
            $user->setPush(false);
        }

        $this->em->persist($user);
        $this->em->flush();
    }
}
