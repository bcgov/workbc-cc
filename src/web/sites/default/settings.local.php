<?php

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

$databases['default']['default'] = array (
  'database' => 'workbc-cc-refactor',
  'username' => 'workbc',
  'password' => 'workbc',
  'prefix' => '',
  'host' => 'postgres',
  'port' => '5432',
  'driver' => 'pgsql',
);



/**
 * Control caching in the local development environment.
 */
const LOCAL_CACHE_ACTIVE = TRUE;

/**
 * Enable local development services.
 */
if (LOCAL_CACHE_ACTIVE) {
  $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.cache.yml';
}
else {
  $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
}

/**
 * Disable the render cache.
 *
 * Note: you should test with the render cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the render cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
if (!LOCAL_CACHE_ACTIVE) {
  $settings['cache']['bins']['render'] = 'cache.backend.null';
}

/**
 * Disable caching for migrations.
 *
 * Uncomment the code below to only store migrations in memory and not in the
 * database. This makes it easier to develop custom migrations.
 */
# $settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';

/**
 * Disable Internal Page Cache.
 *
 * Note: you should test with Internal Page Cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the page cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
if (!LOCAL_CACHE_ACTIVE) {
  $settings['cache']['bins']['page'] = 'cache.backend.null';
}

/**
 * Disable Dynamic Page Cache.
 *
 * Note: you should test with Dynamic Page Cache enabled, to ensure the correct
 * cacheability metadata is present (and hence the expected behavior). However,
 * in the early stages of development, you may want to disable it.
 */
if (!LOCAL_CACHE_ACTIVE) {
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
}



ini_set('memory_limit', '1G');
if (class_exists('Kint')) {
  // Set the max_depth to prevent out-of-memory.
  \Kint::$depth_limit = 4;
}
