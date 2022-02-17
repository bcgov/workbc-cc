<?php

namespace Drupal\work_bc_quiz\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an autocomplete widget for the work_bc_quiz field type.
 *
 * @FieldWidget(
 *   id = "double_reference_autocomplete",
 *   label = @Translation("Double reference autocomplete"),
 *   description = @Translation("Autocomplete text fields for each reference."),
 *   field_types = {
 *     "work_bc_quiz"
 *   }
 * )
 */
class WorkBcQuizAutocompleteWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widget = parent::formElement($items, $delta, $element, $form, $form_state);

    // Get the reference target entity type from the storage settings.
    $ar_target_type = $this->getFieldSetting('ar_target_type');

    // Get the field settings.
    $settings = $this->fieldDefinition->getSettings();

    // Set the label on the primary reference field, if one is in settings.
    if (!empty($settings['pr_label'])) {
      $widget['target_id']['#title'] = $settings['pr_label'];
      $widget['target_id']['#title_display'] = 'before';
    }

    if (!empty($settings['pr_field_hide'])) {
      $widget['target_id']['#attributes']['class'][] = 'visually-hidden-node';
    }

    // Get the settings for the added reference field.
    $ar_bundles = $settings['added_reference']['ar_bundles'];
    $ar_label = !empty($settings['added_reference']['ar_label']) ? $settings['added_reference']['ar_label'] : '';
    $ar_weight = !empty($settings['added_reference']['ar_weight']) ? $settings['added_reference']['ar_weight'] : -50;
    $ar_required = !empty($settings['added_reference']['ar_required']) ? $settings['added_reference']['ar_required'] : FALSE;

    // Get the existing value, if any, for the added reference field.
    $default = isset($items[$delta]) ? $items[$delta]->ar_target_id : NULL;
    if (!empty($default)) {
      $default = \Drupal::entityTypeManager()->getStorage($ar_target_type)->load($default);
    }

    // Build the added reference form field.
    $widget['ar_target_id'] = [
      '#type' => 'entity_autocomplete',
      '#selection_handler' => 'default:' . $ar_target_type,
      '#default_value' => $default,
      '#target_type' => $ar_target_type,
      '#weight' => $widget['target_id']['#weight'] + $ar_weight,
      '#selection_settings' => [
        'target_bundles' => $ar_bundles,
      ],
      '#required' => $this->isDefaultValueWidget($form_state) ? FALSE : $ar_required,
    ];

    // Set the label on the added reference field, if one is in settings.
    if (!empty($ar_label)) {
      $widget['ar_target_id']['#title'] = $ar_label;
      $widget['ar_target_id']['#title_display'] = 'before';
    }

    return $widget;
  }

}
