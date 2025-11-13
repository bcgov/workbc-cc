<?php

namespace Drupal\workbc_cdq_custom\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
* Class AdminSettingsForm.
*
* @package Drupal\workbc_cdq_custom\Form
*/
class AdminSettingsForm extends ConfigFormBase {

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'workbc_cdq_custom_admin_settings_form';
  }

  /**
  * {@inheritdoc}
  */
  protected function getEditableConfigNames() {
    return [
      'workbc_cdq_custom.settings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('workbc_cdq_custom.settings');

    $default = $config->get('results_email_subject', '');
    $form['results_email_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Results email subject'),
      '#description' => 'Use token [cdq:quiz-name] for current quiz name.',
      '#default_value' => $default,
    ];

    $default = $config->get('results_email_body', '');
    $form['results_email_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Results email text'),
      '#description' => "Use token [cdq:quiz-name] for current quiz name, [cdq:results-link] for current user's results link and [site:url] for home page link.",
      '#default_value' => $default,
      '#rows' => 10,
    ];

    $default = $config->get('compare_email_subject', '');
    $form['compare_email_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Compare Careers email subject'),
      '#description' => 'Use token [cdq:quiz-name] for current quiz name.',
      '#default_value' => $default,
    ];

    $default = $config->get('compare_email_body', '');
    $form['compare_email_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Compare Careers email text'),
      '#description' => "Use token [cdq:quiz-name] for current quiz name and [cdq:compare-careers-link] for current user's compare careers link.",
      '#default_value' => $default,
      '#rows' => 10,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('workbc_cdq_custom.settings');
    $config->set('results_email_subject', $form_state->getValue('results_email_subject', ''));
    $config->set('results_email_body', $form_state->getValue('results_email_body', ''));
    $config->set('compare_email_subject', $form_state->getValue('compare_email_subject', ''));
    $config->set('compare_email_body', $form_state->getValue('compare_email_body', ''));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
