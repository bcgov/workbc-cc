<?php

// Uses my fork of PhpSpreadsheet to add custom functions
// https://github.com/infojunkie/PhpSpreadsheet
//

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

if (!isset($argv[1]) || !file_exists($argv[1])) {
  die("Usage: php {$argv[0]} /path/to/work-values-quiz.xlsx\n");
}

$spreadsheet = IOFactory::load($argv[1]);
$calculation = $spreadsheet->getCalculationEngine();
$calculation->getDebugLog()->setWriteDebugLog(true);
//$calculation->getDebugLog()->setEchoDebugLog(true);
$calculation->setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);

$sheet = $spreadsheet->setActiveSheetIndexByName('Work-Values');
$answers = '222221111100000000000000000000000';
$columns = 'IHG';
foreach (str_split($answers) as $a => $answer) {
  foreach (str_split($columns) as $c => $column) {
    $cell = $sheet->getCell($column . ($a + 5));
    $cell->setValue("$c" === $answer ? 1 : NULL);
  }
}
$result = [];
foreach (range(5, 8) as $row) {
  $cell = $sheet->getCell("O$row");
  $label = $calculation->calculate($cell);
  $cell = $sheet->getCell("P$row");
  $score = $calculation->calculate($cell);
  $result[] = [
    'category' => $label,
    'score' => $score,
  ];
}
var_dump($result);
