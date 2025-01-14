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

    $path = \Drupal::service('path.current')->getPath();
    $path = explode("/", $path);
    $submission = WebformSubmission::load($path[2]);

    $resultsUrl = Url::fromUri('route:workbc_cdq_custom.' . $submission->getWebform()->id() . '_results', [
      'query' => ['token' => $submission->getToken()]
    ])->toString();
    $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
    if (empty($previousUrl) || parse_url($previousUrl, PHP_URL_PATH) !== parse_url($resultsUrl, PHP_URL_PATH)) {{
      $previousUrl = $resultsUrl;
    }}

    $markup = <<<END
      <div class="cdq-back-link">
        <a href="$previousUrl" class="back-to-results"><img src="/themes/custom/workbc_cdq/assets/arrow-left.svg"/>Back to Quiz Results</a>
      </div>
    END;

    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

  }

  public function getCacheMaxAge() {
      return 0;
  }

}
