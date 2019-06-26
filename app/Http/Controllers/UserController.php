<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UserShowRequest;
use App\Http\Resources\User\UserResource;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\Access\AuthorizationException;
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
     * @param UserUpdateRequest $request
     * @param $locale
     * @param User $user
     * @return array
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AuthorizationException
     */
    public function update(UserUpdateRequest $request, $locale, User $user)
    {
        $this->authorize('update', [$user, $request->has('roles')]);

        if ($request->has('fcm')) { $user->setFcm($request->fcm); }

        if ($request->has('roles')) { $user->setRoles($request->roles); }

        $this->em->persist($user);
        $this->em->flush();

        return UserResource::make(Auth::user())->toArray(null);
    }
}
