<?php
// Configuration file: set these values for your environment
// DB DSN example: 'mysql:host=localhost;dbname=deltacr;charset=utf8mb4'
define('DB_DSN', 'mysql:host=127.0.0.1;dbname=deltacr;charset=utf8mb4');
define('DB_USER', 'db_user_here');
define('DB_PASS', 'db_password_here');

// Path to write logs (ensure writable by webserver)
define('LOG_PATH', __DIR__ . '/logs/error.log');

// Friendly environment flag
define('ENV', 'development'); // change to 'production' on live

function log_error($message)
{
    $time = date('Y-m-d H:i:s');
    $msg = "[$time] $message\n";
    @file_put_contents(LOG_PATH, $msg, FILE_APPEND | LOCK_EX);
}

set_exception_handler(function ($e) {
    log_error('Uncaught Exception: ' . $e->getMessage() . " in " . $e->getFile() . ':' . $e->getLine());
    if (ENV === 'development') {
        echo "<pre>Exception: " . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "A system error occurred. Administrators have been notified.";
    }
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $msg = "Error [$errno]: $errstr in $errfile:$errline";
    log_error($msg);
    if (ENV === 'development') {
        echo "<pre>$msg</pre>";
    }
    return true;
});

// Basic session settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    session_start();
}
