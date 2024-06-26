<?php

// Uses my fork of PhpSpreadsheet to add custom functions
// https://github.com/infojunkie/PhpSpreadsheet
//

require 'vendor/autoload.php';
require './OnetWebService.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

if (!isset($argv[1]) || !file_exists($argv[1]) || empty(getenv('ONET_USERNAME')) || empty(getenv('ONET_PASSWORD'))) {
  die("Usage: ONET_USERNAME=username ONET_PASSWORD=password php {$argv[0]} /path/to/interests-quiz.xlsx\n");
}

$spreadsheet = IOFactory::load($argv[1]);
$calculation = $spreadsheet->getCalculationEngine();
$calculation->getDebugLog()->setWriteDebugLog(true);
//$calculation->getDebugLog()->setEchoDebugLog(true);
$calculation->setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
$calculation->setCalculationCacheEnabled(false);

$sheet = $spreadsheet->setActiveSheetIndexByName('LookupCareers');
$answers = '432104444400000222220123443210333334321044444432104444400000';

// Get the O*NET matches.
$onet_ws = new OnetWebService(getenv('ONET_USERNAME'), getenv('ONET_PASSWORD'));
$matches = $onet_ws->call('mnm/interestprofiler/careers', [
  'answers' => join('', array_map(function($c) { return strval(intval($c) + 1); }, str_split($answers)))
]);
if (!empty($matches->error)) {
  die($matches->error . "\n");
}
$result = [];
foreach ($matches->career as $match) {
  $noc = $calculation->calculateFormula("=Query('Career-match'!A$2:C$1251,\"select A,B where C='{$match->code}' limit 1\")");
  if (empty($noc)) {
    fwrite(STDERR, "O*NET career {$match->code} {$match->title} not found in concordance. Ignoring result.\n");
  }
  else {
    $result[] = [
      'noc' => $noc[0][0],
      'title' => $noc[0][1],
      'fit' => $match->fit,
    ];
  }
}
var_dump($result);

// $columns = 'JIHGF';
// foreach (str_split($answers) as $a => $answer) {
//   foreach (str_split($columns) as $c => $column) {
//     $cell = $sheet->getCell($column . ($a + 5));
//     $cell->setValue("$c" === $answer ? 1 : NULL);
//   }
// }
// $result = [];
// foreach (range(5, 10) as $row) {
//   $cell = $sheet->getCell("P$row");
//   $label = $calculation->calculate($cell);
//   $cell = $sheet->getCell("T$row");
//   $score = $calculation->calculate($cell);
//   $result[] = [
//     'category' => $label,
//     'score' => $score,
//   ];
// }
