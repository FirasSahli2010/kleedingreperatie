<?php
namespace Public\Routes;

use Public\Src\Controllers\PageController;

$router = new App\Router\Router();

// Home page route
$router->addRoute('GET', '/', function() use ($config) {
    $defaultLang = $_SESSION['language'] ?? $config['default_language'];
    header("Location: /$defaultLang/");
    exit();
});

// Language-based routes
$router->addRoute('GET', '/{lang}/aboutus', [Page::class, 'about']);
$router->addRoute('GET', '/{lang}/contact', [PageController::class, 'contact']);
$router->addRoute('POST', '/{lang}/contact', [PageController::class, 'saveInfo']);
$router->addRoute('GET', '/{lang}/gallery', [PageController::class, 'gallery']);
$router->addRoute('POST', '/{lang}/submit-form', [PageController::class, 'handleFormSubmission']);
$router->addRoute('GET', '/{lang}/tarief', [PageController::class, 'tarief']);
$router->addRoute('GET', '/{lang}/', [PageController::class, 'home']);

// 404 Catch-all route
$router->addRoute('GET', '/{lang}/{any}', function() {
    http_response_code(404);
    include __DIR__ . '/../public/404.php';
});