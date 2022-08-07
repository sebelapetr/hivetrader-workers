<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{

	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);
        define("WWW_DIR", __DIR__.'/../www');

		$configurator->enableTracy($appDir . '/log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($appDir . '/app/config/common.neon');
        $isApi = substr($_SERVER['REQUEST_URI'], 0, 4) === '/api';
        if ($isApi) {
            $configurator->addConfig($appDir . '/app//config/apitte.neon');
        }
        $configurator->addConfig($appDir . '/app//config/local.neon');

		return $configurator;
	}
}
