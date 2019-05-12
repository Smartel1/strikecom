<?php

namespace App\Providers;

use App\Entities\News;
use App\Models\ClientVersion;
use App\Models\Event;
use App\Models\Comment;
use App\Models\Conflict;
use App\Policies\ClientVersionPolicy;
use App\Policies\CommentPolicy;
use App\Policies\ConflictPolicy;
use App\Policies\EventPolicy;
use App\Policies\NewsPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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

        //
    }
}
