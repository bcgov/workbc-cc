<?php

use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;

/**
 * Bulk populate Career Profile Image field.
 *
 * As per ticket WBCAMS-1293
 */
function workbc_cdq_custom_deploy_1293_bulk_image_import(&$sandbox = NULL) {

  if (!isset($sandbox['career_profiles'])) {
    // load career profiles
    $connection = \Drupal::database();
    $query = $connection->select('node__field_noc');
    $query->condition('node__field_noc.bundle', 'career_profile');
    $query->addField('node__field_noc', 'entity_id');
    $query->addField('node__field_noc', 'field_noc_value');
    $sandbox['career_profiles'] = $query->execute()->fetchAll();
    $sandbox['count'] = count($sandbox['career_profiles']);
  }

  $message = "No action taken.";
  $career = array_shift($sandbox['career_profiles']);
  if (!empty($career)) {
    $node = Node::load($career->entity_id);


    $extension_list = \Drupal::service('extension.list.theme');
    $noc = $node->field_noc->value;
    $filepath = $extension_list->getPath('workbc_cdq') . '/assets/profiles/' . $noc . '-NOC-profile.png';
    if (!file_exists($filepath)) {
      $noc = $node->field_noc_2016->value;
      $filepath = $extension_list->getPath('workbc_cdq') . '/assets/profiles/' . $noc . '-NOC-profile.png';
    }

    if (file_exists($filepath)) {
      $directory = 'public://career_profile_images';

      $file_system = \Drupal::service('file_system');
      $file_system->prepareDirectory($directory, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
      $file_system->copy($filepath, $directory . '/' . basename($filepath), FileSystemInterface::EXISTS_REPLACE);

      $file = File::create([
        'filename' => basename($filepath),
        'uri' => $directory . '/' . basename($filepath),
        'status' => 1,
        'uid' => 1,
      ]);
      $file->save();

      $node->set('field_image', [
        'target_id' => $file->id(),
      ]);
      $node->save();
      $message = "Career Profile: " . $node->get("field_noc")->getString() . " - populate image field";
    }
    else {
      $message = "Career Profile: " . $node->get("field_noc")->getString() . " - missing image " . basename($filepath);
    }
  }

  $sandbox['#finished'] = empty($sandbox['career_profiles']) ? 1 : ($sandbox['count'] - count($sandbox['career_profiles'])) / $sandbox['count'];
  return t("[WBCAMS-542] $message");
}



