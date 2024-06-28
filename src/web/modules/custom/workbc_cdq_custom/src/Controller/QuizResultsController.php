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

    $results = getUserSubmission('abilities_quiz');
    if ($results) {
      $quiz = $results->getWebform();
      $questions = $quiz->getElementsDecodedAndFlattened();
      $answers = $results->getData();
      $settings = $quiz->get('third_party_settings');
      $categories = array_reduce(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($settings['workbc_cdq_career_match']['categories']), function($categories, $category) {
        $categories[$category->name] = 0;
        return $categories;
      });

      $markup = "";
      foreach ($answers as $key => $value) {
        $question = $questions[$key]['#title'];
        $category = $questions[$key]['#category'];
        $score = floatval($value);
        $markup .= "<p>$question ($category): <b>$score</b></p>";
        $categories[$category] += $score;
      }
      $markup .= "<hr>";
      foreach ($categories as $category => $score) {
        $markup .= "<p>$category: <b>$score</b></p>";
      }
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

      $data = $results->getData();

      $markup = "";

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


  public function interests_quiz_results() {

    $results = getUserSubmission('interests_quiz');
    if ($results) {

      $data = $results->getData();

      $markup = "";

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


  public function import_career_profile_updates() {

    $profiles = $this->loadCareerProfileUpdates();

    foreach($profiles as $profile) {
      $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['field_noc' => $profile[0]]);
      $node = reset($nodes);
      if ($node) {
        $node->field_video_id = $profile[2];
        $node->field_noc_2016 = $profile[1];
        $node->save();
      }
    }

    $markup = "Career Profile video id import";

    return [
      '#type' => 'markup',
      '#markup' => $markup,
      '#cache' => ['max-age' => 0],
    ];
  }


  function loadCareerProfileUpdates() {
    $path = DRUPAL_ROOT;
    $path = preg_replace('/web$/', '', $path);
    $file = fopen($path . "cdq-career-profile-update.csv", 'r');
    if($file !== FALSE){
      $videosIds = [];
      while (($item = fgetcsv($file, 0, "|")) !== FALSE) {
        if (!empty($item[1])) {
          $videosIds[] = $item;
        }
      }
      fclose($file);

    }
    return empty($videosIds) ? false : $videosIds;
  }
}
