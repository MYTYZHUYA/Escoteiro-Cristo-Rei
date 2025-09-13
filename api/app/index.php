<?php

// Main API Endpoint
// Requests will be routed

declare(strict_types=1);
namespace App;

echo PHP_VERSION;
require __DIR__ . "/imports.php";

use App\Routing\Router;
use App\Utils\DotEnv as UtilsDotEnv;

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

UtilsDotEnv::parseDotEnv(".env.example");


$router = new Router("../routes.ini");
$router->parseRequest();