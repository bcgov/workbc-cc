<?php

/**
 * @file
 * Custom Controller class.
 */

namespace Drupal\workbc_cdq_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;


class CompareCareersController extends ControllerBase {


  public function compare_careers(EntityInterface $webform_submission) {

    // $results = getUserSubmission('abilities_quiz');
    if ($webform_submission) {
ksm($webform_submission);
ksm($webform_submission->id());
      // $data = $results->getData();

      $markup = "got'em";

      // $total = 0;
      // foreach ($data as $key => $value) {
      //   $markup .= "<p>" . $key . ": <b>" . $value . "</b></p>";
      //   $total += $value;
      // }

      // $markup .= "<p></p>";
      // $markup .= "<p>Total Score: <b>" . $total . "</b></p>";
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
