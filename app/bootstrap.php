<?php
declare(strict_types=1);

require __DIR__ . '/includes/session.php';

spl_autoload_register(static function (string $class): void {
    $class = ltrim($class, '\\');
    if ($class === '') {
        return;
    }

    $paths = [
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/Controllers/' . $class . '.php',
        __DIR__ . '/Models/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/model/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});
