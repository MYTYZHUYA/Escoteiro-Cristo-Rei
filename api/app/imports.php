<?php

// All the imports required for the api to function

// Exceptions

require __DIR__ . "/Exceptions/CustomException.php";
{
    $dir = __DIR__ . "/Exceptions";
    $exception_files = scandir($dir);
    foreach ($exception_files as $filename) {
        if ($filename == "" | !str_ends_with($filename, ".php")) {
            continue;
        }
        require_once "$dir/$filename";
    }
}

// require __DIR__ . "/Exceptions/MalformedTokenException.php";

// Utils
require __DIR__ . "/Utils/DotEnv.php";

// Helpers

require __DIR__ . "/Helpers/Auth/JwtManager.php";
require __DIR__ . "/Helpers/Auth/TokenHasher.php";

// Request

require __DIR__ . "/Http/Requests/Request.php";
require __DIR__ . "/Http/Requests/RequestHandler.php";

// General 

require __DIR__ . "/Internals/Database.php";
require __DIR__ . "/Internals/ErrorHandler.php";

require __DIR__ . "/Routing/Router.php";

// Middleware

require __DIR__ . "/Http/Middleware/BaseGateway.php";
require __DIR__ . "/Http/Controllers/Auth/AuthGateway.php";

// Controllers

require __DIR__ . "/Http/Controllers/Controller.php";

require __DIR__ . "/Http/Controllers/Test/TestController.php";
require __DIR__ . "/Http/Controllers/Auth/AuthController.php";
require __DIR__ . "/Http/Controllers/User/UserController.php";