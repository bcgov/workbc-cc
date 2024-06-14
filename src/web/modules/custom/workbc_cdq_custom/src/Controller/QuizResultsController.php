<?php

/**
 * @file
 * Custom Controller class.
 */

namespace Drupal\workbc_cdq_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;


class QuizResultsController extends ControllerBase {


  public function abilities_quiz_results() {

    // $results = $this->getSubmission('abilities_quiz');
    $results = getUserSubmission('abilities_quiz');
    if ($results) {
      // ksm($results->getData());

      $data = $results->getData();

      $markup = "";

      $total = 0;
      foreach ($data as $key => $value) {
        $markup .= "<p>" . $key . ": <b>" . $value . "</b></p>";
        $total += $value;
      }

      $markup .= "<p></p>";
      $markup .= "<p>Total Score: <b>" . $total . "</b></p>";
    }
    else {
      $markup = "No results found";
    }
    return [
      '#type' => 'markup',
      '#markup' => $markup,
      '#cache' => ['max-age' => 0],
    ];

  }


  public function work_preferences_quiz_results() {

    $results = getUserSubmission('work_preferences_quiz');
    if ($results) {
      // ksm($results->getData());

      $data = $results->getData();

      $markup = "";

      $total = 0;
      foreach ($data as $key => $value) {
        $markup .= "<p>" . $key . ": <b>" . $value . "</b></p>";
        $total += $value;
      }

      $markup .= "<p></p>";
      $markup .= "<p>Total Score: <b>" . $total . "</b></p>";
    }
    else {
      $markup = "No results found";
    }
    return [
      '#type' => 'markup',
      '#markup' => $markup,
      '#cache' => ['max-age' => 0],
    ];

  }


  public function interests_quiz_results() {

    $results = getUserSubmission('interests_quiz');
    if ($results) {
      // ksm($results->getData());

      $data = $results->getData();

      $markup = "";

      $total = 0;
      foreach ($data as $key => $value) {
        $markup .= "<p>" . $key . ": <b>" . $value . "</b></p>";
        $total += $value;
      }

      $markup .= "<p></p>";
      $markup .= "<p>Total Score: <b>" . $total . "</b></p>";
    }
    else {
      $markup = "No results found";
    }
    return [
      '#type' => 'markup',
      '#markup' => $markup,
      '#cache' => ['max-age' => 0],
    ];

  }


}
