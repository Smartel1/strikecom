<?php

namespace App\Http\Middleware;

use Closure;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Routing\Route;
use LaravelDoctrine\ORM\Contracts\UrlRoutable;
use ReflectionParameter;

class SubstituteBindings extends \LaravelDoctrine\ORM\Middleware\SubstituteBindings
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws EntityNotFoundException
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();

        $this->substituteImplicitBindings($route);

        return $next($request);
    }
}
