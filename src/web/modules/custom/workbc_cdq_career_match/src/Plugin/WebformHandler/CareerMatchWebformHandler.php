<?php

namespace Drupal\workbc_cdq_career_match\Plugin\WebformHandler;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform career match handler.
 *
 * @WebformHandler(
 *   id = "career_match",
 *   label = @Translation("Career Match"),
 *   category = @Translation("WorkBC CDQ"),
 *   description = @Translation("WorkBC Career Match webform submission handler."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_IGNORED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class CareerMatchWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'debug' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function postDelete(WebformSubmissionInterface $webform_submission) {
    $query = \Drupal::database()->delete('workbc_cdq_career_match');
    $query->condition('sid', $webform_submission->id());
    $query->execute();
  }


  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    $nids = \Drupal::entityQuery('node')
              ->accessCheck(TRUE)
              ->condition('type','career_profile')
              ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $data = $webform_submission->getData();
    $total = array_sum($data);

    $score = "";
    foreach ($data as $value) {
      $score .= $value;
    }
    $profiles = [];
    $first = true;
    foreach ($nodes as $profile) {
      $seed = intval($profile->field_noc->value . $score);
      srand($seed);
      $profiles[] = [
        'sid' =>  $webform_submission->id(),
        'nid' => $profile->id(),
        'career_match' => rand(1, 99) > 90 ? rand(30, 99) : 0,
      ];
    }

    array_multisort( array_column($profiles, "career_match"), SORT_DESC, $profiles );
    $matches = array_slice($profiles, 0, 20);

    if ($update) {
      $query = \Drupal::database()->delete('workbc_cdq_career_match');
      $query->condition('sid', $webform_submission->id());
      $query->execute();
    }

    foreach ($matches as $match) {
      $query = \Drupal::database()->insert('workbc_cdq_career_match');
      $query->fields(['sid', 'nid', 'career_match']);
      $query->values([$match['sid'], $match['nid'], $match['career_match']]);
      $query->execute();
    }

  }



  /**
   * Display the invoked plugin method to end user.
   *
   * @param string $method_name
   *   The invoked method name.
   * @param string $context1
   *   Additional parameter passed to the invoked method name.
   */
  protected function debug($method_name, $context1 = NULL) {
    if (!empty($this->configuration['debug'])) {
      $t_args = [
        '@id' => $this->getHandlerId(),
        '@class_name' => get_class($this),
        '@method_name' => $method_name,
        '@context1' => $context1,
      ];
      $this->messenger()->addWarning($this->t('Invoked @id: @class_name:@method_name @context1', $t_args), TRUE);
    }
  }

}
