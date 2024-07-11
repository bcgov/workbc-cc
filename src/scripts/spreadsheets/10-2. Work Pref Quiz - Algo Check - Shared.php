<?php

// Uses my fork of PhpSpreadsheet to add custom functions
// https://github.com/infojunkie/PhpSpreadsheet
//

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

if (!isset($argv[1]) || !file_exists($argv[1])) {
  die("Usage: php {$argv[0]} /path/to/work-pref-quiz.xlsx\n");
}

$spreadsheet = IOFactory::load($argv[1]);
$calculation = $spreadsheet->getCalculationEngine();
$calculation->getDebugLog()->setWriteDebugLog(true);
$calculation->getDebugLog()->setEchoDebugLog(true);
$calculation->setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);

// Empty out spill cells.
$sheet = $spreadsheet->setActiveSheetIndexByName('Helper-WorkP');
$rowIterator = $sheet->getRowIterator();
foreach ($rowIterator as $row) {
  $columnIterator = $row->getCellIterator();
  foreach ($columnIterator as $cell) {
    if (str_contains($cell->getValueString(), "COMPUTED_VALUE")) {
      $cell->setValueExplicit(NULL, 'null');
    }
  }
}

// Set answers.
$sheet = $spreadsheet->setActiveSheetIndexByName('WorkP-NewQOrder');
$answers = '43210012344321001234432100123443210012344321001234';
$columns = 'JIHGF';
foreach (str_split($answers) as $a => $answer) {
  foreach (str_split($columns) as $c => $column) {
    $cell = $sheet->getCell($column . ($a + 5));
    $cell->setValue("$c" === $answer ? 1 : NULL);
  }
}
$cell = $sheet->getCell('O18');
$cell->setValue('={FILTER(INDIRECT(P16&"AF7:AH36"),INDIRECT(P16&"AH7:AH36")<>"")}');
$result = $calculation->calculate($cell);
echo json_encode($result);
