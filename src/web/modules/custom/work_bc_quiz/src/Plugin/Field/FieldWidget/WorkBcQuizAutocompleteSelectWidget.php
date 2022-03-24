<?php

namespace Drupal\work_bc_quiz\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an autocomplete/select widget for the work_bc_quiz field type.
 *
 * The primary reference uses autocomplete while the added reference uses
 * a select field.
 *
 * @FieldWidget(
 *   id = "double_reference_autocomplete_select",
 *   label = @Translation("Double reference autocomplete/select"),
 *   description = @Translation("Autocomplete for primary and select for added reference."),
 *   field_types = {
 *     "work_bc_quiz"
 *   }
 * )
 */
class WorkBcQuizAutocompleteSelectWidget extends EntityReferenceAutocompleteWidget {

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
   
	if (!empty($settings['pr_field_hide'])) {
		$widget['target_id']['#attributes']['class'][] = 'visually-hidden-node';
	}

    // Get the settings for the added reference field.
    $ar_bundles = $settings['added_reference']['ar_bundles'];
    $ar_label = !empty($settings['added_reference']['ar_label']) ? $settings['added_reference']['ar_label'] : $widget['target_id']['#title'];
    $ar_weight = !empty($settings['added_reference']['ar_weight']) ? $settings['added_reference']['ar_weight'] : -50;
    $ar_required = !empty($settings['added_reference']['ar_required']) ? $settings['added_reference']['ar_required'] : FALSE;
	 
	 
	$widget['target_id']['#title'] = ($settings['pr_label']) ? $settings['pr_label'] :"";
		  //Remove Target Id Label 
	$widget['target_id']['#title_display'] = 'before';
	
    // Get the existing value, if any, for the added reference field.
    $default = isset($items[$delta]) ? $items[$delta]->ar_target_id : null;
    
	if (!empty($default)) {
		$default1 = \Drupal::entityTypeManager()->getStorage($ar_target_type)->load($default);
    } 
	/* print_r($default);
	die; */
	
    // Get the options.
    $options = $this->getTaxonomyOptions((array) $ar_bundles);

    // Build the added reference form field.
    $widget['ar_target_id'] = [
      '#type' => 'radios',
      '#default_value' => $default,
      '#options' => $options,
      '#weight' => $widget['target_id']['#weight'] + $ar_weight,
      '#required' => $this->isDefaultValueWidget($form_state) ? FALSE : $ar_required,
	  '#required_error' => t('Please answer all questions before proceeding.'),
    ];

    // Set the label on the added reference field, if one is in settings.
    if (!empty($ar_label)) {
      $widget['ar_target_id']['#prefix'] = "<span class='question-prefix'>$ar_weight </span>";
      $widget['ar_target_id']['#title'] = $ar_label;
      $widget['ar_target_id']['#title_display'] = 'before';
    }
 
    return $widget;
  }

  /**
   * Get an options list from taxonomy vocabularies.
   *
   * @param array $vocabularies
   *   The list of vocabularies to use.
   *
   * @return array
   *   An options list of terms.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getTaxonomyOptions(array $vocabularies) {
    // Start off with empty so the field can be left empty.
	$options = [ null => 'Please Select'];
	$options = [];

    /** @var \Drupal\taxonomy\TermStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

    foreach ($vocabularies as $vid => $vocab) {
      // Empty check because checkboxes leave the vid in there but set to 0.
      if (!empty($vocabularies[$vid])) {
        // Load all the terms for this vocabulary and format them into
        // an options list.
        $terms = $storage->loadTree($vid);
        foreach ($terms as $term) {
          $options[$term->tid] = FieldFilteredMarkup::create($term->name);
        }
      }
    }

    return $options;
  }

}
