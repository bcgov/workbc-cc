<?php

namespace Drupal\workbc_cdq_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

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
    $url = self::getCredit() ?? '';
    $markup = $this->t('This quiz has been reproduced with permission from the @link.', [
      '@link' => Link::fromTextAndUrl(t("Government of Canada's National Job Bank"), Url::fromUri($url, [
        'attributes' => [
          'target' => '_blank',
          'rel' => 'noopener noreferrer'
        ]
      ]))->toString()
    ]);
    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

  }

  /**
   * Only display block Interests Quiz;
   */
  protected function blockAccess(AccountInterface $account) {

    if (!self::getCredit()) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  public function getCacheMaxAge() {
      return 0;
  }

  private static function getCredit() {
    $quiz = getCurrentQuiz();
    $credits = [
      'abilities_quiz' => 'https://www.jobbank.gc.ca/abilities',
      'work_preferences_quiz' => 'https://www.jobbank.gc.ca/dpt',
      'multiple_intelligences_quiz' => 'https://www.jobbank.gc.ca/intelligence',
      'learning_styles_quiz' => 'https://www.jobbank.gc.ca/seeheardo',
      'work_values_quiz' => 'https://www.jobbank.gc.ca/workvalue',
      'interests_quiz' => false,
    ];
    return !empty($quiz) && array_key_exists($quiz['id'], $credits) ? $credits[$quiz['id']] : false;
  }
}
