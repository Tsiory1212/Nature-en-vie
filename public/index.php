<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");
    header("Allow: *");
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === "OPTIONS") {
        die();
    }
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
