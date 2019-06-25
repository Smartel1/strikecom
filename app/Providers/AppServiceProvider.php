<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //дефолтная локаль (в первую очередь для консольных скриптов)
        app()->instance('locale', 'ru');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Регистрируем firebase сервис
        $this->app->bind('firebase', function($app) {
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

            return (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();
        });
    }
}
