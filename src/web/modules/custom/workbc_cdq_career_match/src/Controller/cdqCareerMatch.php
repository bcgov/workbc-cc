<?php

declare(strict_types=1);

namespace Drupal\workbc_cdq_career_match\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;

/**
 * Updates selected status for 
 */
class cdqCareerMatch extends ControllerBase {

  /**
   * Returns Ajax Response containing the current time.
   *
   */
  public function update_selected(): AjaxResponse {
   
    $id = (int)\Drupal::request()->get('id');
    $selected = \Drupal::request()->get('selected') == "true" ? 1 : 0;

    $query = \Drupal::database()->update('workbc_cdq_career_match');
    $query->fields(['selected' => $selected]);
    $query->condition('id', $id);
    $query->execute();
    
    $response = new AjaxResponse();

    // A status message added in the default location.
    $response->addCommand(new MessageCommand('Your changes have been saved.'));

    return $response;
  }

  public function clear_selected(): AjaxResponse {
    $sid = (int)\Drupal::request()->get('sid');
    
    $query = \Drupal::database()->update('workbc_cdq_career_match');
    $query->fields(['selected' => 0]);
    $query->condition('sid', $sid);
    $query->condition('selected', 1);
    $query->execute();
    
    $response = new AjaxResponse();

    // A status message added in the default location.
    $response->addCommand(new MessageCommand('Your selections have been cleared.'));

    return $response;
  }

}