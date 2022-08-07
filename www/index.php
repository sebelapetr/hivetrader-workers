<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$isApi = substr($_SERVER['REQUEST_URI'], 0, 4) === '/api';

if ($isApi) {
    App\Bootstrap::boot()
        ->createContainer()
        ->getByType(Contributte\Middlewares\Application\IApplication::class)
        ->run();
} else {
    App\Bootstrap::boot()
        ->createContainer()
        ->getByType(Nette\Application\Application::class)
        ->run();
}
