<?php

$envFilePath = '/var/www/html/.env';
if (!empty($envFilePath)) {
    if (file_exists($envFilePath) && $envFile = file_get_contents($envFilePath)) {
        $envLines = array_filter(explode("\n", $envFile));
        foreach ($envLines as $line) {
            // Игнорирование пустых строк и комментариев
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            $parts = explode('=', $line, 2);
            $name = trim($parts[0]);

            switch ($name) {
                case 'PACKAGE_NAME':
                case 'PACKAGE_VERSION_MAJOR':
                case 'PACKAGE_VERSION_MINOR':
                case 'PACKAGE_VERSION_PATCH':
                case 'PACKAGE_RELEASE':
                case 'PACKAGE_ENCRYPTION':
                case 'PACKAGE_INSTALL':
                    putenv($name . '=' . trim($parts[1], " \t\n\r\0\x0B\"'"));
                    break;
                default:
                    break;
            }
        }
    }
}

include_once dirname(__FILE__, 4) . '/bootstrap.php';

if (getenv('PACKAGE_DEPLOY') === 'False') {
    putenv('PACKAGE_RELEASE=noecrypt');
} else {

    if (getenv('PACKAGE_ENCRYPTION') === 'True') {
        if (getenv('MODSTORE_USERNAME') == '') {
            echo 'MODSTORE_USERNAME is not set';
            exit(1);
        }
        if (getenv('MODSTORE_API_KEY') == '') {
            echo 'MODSTORE_API_KEY is not set';
            exit(1);
        }
    }
}

return [
    'name' => getenv('PACKAGE_NAME'),
    'name_lower' => getenv('PACKAGE_NAME'),
    'version' => getenv('PACKAGE_VERSION_MAJOR') . '.' . getenv('PACKAGE_VERSION_MINOR') . '.' . getenv('PACKAGE_VERSION_PATCH'),
    'release' => getenv('PACKAGE_RELEASE'),
    'encryption_enable' => getenv('PACKAGE_ENCRYPTION') === 'True',
    'encryption' => array(
        'username' => getenv('MODSTORE_USERNAME'),
        'api_key' => getenv('MODSTORE_API_KEY'),
    ),
    // Install package to site right after build
    'install' => getenv('PACKAGE_INSTALL') === 'True',
    // Which elements should be updated on package upgrade
    'update' => [
        'chunks' => false,
        'menus' => true,
        'plugins' => false,
        'resources' => false,
        'settings' => false,
        'snippets' => false,
        'templates' => false,
        'widgets' => false,
        'events' => false,
    ],
    // Which elements should be static by default
    'static' => [
        'plugins' => false,
        'snippets' => false,
        'chunks' => false,
    ],
    // Log settings
    'log_level' => !empty($_REQUEST['download']) ? 0 : 3,
    'log_target' => php_sapi_name() == 'cli' ? 'ECHO' : 'HTML',
    // Download transport.zip after build
    'download' => !empty($_REQUEST['download']),
];
