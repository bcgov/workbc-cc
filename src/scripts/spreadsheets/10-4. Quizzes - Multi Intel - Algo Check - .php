<?php

// Uses my fork of PhpSpreadsheet to add custom functions
// https://github.com/infojunkie/PhpSpreadsheet
//

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

if (!isset($argv[1]) || !file_exists($argv[1])) {
  die("Usage: php {$argv[0]} /path/to/multiple-intel-quiz.xlsx\n");
}

$spreadsheet = IOFactory::load($argv[1]);
$calculation = $spreadsheet->getCalculationEngine();
$calculation->getDebugLog()->setWriteDebugLog(true);
//$calculation->getDebugLog()->setEchoDebugLog(true);
$calculation->setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);

$sheet = $spreadsheet->setActiveSheetIndexByName('Multi-Intel-Options1');
$answers = '4321043210444442222211111432100123400000432104321043210012341111';
$columns = 'KJIHG';
foreach (str_split($answers) as $a => $answer) {
  foreach (str_split($columns) as $c => $column) {
    $cell = $sheet->getCell($column . ($a + 5));
    $cell->setValue("$c" === $answer ? 1 : NULL);
  }
}
$result = [];
foreach (range(5, 12) as $row) {
  $cell = $sheet->getCell("Q$row");
  $label = $calculation->calculate($cell);
  $cell = $sheet->getCell("U$row");
  $score = $calculation->calculate($cell);
  $result[] = [
    'category' => $label,
    'score' => $score,
  ];
}
var_dump($result);
