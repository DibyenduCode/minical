<?php
/**
 * DayCal - Front Controller
 */

require_once __DIR__ . '/config/config.php';

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $parts = explode('\\', $relative_class);

    // Lowercase folder names for Linux case-sensitive filesystems compatibility
    for ($i = 0; $i < count($parts) - 1; $i++) {
        $parts[$i] = strtolower($parts[$i]);
    }

    $file = $base_dir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\App;
use App\Core\Session;

Session::init();

// Load Routes
require_once __DIR__ . '/routes/web.php';

$app = new App();
$app->run();
