<?php

namespace App\Providers;

use App\Event;
use App\Comment;
use App\Conflict;
use App\News;
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
        Conflict::class => ConflictPolicy::class,
        Event::class    => EventPolicy::class,
        News::class     => NewsPolicy::class,
        Comment::class  => CommentPolicy::class,
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
