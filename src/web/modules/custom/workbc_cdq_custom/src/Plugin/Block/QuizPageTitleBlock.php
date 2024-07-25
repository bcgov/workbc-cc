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

    $markup = "";
    $class = "";

    $current_quiz = getCurrentQuiz();
    if ($current_quiz) {
      $quiz = workbc_cdq_quiz_info($current_quiz);
      $markup .= '<h2 class="cdq-quiz-title">' . $quiz['title'] . '</h2>';
      $markup .= '<div class="sub-title">';
      $markup .= $quiz['subtitle'];
      $markup .= '</div>';
      $markup .= '<div class="node-questions">';
      $markup .= '<div class="quiz-questions"> <span></span>' . $quiz['count'] . ' questions</div>';
      $markup .= '<div class="quiz-duration"> <span></span>' . $quiz['time'] .'</div>';
      $markup .= '</div>';
    }

    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

  }

  public function getCacheMaxAge() {
      return 0;
  }

}
