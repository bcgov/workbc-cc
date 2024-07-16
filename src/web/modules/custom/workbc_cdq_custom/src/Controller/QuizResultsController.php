<?php

/**
 * @file
 * Custom Controller class.
 */

namespace Drupal\workbc_cdq_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\image\Entity\ImageStyle;

class QuizResultsController extends ControllerBase {

  public function abilities_quiz_results() {
    $submission = getUserSubmission('abilities_quiz');
    if ($submission) {
      $colors = [
        'General learning ability' => '880364',
        'Verbal ability' => '216C06',
        'Numerical ability' => '2671CA',
        'Spatial perception' => '002855',
        'Form perception' => '234075',
        'Clerical perception' => '216C06',
        'Motor co-ordination' => '007EB4',
        'Finger dexterity' => '880364',
        'Manual dexterity' => '510B76',                
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $colors[$category_name];
        $score[$category_name]['image'] = strtolower(str_replace(" ", "-", $category_name));       
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category' => $this->t('Aptitudes'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function work_preferences_quiz_results() {
    $submission = getUserSubmission('work_preferences_quiz');
    if ($submission) {
      $colors = [
        'Social' => '216C06',
        'Directive' => '002857',
        'Innovative' => '510B76',
        'Methodical' => '880364',
        'Objective' => '2A7DE2',               
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $colors[$category_name];
        if($category_name == "Social") {
          $score[$category_name]['image'] = "social-2";
        }
        else {
          $score[$category_name]['image'] = strtolower(str_replace(" ", "-", $category_name));       
        }
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category' => $this->t('Work Preferences'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function interests_quiz_results() {
    $submission = getUserSubmission('interests_quiz');
    if ($submission) {
      $colors = [
        'Realistic' => '002857',
        'Enterprising' => '89226E',
        'Conventional' => '510B76',
        'Investigative' => '2671CA',
        'Artistic' => '216C06',
        'Social' => '007EB4',
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $colors[$category_name];
        if($category_name == "Social") {
          $score[$category_name]['image'] = "social-1";
        }
        else {
          $score[$category_name]['image'] = strtolower(str_replace(" ", "-", $category_name));       
        }        
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category' => $this->t('Interests'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function multiple_intelligences_quiz_results() {
    $submission = getUserSubmission('multiple_intelligences_quiz');
    if ($submission) {
      $markup = "";
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $percent = $category['percent'];
        $normalized = $category['normalized'];
        $markup .= "<p>$category_name: <b>$percent% ($normalized)</b></p>";
      }
    }
    else {
      $markup = "No results found";
    }
    return [
      '#type' => 'markup',
      '#markup' => '',
      '#cache' => ['max-age' => 0],
    ];
  }

  public function learning_styles_quiz_results() {
    $submission = getUserSubmission('learning_styles_quiz');
    if ($submission) {
      $markup = "";
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $percent = $category['percent'];
        $normalized = $category['normalized'];
        $markup .= "<p>$category_name: <b>$percent% ($normalized)</b></p>";
      }
    }
    else {
      $markup = "No results found";
    }
    return [
      '#type' => 'markup',
      '#markup' => '',
      '#cache' => ['max-age' => 0],
    ];
  }

  public function work_values_quiz_results() {
    $submission = getUserSubmission('work_values_quiz');
    if ($submission) {
      $markup = "";
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $percent = $category['percent'];
        $normalized = $category['normalized'];
        $markup .= "<p>$category_name: <b>$percent% ($normalized)</b></p>";
      }
    }
    else {
      $markup = "No results found";
    }
    return [
      '#type' => 'markup',
      '#markup' => '',
      '#cache' => ['max-age' => 0],
    ];
  }
}
