<?php

namespace Drupal\workbc_cdq_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;


/**
 * Provides a WorkBC Career Discover Quizzes block.
 *
 * @Block(
 *   id = "workbc_quiz_page_title",
 *   admin_label = @Translation("WorkBC Quiz Page Title"),
 *   category = @Translation("WorkBC - CDQ"),
 * )
 */
class QuizPageTitleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $colors = [
      'abilities_quiz' => 'cdq-quiz-dark-blue',
      'work_preferences_quiz' => 'cdq-quiz-purple',
      'interests_quiz' => 'cdq-quiz-blue',
      'multiple_intelligencies_quiz' => 'cdq-quiz-green',
      'learning_styles_quiz' => 'cdq-quiz-light-blue',
      'work_values_quiz' => 'cdq-quiz-pink'
    ];

    $path = \Drupal::service('path.current')->getPath();
    $path = explode("/", $path);

    $quiz = workbc_cdq_quiz_info($path[2]);


    $markup = "";
    $markup .= '<h2 class="cdq-quiz-title">' . $quiz['title'] . '</h2>';
    $markup .= '<div class="sub-title">';
    $markup .= $quiz['subtitle'];
    $markup .= '</div>';
    $markup .= '<div class="node-questions">';
    $markup .= '<div class="quiz-questions"> <span></span>' . $quiz['count'] . ' questions</div>';
    $markup .= '<div class="quiz-duration"> <span></span>' . $quiz['time'] .'</div>';
    $markup .= '</div>';


    return array(
      '#type' => 'markup',
      '#markup' => $markup,
      '#attributes' => [
        'class' => [$colors[$path[2]]]
      ]
    );

  }

  public function getCacheMaxAge() {
      return 0;
  }

}
