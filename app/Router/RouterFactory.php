<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;


        $router[] = $routerAdmin = new RouteList("Admin");
        $routerAdmin[] = new Route('admin/<presenter>/<action>[/<id>]', 'Login:default');

        $router[] = $routerFront = new RouteList("Front");
        $routerFront->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}
}
