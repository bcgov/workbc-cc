<?php

namespace Drupal\ccext\Breadcrumb;

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class CcextBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  public function applies(RouteMatchInterface $route_match) {
    $parameters = $route_match->getParameters()->all();
    if (isset($parameters['forms_steps'])) {
      return TRUE;
    }
    if (isset($parameters['node'])) {
      if ($parameters['node']->getType() === 'career_profile') {
        return FALSE;
      }
      return TRUE;
    }
    if (isset($parameters['term'])) {
      return FALSE;
    }
  }

  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addCacheContexts(["url"]);
    $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $page_title = \Drupal::service('title_resolver')
      ->getTitle($request, $route_match->getRouteObject());
    if (!empty($page_title)) {
      $breadcrumb->addLink(Link::createFromRoute($page_title, '<none>'));
    }

    return $breadcrumb;
  }

}
