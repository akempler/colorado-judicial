<?php

// Prepare a LANDO_INFO constant.
define('LANDO_INFO', json_decode($_ENV['LANDO_INFO'], TRUE));

// When using lando, use Lando settings.
if (defined('LANDO_INFO')) {
    // Databases.
    $databases['default']['default'] = [
        // Since "mariadb" drivers are the same as "mysql", we hard-code "mysql".
        'driver' => 'mysql',
        'database' => LANDO_INFO['database']['creds']['database'],
        'username' => LANDO_INFO['database']['creds']['user'],
        'password' => LANDO_INFO['database']['creds']['password'],
        'host' => LANDO_INFO['database']['internal_connection']['host'],
        'port' => LANDO_INFO['database']['internal_connection']['port'],
        'prefix' => '',
        'collation' => 'utf8mb4_general_ci',
    ];
}

// Trusted host patterns.
$settings['trusted_host_patterns'][] = '^.*\.lndo\.site$';

$settings['hash_salt'] = 'asdgaweoiuwa23570sdgih2398523kjhsdgsdsdgsdgs';

$settings['config_sync_directory'] = dirname(DRUPAL_ROOT) . '/config';
