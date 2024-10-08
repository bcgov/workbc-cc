<?php

namespace Drupal\workbc_cdq_career_match\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\webform\Entity\WebformSubmission;

/**
 * Provides a WorkBC Career Discover Quizzes block.
 *
 * @Block(
 *   id = "workbc_return_to_results",
 *   admin_label = @Translation("WorkBC Return to Quiz Results page"),
 *   category = @Translation("WorkBC - CDQ"),
 * )
 */
class ReturnToResultsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    global $base_url;

    $theme = \Drupal::theme()->getActiveTheme();
    $default_image_url = $base_url.'/'. $theme->getPath() .'/images/image.jpg';

    $path = \Drupal::service('path.current')->getPath();
    $path = explode("/", $path);
    $submission = WebformSubmission::load($path[2]);

    $results_link = Url::fromUri('route:workbc_cdq_custom.' . $submission->getWebform()->id() . '_results', [
      'query' => ['token' => $submission->getToken()]
    ])->toString();

    $markup = "";
    $markup .= '<div class="cdq-back-link">';
    $markup .= '<a href="#back" class="back-to-results"><img src="/themes/custom/workbc_cdq/assets/arrow-left.svg"/> Back to Quiz Results </a>';
    $markup .= '</div>';
  
    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

  }

  public function getCacheMaxAge() {
      return 0;
  }

}
