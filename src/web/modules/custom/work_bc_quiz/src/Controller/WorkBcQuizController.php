<?php

namespace Drupal\work_bc_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class WorkBcQuizcontroller extends ControllerBase {

  /**
   *
   */
  public function clearNodes() {
    $quiz_types = ['abilities_quiz', 'work_preferences_quiz', 'interests_quiz', 'multiple_intelligences_quiz', 'learning_styles_quiz', 'work_values_quiz'];
    $storage_handler = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')->accessCheck(FALSE)
      ->condition('created', strtotime('-24 hour'), '<=')
      ->condition('type', $quiz_types, 'IN')
      ->range(0, 2);
    $nids = $query->execute();
    if (!empty($nids)) {
      $nodes = $storage_handler->loadMultiple($nids);
      $storage_handler->delete($nodes);
      print "Old quiz nodes have been deleted";
    }
    else {
      print "There are no quiz nodes to be deleted";
    }
    die;
  }

}
