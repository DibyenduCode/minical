<?php
/**
 * MiniCal - Front Controller
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
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

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
