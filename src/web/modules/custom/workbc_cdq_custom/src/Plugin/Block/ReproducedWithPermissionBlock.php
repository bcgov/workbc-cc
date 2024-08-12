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
 *   id = "workbc_reproduced_with_permission",
 *   admin_label = @Translation("WorkBC Reproduced with Permission"),
 *   category = @Translation("WorkBC - CDQ"),
 * )
 */
class ReproducedWithPermissionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $url = "https://www.jobbank.gc.ca/abilities";
    $link = '<a href="' . $url . '" target="_blank">Government of Canada\'s National Job Bank</a>';
    $markup = "This quiz has been reproduced with permisssion from the " . $link . ".";

    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

  }

  public function getCacheMaxAge() {
      return 0;
  }

}
