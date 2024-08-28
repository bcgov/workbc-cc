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
 *   id = "workbc_onet_notice",
 *   admin_label = @Translation("WorkBC ONET Notice"),
 *   category = @Translation("WorkBC - CDQ"),
 * )
 */
class OnetNoticeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $quiz = getCurrentQuiz();

    if ($quiz['id'] == "interests_quiz") {
      $url1 = "https://www.onetcenter.org/tools.html";
      $url2 = "https://www.onetcenter.org/license_toolsdev.html";
      $link1 = '<a href="' . $url1 . '" target="_blank">O*NET Career Exploration Tools</a>';
      $link2 = '<a href="' . $url2 . '" target="_blank">O*NET Tools Developer License</a>';
    
      $markup = "This page includes information from the " . $link1 . " by the U.S. Department of Labor, Employment and Training Administration (USDOL/ETA). Used under the " . $link2 . ". O*NET® is a trademark of USDOL/ETA. WorkBC’s Interests Quiz has modified all or some of this information. USDOL/ETA has not approved, endorsed, or tested these modifications.";
    }
    else {
      $markup = "";
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
