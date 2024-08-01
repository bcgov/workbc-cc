<?php

/**
 * @file
 * Custom Controller class.
 */

namespace Drupal\workbc_cdq_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;

define ("NEW_LINE", "%0D%0A");

class QuizResultsController extends ControllerBase {

  public function abilities_quiz_results() {
    $id = 'abilities_quiz';
    $submission = getUserSubmission($id);
    if ($submission) {
      $info = [
        'General learning ability' => [
          'color' => '880364',
          'icon' => 'general-learning-ability',
        ],
        'Verbal ability' => [
          'color' => '216C06',
          'icon' => 'verbal-ability',
        ],
        'Numerical ability' => [
          'color' => '2671CA',
          'icon' => 'numerical-ability',
        ],
        'Spatial perception' => [
          'color' => '002855',
          'icon' => 'spatial-perception',
        ],
        'Form perception' => [
          'color' => '234075',
          'icon' => 'form-perception',
        ],
        'Clerical perception' => [
          'color' => '216C06',
          'icon' => 'clerical-perception',
        ],
        'Motor co-ordination' => [
          'color' => '007EB4',
          'icon' => 'motor-co-ordination',
        ],
        'Finger dexterity' => [
          'color' => '880364',
          'icon' => 'finger-dexterity',
        ],
        'Manual dexterity' => [
          'color' => '510B76',
          'icon' => 'manual-dexterity',
        ],
      ];

      $url = Url::fromRoute('entity.webform.canonical', ['webform' => $id, 'token' => $submission->getToken()]);
      $quiz_link = $url->toString();

      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text'),
      '#category_all' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function work_preferences_quiz_results() {
    $submission = getUserSubmission('work_preferences_quiz');
    if ($submission) {
      $info = [
        'Social' => [
          'color' => '216C06',
          'icon' => 'social-2',
        ],
        'Directive' => [
          'color' => '002857',
          'icon' => 'directive',
        ],
        'Innovative' => [
          'color' => '510B76',
          'icon' => 'innovative',
        ],
        'Methodical' => [
          'color' => '880364',
          'icon' => 'methodical',
        ],
        'Objective' => [
          'color' => '2A7DE2',
          'icon' => 'objective',
        ],
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
      }
    }
    else {
      $score = NULL;
    }

    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text'),
      '#category_all' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text'),
      '#email_subject' => "WorkBC's Career Discovery Quizzes - Work Preferences Quiz results",
      '#email_body' => $this->results_email_body($submission),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function interests_quiz_results() {
    $submission = getUserSubmission('interests_quiz');
    if ($submission) {
      $info = [
        'Realistic' => [
          'color' => '002857',
          'icon' => 'realistic',
        ],
        'Enterprising' => [
          'color' => '89226E',
          'icon' => 'enterprising',
        ],
        'Conventional' => [
          'color' => '510B76',
          'icon' => 'conventional',
        ],
        'Investigative' => [
          'color' => '2671CA',
          'icon' => 'investigative',
        ],
        'Artistic' => [
          'color' => '216C06',
          'icon' => 'artistic',
        ],
        'Social' => [
          'color' => '007EB4',
          'icon' => 'social-1',
        ],
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text'),
      '#category_all' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function multiple_intelligences_quiz_results() {
    $submission = getUserSubmission('multiple_intelligences_quiz');
    if ($submission) {
      $info = [
        'Self Smart' => [
          'color' => '510B76',
          'icon' => 'self-smart',
        ],
        'Nature Smart' => [
          'color' => '216C06',
          'icon' => 'nature-smart',
        ],
        'Sound Smart' => [
          'color' => '002857',
          'icon' => 'sound-smart',
        ],
        'People Smart' => [
          'color' => '2A7DE2',
          'icon' => 'people-smart',
        ],
        'Number Smart' => [
          'color' => '002857',
          'icon' => 'number-smart',
        ],
        'Body Smart' => [
          'color' => '007EB4',
          'icon' => 'body-smart',
        ],
        'Word Smart' => [
          'color' => '880364',
          'icon' => 'word-smart',
        ],
        'Picture Smart' => [
          'color' => '216C06',
          'icon' => 'picture-smart',
        ],
      ];
      $score = getSubmissionScore($submission);


      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text'),
      '#category_all' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function learning_styles_quiz_results() {
    $submission = getUserSubmission('learning_styles_quiz');
    if ($submission) {
      $info = [
        'Hearing' => [
          'color' => '2A7DE2',
          'icon' => 'hearing',
        ],
        'Seeing' => [
          'color' => '510B76',
          'icon' => 'seeing',
        ],
        'Doing' => [
          'color' => '002857',
          'icon' => 'doing',
        ],
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text'),
      '#category_all' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

  public function work_values_quiz_results() {
    $submission = getUserSubmission('work_values_quiz');
    if ($submission) {
      $info = [
        'Your work motivations' => [
          'color' => '002857',
          'icon' => 'work-motivation',
        ],
        'Your preferred work setting' => [
          'color' => '1E75DF',
          'icon' => 'work-settings',
        ],
        'How you like to interact with others' => [
          'color' => '510B76',
          'icon' => 'interacting-with-others',
        ],
        'Your work styles' => [
          'color' => '880364',
          'icon' => 'work-style',
        ],
      ];
      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
        $score[$category_name]['important'] = array_map(function($answer) {
          return $answer['title'];
        }, array_filter($score[$category_name]['answers'], function($answer) {
          return $answer['value'] == '2';
        }));
        $score[$category_name]['somewhat'] = array_map(function($answer) {
          return $answer['title'];
        }, array_filter($score[$category_name]['answers'], function($answer) {
          return $answer['value'] == '1';
        }));
      }
    }
    else {
      $score = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_work_values',
      '#category_top' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text'),
      '#category_all' => $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text'),
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }


  function results_email_body($submission) {

    $id = $submission->getWebform()->id();

    $options = [
      'absolute' => TRUE,
      'https' => TRUE,
    ];
    $home_link = Url::fromRoute('<front>', [], $options)->toString();

    $options['query'] = ['token' => $submission->getToken()];
    $results_link = Url::fromUri('route:workbc_cdq_custom.' . $id . '_results', $options)->toString();

    $body = "View your Work Preferences Quiz results: " . $results_link;
    $body .= NEW_LINE . NEW_LINE;
    $body .= "Your result are available for next 14 days. print or download to save your results.";
    $body .= NEW_LINE . NEW_LINE;
    $body .= "Discover more! Complete other career and personality quizzes and find a career path that's right for you.";
    $body .= NEW_LINE . NEW_LINE;    
    $body .= "Career Discovery Quizzes: " . $home_link;
    $body .= NEW_LINE . NEW_LINE;    
    $body .= "Find more resources on careers, funding, education and finding jobs at https://WorkBC.ca.";

    return $body;
  }

  
  function compare_email_body($submission) {

    $id = $submission->getWebform()->id();

    $options = [
      'absolute' => TRUE,
      'https' => TRUE,
    ];
    $home_link = Url::fromRoute('<front>', [], $options)->toString();

    $options['query'] = ['token' => $submission->getToken()];
    $compare_link = Url::fromUri('route:workbc_cdq_custom.' . $id . '_results', $options)->toString();

    $body = "View your compared careers: " . $compare_link;
    $body .= NEW_LINE . NEW_LINE;
    $body .= "WorkBC's Career Discovery Quizzes help you learn more about your preferences and discover careers that suit you.";

    return $body;
  }

}
