<?php

namespace Drupal\workbc_cdq_career_match\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Default argument plugin to fetch Webform Submission by Current User.
 *
 * @ViewsArgumentDefault(
 *   id = "webform_submission_by_user",
 *   title = @Translation("Webform Submission by Current User")
 * )
 */
class WebformSubmissionByUser extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  /**
   * {@inheritdoc}
   */
  public function getArgument() {

    $current_user = \Drupal::currentUser();

    $current_path = \Drupal::service('path.current')->getPath();

    $webform_id = ltrim($current_path, "/quizzes/");
    $webform_id = rtrim($webform_id, "/results");
    $webform_id = str_replace("-", "_", $webform_id);

    $query = \Drupal::entityQuery('webform_submission')
    ->condition('uid', $current_user->id())
    ->condition('webform_id', $webform_id)
    ->range(0, 1)
    ->accessCheck(false);

    $results = $query->execute();
    if ($results) {
      $sid = array_shift($results);
    }

    if (!empty($sid)) {
      return $sid;
    }
    return FALSE;
  }

  /**
   * Resolves a URL alias to a node ID.
   *
   * @param string $alias
   *   The URL alias to resolve.
   *
   * @return int|null
   *   The node ID corresponding to the alias, or NULL if not found.
   */
  public function getNidByAlias($alias) {
    $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
    if (preg_match('/^\/node\/(\d+)$/', $path, $matches)) {
      return (int) $matches[1];
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
  * {@inheritdoc}
  */
  public function getCacheContexts() {
    return ['url', 'url.query_args'];
  }
}
