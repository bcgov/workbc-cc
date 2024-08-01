<?php

$base_urls = [
  'aws-dev' => 'https://careerdiscoveryquizzes-dev.workbc.ca',
  'aws-test' => 'https://careerdiscoveryquizzes-test.workbc.ca',
  'aws-prod' => 'https://careerdiscoveryquizzes.workbc.ca',
];
if (array_key_exists(getenv('PROJECT_ENVIRONMENT'), $base_urls)) {
  $base_url = $base_urls[getenv('PROJECT_ENVIRONMENT')];
}

$databases['default']['default'] = array (
    'database' => getenv('POSTGRES_DB'),
    'username' => getenv('POSTGRES_USER'),
    'password' => getenv('POSTGRES_PASSWORD'),
    'prefix' => '',
    'host' => getenv('POSTGRES_HOST'),
    'port' => getenv('POSTGRES_PORT'),
    'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
    'driver' => 'pgsql',
);

$settings['hash_salt'] = json_encode($databases);
