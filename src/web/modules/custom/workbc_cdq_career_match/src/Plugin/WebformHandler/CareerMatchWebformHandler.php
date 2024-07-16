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
ksm("postSave");
ksm($webform_submission->getState());
ksm(getenv("DB_NAME"));
ksm(getenv("ONET_PASSWORD"));
ksm(getenv("ONET_PASSWORD"));
    if ($webform_submission->getState() == "completed" || $webform_submission->getState()) {
      ksm("savin' stuff");
      $scores = getSubmissionScore($webform_submission);
      ksm($scores);
      $matches = matchCareers($webform_submission, $scores);
      ksm($matches);
      if (empty($matches)) {
        ksm("no matches found");
        return;
      }
      $matches = array_slice($matches, 0, 20);
      $nids = \Drupal::database()->query("SELECT field_noc_value, entity_id FROM {node__field_noc} WHERE field_noc_value IN (:nocs[])", [
        ':nocs[]' => array_map(function($match) { return $match['noc']; }, $matches)
      ])->fetchAllAssoc('field_noc_value');
      if ($update) {
        $query = \Drupal::database()->delete('workbc_cdq_career_match');
        $query->condition('sid', $webform_submission->id());
        $query->execute();
      }
      foreach ($matches as $match) {
        $query = \Drupal::database()->insert('workbc_cdq_career_match');
        $query->fields(['sid', 'nid', 'career_match']);
        $query->values([$webform_submission->id(), $nids[$match['noc']]->entity_id, $match['match']]);
        $query->execute();
      }
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
