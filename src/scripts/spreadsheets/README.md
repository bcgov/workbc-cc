Spreadsheet-in-code experiment
==============================

The aim of this experiment is to programmatically use the CDQ quiz spreadsheets, to more rapidly iterate on the quiz scoring refactoring in the Drupal application. By running a spreadsheet interpreter that executes the scoring formula, we can test that the results of our custom implementation matches the expected score results - the same way to QA and UAT will validate it.

The core idea of this experiment is to utilize an [Excel interpreter](https://github.com/PHPOffice/PhpSpreadsheet) that is able to execute the complex formulas that are used for the quiz scoring. We then have a separate script for each of the 6 quizzes, to load the quiz, set the wanted responses, and calculate the cell(s) that show the results.

## Usage
- Download a local copy of the quiz spreadsheets at https://github.com/bcgov/workbc-ssot/tree/noc/migration/data/CDQ
- `composer install`
- Copy `.env.example` to `.env`, filling in the O*NET API credentials
- Run `quizzes.sh`
