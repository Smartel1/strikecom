<?php

namespace App\Http\Middleware;

use App\Entities\User;
use Closure;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Lcobucci\JWT\Token;
use Throwable;

class TokenAuth
{
    private $em;

    /**
     * @var Firebase
     */
    private $firebase;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        $serviceAccount = ServiceAccount::fromArray([
            "type"                        => "service_account",
            "project_id"                  => "strikecom-7ad08",
            "private_key_id"              => "b9898c8f2a0800be1cf5c8b1c671e1eb771271ae",
            "private_key"                 => base64_decode(env('FB_SECRET')),
            "client_email"                => "firebase-adminsdk-gk3et@strikecom-7ad08.iam.gserviceaccount.com",
            "client_id"                   => "113160820514212811997",
            "auth_uri"                    => "https://accounts.google.com/o/oauth2/auth",
            "token_uri"                   => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"        => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-gk3et%40strikecom-7ad08.iam.gserviceaccount.com"
        ]);

        try {
            $this->firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();
        } catch (Throwable $e) {
            throw new AuthenticationException('Проблемы с аутентификацией: ' . $e->getMessage());
        }
    }

    /**
     * Если нет токена, то просто продолжаем.
     * Парсим токен, сверяем. Берем юзера из токена и записываем в базу если его там нет.
     * Если уже есть, то обновим (при условии, что он ещё не обновлялся из этого токена)
     * Аутентифицируем
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AuthenticationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function handle($request, Closure $next)
    {
        if (!request()->bearerToken()) {
            return $next($request);
        }

        Log::info(request()->bearerToken());

        $verifiedIdToken = $this->verifyTokenAndGetObject(request()->bearerToken());

        $uuid = $verifiedIdToken->getClaim('sub');

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['uuid' => $uuid]);

        //Если пользователя ещё нет в базе, то создадим его.
        if (!$user) {
            $user = new User;
            $this->updateUserFields($user, $uuid);
        }

        //Если пользователь был в базе, но обновлялся раньше, чем был выпущен этот токен, то обновим
        //todo сделать это асинхронно
        if (null === $user->getUpdatedAt() or $user->getUpdatedAt() < $verifiedIdToken->getClaim('iat')) {
            $this->updateUserFields($user, $uuid);
        }

        Auth::login($user);

        return $next($request);
    }

    /**
     * Проверить сигнатуру токена и получить объект JWT
     * После проверки токен помещается в кэш, чтобы не проверять его повторно (ибо затратно)
     * @param $bearer
     * @return Token
     * @throws AuthenticationException
     */
    private function verifyTokenAndGetObject($bearer)
    {
        $cache = app('cache');

        $cacheKey = md5($bearer);
        //Если в кэше нет данных об этом токене, то мы их получим и запишем. В дальнейшем берем из кэша
        if (!$cache->has($cacheKey)) {
            try {
                //Верифицируем токен (это связано с отправкой запроса в fb, поэтому кэшируем)
                $verifiedIdToken = $this->firebase->getAuth()->verifyIdToken(request()->bearerToken());
            } catch (Throwable $e) {
                throw new AuthenticationException('Проблемы с аутентификацией: ' . $e->getMessage());
            }
            //Храним в кэше данные о токене, пока он действителен
            $cacheTTL = ($verifiedIdToken->getClaim('exp') - time()) / 60;
            $cache->put($cacheKey, $verifiedIdToken, $cacheTTL);
        }

        return $cache->get($cacheKey);
    }

    /**
     * Обновить пользователя в локальной базе, получив его данные в firebase
     * @param $user
     * @param $uuid
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function updateUserFields(User $user, $uuid)
    {
        //Запрос к fb, занимает много времени
        $userData = $this->firebase->getAuth()->getUser($uuid);

        $user->setUuid($userData->uid);
        $user->setEmail($userData->email);
        $user->setName($userData->displayName);
        $user->setImageUrl($userData->photoUrl);
        $user->setUpdatedAt(new DateTime);

        $this->em->persist($user);
        $this->em->flush();
    }
}
