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
  public function getCareerCodes($parameters=''){
    if(empty($parameters)){
      $response = ['response'=>401, 'message'=>'Unauthorized', 'data'=>[]];
    }else {
      $eservice = new eServices();
      $data = $eservice->fnGetCodes($parameters);
      $data = json_decode($data['response'], true);

      if(!empty($data)){
        $response = ['response'=>200, 'message'=>'Success', 'data'=>$data];
      }else {
        $response = ['response'=>403, 'message'=>'No Data found', 'data'=>[]];
      }
      return $response;
    }
  }

}
