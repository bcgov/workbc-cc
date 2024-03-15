<?php

/**
 * Update JSON files to use new NOC 2021 codes.
 *
 * Usage: php csv_extract.php --range start1-end1 [--cols length] [--range start2-end2] ... < /path/to/input.csv > /path/to/output.csv
 */

$opts = getopt('', [
    'noc:',
    'title:'
]);
$usage = 'Usage: php csv_extract.php --noc field --title field < /path/to/input.csv > /path/to/output.csv';
if (!array_key_exists('noc', $opts)) {
    die("No noc code field provided\n" . $usage . PHP_EOL);
}
if (!array_key_exists('title', $opts)) {
    die("No title field provided\n" . $usage . PHP_EOL);
}

$concordance = loadConcordance();

$json = file_get_contents('php://stdin'); 

// Decode the JSON file 
$oldData = json_decode($json,true); 

$newData = [];
foreach($oldData as $key => $noc) {
  $code = str_pad($noc[$opts['noc']], 4, "0", STR_PAD_LEFT);

  $noc2021 = searchConcordance($code);
  if ($noc2021) {
    $noc[$opts['noc']] = $noc2021[3];
    $noc[$opts['title']] = $noc2021[4];
    $newData[] = $noc;
  }
}

$json = preg_replace_callback(
    '/^(?: {4})+/m',
    function($m) {
        return str_repeat(' ', 2 * (strlen($m[0]) / 4));
    },
    json_encode($newData, JSON_PRETTY_PRINT)
  );



// json_encode($NOC2016);
// Write in the file
$fp = fopen("php://stdout", 'w');
fwrite($fp, $json);
fclose($fp);


function loadConcordance() {

    $file = fopen("./data/noc_2016_2021_concordance.csv", 'r');
    if($file !== FALSE){
      $concordance = [];
      while (($item = fgetcsv($file)) !== FALSE) {
        if ($item) {
          $concordance[] = $item;
        }
      }
      fclose($file);
  
      // drop header row
      array_shift($concordance);
  
      // convert NOC into 4 & 5 digit strings
      foreach ($concordance as $key => $noc) {
        if ($noc) {
          $concordance[$key][0] = str_pad($noc[0], 4, "0", STR_PAD_LEFT);
          $concordance[$key][3] = str_pad($noc[3], 5, "0", STR_PAD_LEFT);
        }
      }
    }
    return empty($concordance) ? false : $concordance;
  }


  function searchConcordance($noc) {
    global $concordance;

    $result = false;
    foreach ($concordance as $key => $item) {
      if ($item[0] == $noc) {
        $result = $item;
        break;
      }
    }
    return $result;
  }