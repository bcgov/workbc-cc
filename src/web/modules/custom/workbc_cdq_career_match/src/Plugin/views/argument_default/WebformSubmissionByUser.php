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
    if (!preg_match('/quizzes\/(.*?)\/results/', $current_path, $matches)) {
      return false;
    }
    $webform_id = str_replace('-', '_', $matches[1]);

    $session = \Drupal::request()->getSession();
    $token_session = $session->get($webform_id.'_token')?: "";

    $token_url = \Drupal::request()->get('token');

    if ($token_url) {
      $token = $token_url;
    }
    else {
      $token = $token_session;
    }

    $query = \Drupal::entityQuery('webform_submission')
      ->condition('token', $token)
      ->condition('webform_id', $webform_id)
      ->range(0, 1)
      ->sort('changed', 'DESC')
      ->accessCheck(false);

    $results = $query->execute();
    if ($results) {
      return array_shift($results);
    }
    return false;
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
