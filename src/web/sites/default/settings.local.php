<?php

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

$databases['default']['default'] = array (
  'database' => 'workbc-cc-refactor',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'postgres',
  'port' => '5432',
  'driver' => 'pgsql',
);


ini_set('memory_limit', '1G');
if (class_exists('Kint')) {
  // Set the max_depth to prevent out-of-memory.
  \Kint::$depth_limit = 4;
}
