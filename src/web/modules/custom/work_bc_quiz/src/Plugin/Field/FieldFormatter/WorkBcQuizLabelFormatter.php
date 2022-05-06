<?php

namespace Drupal\work_bc_quiz\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a formatter for the double reference field.
 *
 * @FieldFormatter(
 *   id = "double_reference_label",
 *   label = @Translation("Labels"),
 *   description = @Translation("Display the labels for both referenced entities."),
 *   field_types = {
 *     "work_bc_quiz"
 *   }
 * )
 */
class WorkBcQuizLabelFormatter extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'ar_link' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['ar_link'] = [
      '#title' => $this->t('Added reference: Link label to the referenced entity'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('ar_link'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary[] = $this->getSetting('ar_link') ? $this->t('Added reference: Link to the referenced entity') : $this->t('Added reference: No link');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    $output_as_link = $this->getSetting('ar_link');

    // Get the entity type referenced by the added field.
    $settings = $this->fieldDefinition->getSettings();
    $ar_target_type = $settings['ar_target_type'];

    // Get the contents of the field.
    $values = $items->getValue();

    // Loop through each of the primary reference elements as provided
    // by the parent class to add our added reference.
    foreach ($elements as $delta => $entity) {
      // Shift the primary reference render array into its own section.
      $primary_reference_elements = $elements[$delta];
      $elements[$delta] = [];
      $elements[$delta]['primary_reference'] = $primary_reference_elements;

      // Get the target entity for the added reference.
      $ar_target_id = $values[$delta]['ar_target_id'];
      /* print_r($ar_target_id);
      die; */
      $entity = \Drupal::entityTypeManager()->getStorage($ar_target_type)->load($ar_target_id);

      $label = (isset($entity)) ? $entity->label() : '';

      // If the link is to be displayed and the entity has a uri, display a
      // link.
      if ($output_as_link && (isset($entity) && !$entity->isNew())) {
        try {
          $uri = $entity->toUrl();
        }
        catch (UndefinedLinkTemplateException $e) {
          // This exception is thrown by \Drupal\Core\Entity\Entity::urlInfo()
          // and it means that the entity type doesn't have a link template nor
          // a valid "uri_callback", so don't bother trying to output a link for
          // the rest of the referenced entities.
          $output_as_link = FALSE;
        }
      }

      if ($output_as_link && isset($uri) && !$entity->isNew()) {
        $elements[$delta]['added_reference'] = [
          '#type' => 'link',
          '#title' => $label,
          '#url' => $uri,
          '#options' => $uri->getOptions(),
        ];

        if (!empty($items[$delta]->_attributes)) {
          $elements[$delta]['added_reference']['#options'] += ['attributes' => []];
          $elements[$delta]['added_reference']['#options']['attributes'] += $items[$delta]->_attributes;
          // Unset field item attributes since they have been included in the
          // formatter output and shouldn't be rendered in the field template.
          unset($items[$delta]->_attributes);
        }
      }
      else {
        $elements[$delta]['added_reference'] = ['#plain_text' => $label];
      }
      $elements[$delta]['added_reference']['#cache']['tags'] = ($entity) ? $entity->getCacheTags() : '';
    }

    return $elements;
  }

}
