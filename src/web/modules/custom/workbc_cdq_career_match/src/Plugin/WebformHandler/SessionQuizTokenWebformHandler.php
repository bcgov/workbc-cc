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
 *   id = "session_quiz_token",
 *   label = @Translation("Session Quiz Token"),
 *   category = @Translation("WorkBC CDQ"),
 *   description = @Translation("WorkBC Session Quiz Token webform submission handler."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_IGNORED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class SessionQuizTokenWebformHandler extends WebformHandlerBase {

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
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    
    \Drupal::logger('workbc_cdq_career_match')->notice("Session Quiz -oken @token", array('@quiz_id' => $webform_submission->getWebform()->id(), '@token' => $webform_submission->getToken()));
    
    $form = \Drupal::request()->get('ajax_form');
    if (is_null($form)) {
      $request = \Drupal::request();
      $session = $request->getSession();
      \Drupal::logger('workbc_cdq_career_match')->notice("Session Quiz - set @quiz_id token @token", array('@quiz_id' => $webform_submission->getWebform()->id(), '@token' => $webform_submission->getToken()));
      $session->set($webform_submission->getWebform()->id().'_token', $webform_submission->getToken());
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
