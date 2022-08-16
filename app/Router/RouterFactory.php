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

        $router[] = $routerModule = new RouteList("Module");
        $routerModule->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}
}
