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

    $request = \Drupal::request();
    $session = $request->getSession();
    $order = $session->get("results_order");
    $sort = $session->get("results_sort");

    $previousUrl = Url::fromUri('route:workbc_cdq_custom.' . $submission->getWebform()->id() . '_results', [
      'query' => ['token' => $submission->getToken()]
    ])->toString();
    if ($order && $sort) {
      $previousUrl .= "&order=" . $order . "&sort=" . $sort;
    }

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
