<?php

/**
 * @file
 * Custom Controller class.
 */

namespace Drupal\workbc_cdq_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;

class QuizResultsController extends ControllerBase {

  public function abilities_quiz_results() {
    
    $submission = getUserSubmission('abilities_quiz');
    if ($submission) {
      $info = [
        'General Learning Ability' => [
          'color' => '880364',
          'icon' => 'general-learning-ability',
        ],
        'Verbal Ability' => [
          'color' => '216C06',
          'icon' => 'verbal-ability',
        ],
        'Numerical Ability' => [
          'color' => '2671CA',
          'icon' => 'numerical-ability',
        ],
        'Spatial Perception' => [
          'color' => '002855',
          'icon' => 'spatial-perception',
        ],
        'Form Perception' => [
          'color' => '234075',
          'icon' => 'form-perception',
        ],
        'Clerical Perception' => [
          'color' => '216C06',
          'icon' => 'clerical-perception',
        ],
        'Motor Co-ordination' => [
          'color' => '007EB4',
          'icon' => 'motor-co-ordination',
        ],
        'Finger Dexterity' => [
          'color' => '880364',
          'icon' => 'finger-dexterity',
        ],
        'Manual Dexterity' => [
          'color' => '510B76',
          'icon' => 'manual-dexterity',
        ],
      ];

      $score = getSubmissionScore($submission);
      foreach ($score as $category_name => $category) {
        $score[$category_name]['description'] = $category['term']->description__value;
        $score[$category_name]['color'] = $info[$category_name]['color'];
        $score[$category_name]['image'] = $info[$category_name]['icon'];
      }
      $categoryTop = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text');
      $categoryAll = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text');
      $emailBody = results_email_body($submission);
    }
    else {
      $score = NULL;
      $categoryTop = NULL;
      $categoryAll = NULL;
      $emailBody = NULL;
    }

    $this->setResultsSortParameters();
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $categoryTop,
      '#category_all' => $categoryAll,
      '#category' => "Aptitudes",
      '#email_subject' => results_email_subject($submission->getWebform()->get('title')),
      '#email_body' => $emailBody,
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
      $categoryTop = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text');
      $categoryAll = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text');
      $emailBody = results_email_body($submission);
    }
    else {
      $score = NULL;
      $categoryTop = NULL;
      $categoryAll = NULL;
      $emailBody = NULL;
    }

    $this->setResultsSortParameters();
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $categoryTop,
      '#category_all' => $categoryAll,
      '#category' => "Work Preferences",
      '#email_subject' => results_email_subject($submission->getWebform()->get('title')),
      '#email_body' => $emailBody,
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
      $categoryTop = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text');
      $categoryAll = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text');
      $emailBody = results_email_body($submission);
    }
    else {
      $score = NULL;
      $categoryTop = NULL;
      $categoryAll = NULL;
      $emailBody = NULL;
    }

    $this->setResultsSortParameters();
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $categoryTop,
      '#category_all' => $categoryAll,
      '#category' => "Interests",
      '#email_subject' => results_email_subject($submission->getWebform()->get('title')),
      '#email_body' => $emailBody,
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
      $categoryTop = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text');
      $categoryAll = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text');
      $emailBody = results_email_body($submission);
    }
    else {
      $score = NULL;
      $categoryTop = NULL;
      $categoryAll = NULL;
      $emailBody = NULL;
    }

    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $categoryTop,
      '#category_all' => $categoryAll,
      '#category' => "Types of Smart",
      '#email_subject' => results_email_subject($submission->getWebform()->get('title')),
      '#email_body' => $emailBody,
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
      $categoryTop = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text');
      $categoryAll = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text');
      $emailBody = results_email_body($submission);
    }
    else {
      $score = NULL;
      $categoryTop = NULL;
      $categoryAll = NULL;
      $emailBody = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_categories',
      '#category_top' => $categoryTop,
      '#category_all' => $categoryAll,
      '#category' => "Learning Styles",
      '#email_subject' => results_email_subject($submission->getWebform()->get('title')),
      '#email_body' => $emailBody,
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
        'Interacting with others' => [
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
      $categoryTop = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'top_text');
      $categoryAll = $submission->getWebform()->getThirdPartySetting('workbc_cdq_career_match', 'all_text');
      $emailBody = results_email_body($submission);
    }
    else {
      $score = NULL;
      $categoryTop = NULL;
      $categoryAll = NULL;
      $emailBody = NULL;
    }
    return [
      '#theme' => 'workbc_cdq_quiz_results_work_values',
      '#category_top' => $categoryTop,
      '#category_all' => $categoryAll,
      '#category' => "Values",
      '#email_subject' => results_email_subject($submission->getWebform()->get('title')),
      '#email_body' => $emailBody,
      '#score' => $score,
      '#cache' => ['max-age' => 0],
    ];
  }

/**
  * {@inheritdoc}
  */
  protected function setResultsSortParameters() {
    $order = is_null(\Drupal::request()->get('order')) ? "" : \Drupal::request()->get('order');
    $sort = is_null(\Drupal::request()->get('sort')) ? "" :  \Drupal::request()->get('sort');

    $request = \Drupal::request();
    $session = $request->getSession();
    $session->set('results_order', $order);
    $session->set('results_sort', $sort);

  }

}
