<?php
/**
 * Application Configuration
 */

// Base URL configuration (without trailing slash)
define('APP_NAME', 'MiniCal');
define('APP_URL', 'http://localhost/cal');

// Directory Paths
define('ROOT_DIR', dirname(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('CONFIG_DIR', ROOT_DIR . '/config');
define('STORAGE_DIR', ROOT_DIR . '/storage');
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('TEMPLATES_DIR', ROOT_DIR . '/templates');

// Session Config
define('SESSION_NAME', 'minical_session');

// Timezone
date_default_timezone_set('UTC');
