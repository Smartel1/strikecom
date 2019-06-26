<?php

namespace App\Providers;

use App\Entities\Event;
use App\Entities\News;
use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use LaravelDoctrine\ORM\Facades\EntityManager;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('conflict', function($id){
            return $this->findOrFail('App\Entities\Conflict', $id);
        });

        Route::bind('event', function($id, $route){

            /** @var $event Event*/
            $event = $this->findOrFail('App\Entities\Event', $id);

            Route::bind('comment', function($id, $route) use ($event) {
                $comment = EntityManager::createQueryBuilder()
                    ->select('c')
                    ->from('App\Entities\Event','e')
                    ->from('App\Entities\Comment','c')
                    ->andwhere('c.id = :id')
                    ->andwhere('e.id = :eventId')
                    ->andwhere(EntityManager::getExpressionBuilder()->isMemberOf('c','e.comments'))
                    ->setParameter('id', $id)
                    ->setParameter('eventId', $event->getId())
                    ->getQuery()
                    ->getOneOrNullResult();
                if (!$comment) throw new EntityNotFoundException();
                return $comment;
            });

            return $this->findOrFail('App\Entities\Event', $id);
        });

        Route::bind('news', function($id){
            /** @var $news News*/
            $news = $this->findOrFail('App\Entities\News', $id);

            //находим коммент новости. Если он не найден, выбрасываем исключение
            Route::bind('comment', function($id, $route) use ($news) {
                $comment = EntityManager::createQueryBuilder()
                    ->select('c')
                    ->from('App\Entities\News','n')
                    ->from('App\Entities\Comment','c')
                    ->andwhere('c.id = :id')
                    ->andwhere('n.id = :newsId')
                    ->andwhere(EntityManager::getExpressionBuilder()->isMemberOf('c','n.comments'))
                    ->setParameter('id', $id)
                    ->setParameter('newsId', $news->getId())
                    ->getQuery()
                    ->getOneOrNullResult();
                if (!$comment) throw new EntityNotFoundException();
                return $comment;
            });

            return $news;
        });

        Route::bind('client_version', function($id){
            return $this->findOrFail('App\Entities\ClientVersion', $id);
        });

        Route::bind('user', function($id){
            return $this->findOrFail('App\Entities\User', $id);
        });

        parent::boot();
    }

    /**
     * @param $entityClass
     * @param $id
     * @return object|null
     * @throws EntityNotFoundException
     */
    private function findOrFail($entityClass, $id)
    {
        $entity = EntityManager::find($entityClass, $id);
        if (!$entity) throw new EntityNotFoundException();
        return $entity;
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
