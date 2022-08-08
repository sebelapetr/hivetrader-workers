<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;
use Tracy\Debugger;


class Bootstrap
{

    public static function boot(): Configurator
    {
        $configurator = new Configurator;
        $appDir = dirname(__DIR__);
        define("WWW_DIR", __DIR__.'/../www');
        define("ROOT_DIR", __DIR__.'/../');
        define("LOG_DIR", __DIR__.'/../log');

        $configurator->enableTracy($appDir . '/log');

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig($appDir . '/app/config/common.neon');
        if (isset($_SERVER['REQUEST_URI']) && (substr($_SERVER['REQUEST_URI'], 0, 4) === '/api')) {
            $configurator->addConfig($appDir . '/app//config/apitte.neon');
        }
        $configurator->addConfig($appDir . '/app//config/local.neon');

        return $configurator;
    }
}
