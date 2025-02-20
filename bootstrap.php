<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use Symfony\Component\Dotenv\Dotenv;
define('BASE_DIR', dirname(__FILE__) . '/');
require_once BASE_DIR . 'vendor/autoload.php';
(new Dotenv(true))->loadEnv(BASE_DIR . '.env');

if (!defined('MODX_CONFIG_KEY')) {
    define('MODX_CONFIG_KEY', 'config');
}
if (!defined('MODX_CORE_PATH')) {
    define('MODX_CORE_PATH', BASE_DIR . 'core/');
}
