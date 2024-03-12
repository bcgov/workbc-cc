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


  public function noc2021Migration() {

    $markup = "<<< NOC 2021 Migration >>><br><br>";

    $sandbox['concordance'] = loadConcordance();
    $sandbox['count'] = count($sandbox['concordance']);
    $sandbox['last_noc'] = 0;
    $sandbox['last_noc_2016'] = 0;
    $sandbox['last_noc_nid'] = 0;
    $sandbox['last_noc_type'] = "";
    $sandbox['noc_map'] = [];
    $sandbox['lookup'] = [];

    // create lookup map for checking if creating a split is necessary.
    // no split required if an existing career profile will be updated to this
    // NOC 2021 number later in the migration
    foreach ($sandbox['concordance'] as $key => $noc) {
      if ($noc[0] <> "0000" && !empty($noc[3])) {
        $sandbox['lookup'][$noc[3]] = $noc[0];
      }
    }
        
    // save original Career Profile paths for post migration validation.
    saveCareerProfilePaths();

    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $lFirst = true;
    $messages = [];
    foreach ($sandbox['concordance'] as $key => $noc) {
      if (!empty($noc)) {

        // split
        if ($noc[0] ==  "0000") {
          // assumes all split NOCs immediately follow the original NOC in concordance
          // only create split if doesn't already exist or will be created later in the migration
          if (!isset($sandbox['noc_map'][$noc[3]]) && !array_key_exists($noc[3], $sandbox['lookup'])) {
            // load source node
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($sandbox['last_noc_nid']);
    
            // clone node and update
            $split = $node->createDuplicate();
            $split->title = $noc[3];
            $split->field_noc_name = $noc[4];
            $split->created = time();
            $split->setPublished(TRUE);
            $split->save();
    
            $sandbox['noc_map'][$noc[3]] = ['noc2016' => $split->field_noc_2016->value, 'nid' => $split->id()];
    
            if ($sandbox['last_noc_type'] == "merge") {
              $message = "NOC 2021 data migration: Split after Merge " . $node->field_noc_2016->value . " -> " . $split->title->value;
            }
            else {
              $message = "NOC 2021 data migration: Split " . $node->field_noc_2016->value . " -> " . $split->title->value;
            }
          }
          else {
            if (isset($sandbox['noc_map'][$noc[3]])) {
              $message = "NOC 2021 data migration: Split already exists " . $sandbox['last_noc_2016'] . " -> " . $noc[3];
            }
            else {
              $message = "NOC 2021 data migration: Split not required " . $sandbox['last_noc_2016'] . " -> " . $noc[3];
            }
          }
        }
        else { 
          $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['title' => $noc[0]]);
          $node = array_shift($nodes);
          $node->field_noc_2016 = $node->title;
    
          // if record for NOC 2021 already exists merge
          if (isset($sandbox['noc_map'][$noc[3]])) {
            $node->setUnpublished();

            $node->title = "[ARCHIVED] " . $node->title->value;
            $node->field_noc_name = "[ARCHIVED] " . $node->field_noc_name->value;
    
            $message = "NOC 2021 data migration: Merge " . $node->field_noc_2016->value . " -> " . $noc[3];
            $sandbox['last_noc_type'] = "merge";
    
            // save old path alias
            $old_path = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $node->id(), $langcode);
            $node->save();
    
            $old_path = ltrim($old_path, '/');
            $new_path = 'node/'. $sandbox['noc_map'][$noc[3]]['nid'];

            $redirect_storage = \Drupal::entityTypeManager()->getStorage('redirect');
            $redirects = $redirect_storage->loadByProperties(['redirect_source__path' => $old_path]);
            $redirect = array_shift($redirects);
            
            $redirect->setRedirect($new_path);
            $redirect->save();
          }
          // else update
          else {
            $node->title = $noc[3];
            $node->field_noc_name = $noc[4];
    
            $sandbox['noc_map'][$noc[3]] = ['noc2016' => $node->field_noc_2016->value, 'nid' => $node->id()];
            $sandbox['last_noc_type'] = "update";
    
            $message = "NOC 2021 data migration: Update " . $node->field_noc_2016->value . " -> " . $node->title->value;
    
            $node->save();
          }
    
          // save noc and nid in case needed for split
          $sandbox['last_noc'] = $noc[3];
          $sandbox['last_noc_2016'] = $noc[0];
          $sandbox['last_noc_nid'] = $node->id();
        }

        \Drupal::logger('noc_2021_migration')->notice($message);
        $messages[] = $message;
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $markup,
    ];
  }


  public function noc2021MigrationEducation() {

    $markup = "<<< NOC 2021 Education Migration >>><br><br>";

    $updates = array();
    $updates[] = array(
      'old' => 'Apprenticeship Certificate',
      'teer' => '0',
      'term' => 'Management',
      'description' => 'Management responsibilities'
    );
    $updates[] = array(
      'old' => 'Degree',
      'teer' => '1',
      'term' => 'University Degree',
      'description' => "Completion of a university degree (bachelor's, master's, or doctorate); or Previous experience and expertise in subject matter knowledge from a related occupation found in TEER category 2 (when applicable).",
    );
    $updates[] = array(
      'old' => 'Diploma/Certificate Excluding Apprenticeship',
      'teer' => '2',
      'term' => 'College Diploma or Apprenticeship, 2 or more years',
      'description' => "Completion of a post-secondary education program of two to three years at community college, institute of technology, or CÉGEP; or Completion of an apprenticeship training program of two to five years; or Occupations with supervisory or significant safety (e.g. police officers and firefighters) responsibilities; or Several years of experience in a related occupation from TEER category 3 (when applicable).",
    );
    $updates[] = array(
      'old' => 'High School',
      'teer' => '3',
      'term' => 'College Diploma or Apprenticeship, less than 2 years',
      'description' => "Completion of a post-secondary education program of less than two years at community college, institute of technology or CÉGEP; or Completion of an apprenticeship training program of less than two years; or More than six months of on-the-job training, training courses or specific work experience with some secondary school education; or Several years of experience in a related occupation from TEER category 4 (when applicable).",
    );
    $updates[] = array(
      'old' => 'Less than High School',
      'teer' => '4',
      'term' => 'High School Diploma',
      'description' => "Completion of secondary school; or Several weeks of on-the-job training with some secondary school education; or Experience in a related occupation from TEER category 5 (when applicable).",
    );
    $updates[] = array(
      'old' => '',
      'teer' => '5',
      'term' => 'No Formal Education',
      'description' => "Short work demonstration and no formal educational requirements",
    );
  
    foreach ($updates as $update) {
      if (!empty($update['old'])) {
        $terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadByProperties(['name' => $update['old'], 'vid' => 'education_level']);
        $term = $terms[array_key_first($terms)];
        if ($term) {
          $term->name = $update['term'];
          $term->field_teer = $update['teer'];
          $term->description = $update['description'];
          $term->save();
        }
      }
      else {
        // create new term
        $term = \Drupal\taxonomy\Entity\Term::create([
          'name' => $update['term'],
          'vid' => 'education_level',
          'description' => $update['description'],
          'field_teer' => $update['teer'],
        ])->save();
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $markup,
    ];
  }



}
