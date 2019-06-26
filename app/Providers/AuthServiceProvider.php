<?php

namespace App\Providers;

use App\Entities\ClientVersion;
use App\Entities\Comment;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\News;
use App\Entities\User;
use App\Policies\ClientVersionPolicy;
use App\Policies\CommentPolicy;
use App\Policies\ConflictPolicy;
use App\Policies\EventPolicy;
use App\Policies\NewsPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Conflict::class      => ConflictPolicy::class,
        Event::class         => EventPolicy::class,
        News::class          => NewsPolicy::class,
        Comment::class       => CommentPolicy::class,
        User::class          => UserPolicy::class,
        ClientVersion::class => ClientVersionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('moderate', function (User $user) {
            return in_array(User::$ROLE_MODERATOR, $user->getRoles())
                or in_array(User::$ROLE_ADMIN, $user->getRoles());
        });
    }
}
