<?php

namespace App\Providers;

use App\Comment;
use App\Conflict;
use App\Policies\CommentPolicy;
use App\Policies\ConflictPolicy;
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
