<?php

namespace Drupal\ccext\Entity;

use Drupal\entity_comparison\Entity\EntityComparison;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * The Entity Comparison class extends.
 */
class CcextEntityComparison extends EntityComparison {

  /**
   * {@inheritdoc}
   */
  public function getLink($entity_id, $use_ajax = FALSE) {
    // Get session service.
    $session = \Drupal::service('session');

    // Get vurrent user's id.
    $uid = \Drupal::currentUser()->id();
    $route_match = \Drupal::routeMatch();
    $node = $route_match->getParameter('node');
    $career_compare = $route_match->getParameter('node_id');
    $node_id = !empty($node) ? $node->id() : $career_compare;

    // Get entity type and bundle type.
    $entity_type = $this->getTargetEntityType();
    $bundle_type = $this->getTargetBundleType();

    // Get current entity comparison list.
    $entity_comparison_list = $session->get("career_comparison_$node_id" . "_$uid");

    if (empty($entity_comparison_list)) {
      $add_link = TRUE;
    }
    else {
      if (!empty($entity_comparison_list[$entity_type][$bundle_type][$this->id()]) &&
      in_array($entity_id, $entity_comparison_list[$entity_type][$bundle_type][$this->id()])) {
        $add_link = FALSE;
      }
      else {
        $add_link = TRUE;
      }
    }

    // Get the url object from route.
    $url = Url::fromRoute('ccext.action', [
      'entity_comparison_id' => $this->id(),
      'entity_id' => $entity_id,
      'node_id' => $node_id,
    ], [
      'query' => \Drupal::service('redirect.destination')->getAsArray(),
      'attributes' => [
        'id' => 'career-compare-' . $this->id() . '-' . $entity_id . '-' . $node_id,
        'data-entity-comparison' => $this->id() . '-' . $entity_id . '-' . $node_id,
        'class' => [
          $use_ajax ? 'use-ajax' : '',
          $add_link ? 'add-link' : 'remove-link',
        ],
      ],
    ]);

    // Set link text.
    $link_text = $add_link ? $this->getAddLinkText() : $this->getRemoveLinkText();

    // Return with the link.
    return Link::fromTextAndUrl($link_text, $url);
  }

}
