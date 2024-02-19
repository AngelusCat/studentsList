<?php

use App\Route\Router;
use App\Route\Route;

require_once('C:\localhost\vendor\autoload.php');
require_once('pathConstants.php');
require_once(SRC . 'bootstrap.php');

set_exception_handler(function (Throwable $exception) {
	header('Location: http://localhost/500');
});

set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    // Не выбрасываем исключение если ошибка подавлена с 
    // помощью оператора @
    if (!error_reporting()) {
        return;
    }

    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

$path = $_SERVER['REQUEST_URI'];

/**
 * @var string $path cодержит path
 */

$path = parse_url($path, PHP_URL_PATH);

$router = new Router([
	new Route('/', 'homePage.php'),
	new Route('/form', 'enrolleeForm.php'),
	new Route('/home', 'homePage.php'),
	new Route('/search', 'search.php'),
	new Route('/500', '500.php')
]);

/**
 * @var Route $route хранит объект класса Route, который соответствует переменной $path
 */

$route = $router->route($path);

if ($route) {
	require_once(CONTROLLERS . $route->getControllerName());
} else {
	require_once(CONTROLLERS . '404.php');
}