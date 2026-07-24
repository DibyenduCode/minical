<?php
/**
 * Application Configuration
 */

// Base URL configuration (without trailing slash)
define('APP_NAME', 'DayCal');
define('APP_URL', 'https://daycal.in');

// Directory Paths
define('ROOT_DIR', dirname(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('CONFIG_DIR', ROOT_DIR . '/config');
define('STORAGE_DIR', ROOT_DIR . '/storage');
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('TEMPLATES_DIR', ROOT_DIR . '/templates');

// Session Config
define('SESSION_NAME', 'daycal_session');

// Google OAuth 2.0 Credentials (Set via Environment Variables or Admin Settings)
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: 'https://daycal.in/integrations/google/callback');

// Timezone
date_default_timezone_set('UTC');
