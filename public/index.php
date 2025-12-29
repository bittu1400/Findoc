<?php

// Start session
session_start();

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Autoloader for classes
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    // App\Core\Database -> app/Core/Database.php
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Error handling for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create router instance
$router = new App\Core\Router();

// Load routes
require_once __DIR__ . '/../routes/web.php';

// Dispatch the request
try {
    $router->dispatch();
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
    
    // In production, log error and show generic message
    // error_log($e->getMessage());
    // echo "Something went wrong. Please try again later.";
}

?>