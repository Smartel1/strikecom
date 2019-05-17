<?php

namespace App\Http\Middleware;

use App\Entities\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use LaravelDoctrine\ORM\Facades\EntityManager;

class TokenAuth
{
    /**
     * Если нет токена, то просто продолжаем.
     * Парсим токен, сверяем. Берем юзера из токена и записываем в базу если его там нет.
     * Аутентифицируем
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        /**
         * @var $em EntityManager
         */
        $em = app('em');

        //Заглушка на время разработки
        if (!request()->bearerToken()) {
            $user = $em->getRepository('App\Entities\User')->findOneBy(['uuid'=>1, 'admin'=>true]);
            if (!$user) {
                $user = new User;
                $user->setUuid(1);
                $user->setAdmin(true);
                $em->persist($user);
            }
            Auth::login($user);
            return $next($request);
        }

        $serviceAccount =  ServiceAccount::fromArray([
            "type" => "service_account",
            "project_id"=> "strikecom-7ad08",
            "private_key_id"=> "b9898c8f2a0800be1cf5c8b1c671e1eb771271ae",
            "private_key" => base64_decode(env('FB_SECRET')),
            "client_email"=> "firebase-adminsdk-gk3et@strikecom-7ad08.iam.gserviceaccount.com",
            "client_id"=> "113160820514212811997",
            "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
            "token_uri"=> "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-gk3et%40strikecom-7ad08.iam.gserviceaccount.com"

        ]);

        try {
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();

            $verifiedIdToken = $firebase->getAuth()->verifyIdToken(request()->bearerToken());

            $uuid = $verifiedIdToken->getClaim('sub');
        } catch (\Throwable $e) {
            throw new AuthenticationException('Проблемы с аутентификацией');
        }

        $user = $em->getRepository('App\Entities\User')->findOneBy(['uuid', $uuid]);

        if (!$user) {

            $userData = $firebase->getAuth()->getUser($uuid);

            $user = new User;
            $user->setUuid($userData->uuid);
            $user->setEmail($userData->email);
            $user->setName($userData->displayName);

            $em->persist($user);
        }

        Auth::login($user);

        return $next($request);
    }
}
