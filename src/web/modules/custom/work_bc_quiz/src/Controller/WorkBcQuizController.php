<?php

namespace Drupal\work_bc_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\redirect\Entity\Redirect;

/**
 *
 */
class WorkBcQuizcontroller extends ControllerBase {

  /**
   *
   */
  public function clearNodes() {
    $site_settings = \Drupal::service('site_settings.loader');
    $settings = $site_settings->loadAll();
    $quizLifespanHours = (isset($settings['cron_settings']['cron'])) ? $settings['cron_settings']['cron'] : 24;
    $quiz_types = ['abilities_quiz', 'work_preferences_quiz', 'interests_quiz', 'multiple_intelligences_quiz', 'learning_styles_quiz', 'work_values_quiz'];
    $storage_handler = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')->accessCheck(FALSE)
      ->condition('created', strtotime('-'.$quizLifespanHours.' hour'), '<=')
      ->condition('type', $quiz_types, 'IN');
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
  
  public function getQuizTypes($type=''){
    $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
    $quiz_types = [];
    $labelsToChange = [];
    $quiz_type = \Drupal::request()->query->get('quiz');
    $ar_weight = 0;
    foreach ($node_types as $node_type) {
      $fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $node_type->id());
      foreach ($fields as $field_name => $field_definition) {
        if ($field_definition->getType() == "work_bc_quiz") {
          if($quiz_type == $node_type->id()){
            //ar_weight
            $options_array = $fields[$field_name]->get('settings');
            if (isset($options_array['added_reference']['ar_weight'])) {
              $ar_weight = $options_array['added_reference']['ar_weight'];
            }else {
              $ar_weight++;
            }
            $labelsToChange[$field_name]['value'] = $field_definition->getLabel();
            $labelsToChange[$field_name]['weight'] = $ar_weight;
          }
          if($type == 'object'){
            $quiz_types[$node_type->id()] = $node_type->label();
          }else {
            $quiz_types[] = $node_type->id();
          }
          break;
        }
      }
    }
    return $quiz_types;
  }


  public function noc2021Validation() {
    
    $errors = [];
    $errors = noc2021ProcessValidation();
    
    if (empty($errors)) {
      $markup = "<p>No validation errors found.</p>";
    }
    else {
      $markup = "";
      foreach ($errors as $error) {
        $markup .= "<p>" . $error . "</p>";
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $markup,
    ];
  }

}
