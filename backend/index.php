<?php
// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse line
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove surrounding quotes
            if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Error reporting based on environment
if (($_ENV['ENVIRONMENT'] ?? 'production') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('UTC');

// Load required files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/middleware/MiddlewareInterface.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/CorsMiddleware.php';
require_once __DIR__ . '/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/middleware/LoggerMiddleware.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/ArtistController.php';
require_once __DIR__ . '/controllers/AlbumController.php';
require_once __DIR__ . '/controllers/StreamController.php';
require_once __DIR__ . '/controllers/GuestController.php';

// Dispatch router
$router = require __DIR__ . '/routes/api.php';
$router->dispatch();
?>