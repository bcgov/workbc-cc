<?php

/**
 * Retrieve session entry for given cookie.
 * Based on https://drupal.stackexchange.com/a/231726/767
 *
 * Usage: drush scr scripts/export_sessions.php -- --cookie=<value of SESSxxxx cookie>
 *
 * [
 *   {
 *       "uid": "1",
 *       "sid": "-Xcm0ar3mWcMhIhhBANA3K-jUx3JNOsu190LPEUzIN8",
 *       "hostname": "172.24.0.1",
 *       "timestamp": "1723588455",
 *       "session": "_sf2_attributes|a:1:{s:3:\"uid\";s:1:\"1\";}_sf2_meta|a:4:{s:1:\"u\";i:1723588455;s:1:\"c\";i:1723574737;s:1:\"l\";i:2000000;s:1:\"s\";s:43:\"OCpNT7IvSsWNfPeYXam7E7XFPTKqb-8qWPUTMe8MFlQ\";}",
 *       "sf2": [
 *           {
 *               "name": "attributes",
 *               "value": {
 *                   "uid": "1"
 *               }
 *           },
 *           {
 *               "name": "meta",
 *               "value": {
 *                   "u": 1723588455,
 *                   "c": 1723574737,
 *                   "l": 2000000,
 *                   "s": "OCpNT7IvSsWNfPeYXam7E7XFPTKqb-8qWPUTMe8MFlQ"
 *               }
 *           }
 *       ]
 *   },
 * ]
 *
 */

use Drupal\Component\Utility\Crypt;

if (!empty($extra)) {
  if (!str_starts_with($extra[0], '--cookie=')) {
    die("Usage: drush scr scripts/export_sessions.php [-- --cookie=<value of SESSxxxx cookie>]\n");
  }
  else {
    $cookie = trim(str_replace('--cookie=', '', $extra[0]));
    $cookie = urldecode($cookie);
    $sid = Crypt::hashBase64($cookie);
  }
}
$connection = \Drupal::database();
if (isset($sid)) {
  $query = $connection->query('SELECT * FROM {sessions} WHERE sid = :sid', [':sid' => $sid]);
}
else {
  $query = $connection->query('SELECT * FROM {sessions}');
}

echo json_encode(array_map(function($session) {
  preg_match_all('/_sf2_(\w+)\|/', $session->session, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
  $session->sf2 = array_map(function($match, $index) use ($session, $matches) {
    $offset = $match[0][1] + strlen($match[0][0]);
    $length = $index + 1 < count($matches) ?
      $matches[$index + 1][0][1] - $offset:
      strlen($session->session) - 1 - $match[0][1];
    return [
      'name' => $match[1][0],
      'value' => unserialize(substr($session->session, $offset, $length)),
    ];
  }, $matches, array_keys($matches));
  return $session;
}, $query->fetchAll()), JSON_PRETTY_PRINT) . "\n";
