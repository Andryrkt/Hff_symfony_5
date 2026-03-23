<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    $envFile = dirname(__DIR__) . '/.env';
    if (!file_exists($envFile)) {
        $envFile = dirname(__DIR__) . '/.env.test';
    }
    (new Dotenv())->bootEnv($envFile);
}
