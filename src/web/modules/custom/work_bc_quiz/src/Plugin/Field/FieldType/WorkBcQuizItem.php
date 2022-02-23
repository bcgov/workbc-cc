<?php

namespace Drupal\work_bc_quiz\Plugin\Field\FieldType;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\TypedData\EntityDataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataReferenceDefinition;
use Drupal\Core\TypedData\DataReferenceTargetDefinition;

/**
 * Defines a double reference field type.
 *
 * @FieldType(
 *   id = "work_bc_quiz",
 *   label = @Translation("Double reference"),
 *   description = @Translation("An entity field containing an two entity references."),
 *   category = @Translation("Reference"),
 *   default_widget = "double_reference_autocomplete_select",
 *   default_formatter = "double_reference_label",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class WorkBcQuizItem extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'ar_target_type' => \Drupal::moduleHandler()->moduleExists('node') ? 'node' : 'user',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    $settings = parent::defaultFieldSettings();

    $settings['pr_label'] = '';
    $settings['pr_field_hide'] = 1;
    $settings['added_reference']['ar_label'] = '';
    $settings['added_reference']['ar_bundles'] = [];
    $settings['added_reference']['ar_weight'] = 1;
    $settings['added_reference']['ar_required'] = FALSE;

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $settings = $field_definition->getSettings();

    $ar_target_type_info = \Drupal::service('entity_type.manager')->getDefinition($settings['ar_target_type']);

    $ar_target_id_data_type = 'string';
    if ($ar_target_type_info->entityClassImplements(FieldableEntityInterface::class)) {
      $id_definition = \Drupal::service('entity_field.manager')->getBaseFieldDefinitions($settings['ar_target_type'])[$ar_target_type_info->getKey('id')];
      if ($id_definition->getType() === 'integer') {
        $ar_target_id_data_type = 'integer';
      }
    }

    if ($ar_target_id_data_type === 'integer') {
      $ar_target_id_definition = DataReferenceTargetDefinition::create('integer')
        ->setLabel(new TranslatableMarkup('AR: @label ID', ['@label' => $ar_target_type_info->getLabel()]))
        ->setSetting('unsigned', TRUE);
    }
    else {
      $ar_target_id_definition = DataReferenceTargetDefinition::create('string')
        ->setLabel(new TranslatableMarkup('AR: @label ID', ['@label' => $ar_target_type_info->getLabel()]));
    }
    $ar_target_id_definition->setRequired(FALSE);
    $properties['ar_target_id'] = $ar_target_id_definition;

    $properties['ar_entity'] = DataReferenceDefinition::create('entity')
      ->setLabel($ar_target_type_info->getLabel())
      ->setDescription(new TranslatableMarkup('The added reference referenced entity'))
      ->setComputed(TRUE)
      ->setReadOnly(FALSE)
      ->setTargetDefinition(EntityDataDefinition::create($settings['ar_target_type']))
      ->addConstraint('EntityType', $settings['ar_target_type']);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $ar_target_type = $field_definition->getSetting('ar_target_type');
    $ar_target_type_info = \Drupal::service('entity_type.manager')->getDefinition($ar_target_type);
    $properties = static::propertyDefinitions($field_definition)['ar_target_id'];

    if ($ar_target_type_info->entityClassImplements(FieldableEntityInterface::class) && $properties->getDataType() === 'integer') {
      $columns = [
        'ar_target_id' => [
          'description' => 'AR: The ID of the target entity.',
          'type' => 'int',
          'unsigned' => TRUE,
        ],
      ];
    }
    else {
      $columns = [
        'ar_target_id' => [
          'description' => 'AR: The ID of the target entity.',
          'type' => 'varchar_ascii',
          'length' => $ar_target_type_info->getBundleOf() ? EntityTypeInterface::BUNDLE_MAX_LENGTH : 255,
        ],
      ];
    }

    $schema['columns']['ar_target_id'] = $columns['ar_target_id'];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = parent::storageSettingsForm($form, $form_state, $has_data);

    $element['ar_target_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Added reference: Type of item to reference'),
      '#options' => \Drupal::service('entity_type.repository')->getEntityTypeLabels(TRUE),
      '#default_value' => $this->getSetting('ar_target_type'),
      '#required' => TRUE,
      '#disabled' => $has_data,
      '#size' => 1,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);
    $settings = $this->getSettings();

    // Get the reference target entity type from the storage settings.
    $ar_target_type = $this->getSetting('ar_target_type');

    $form['pr_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Primary reference: Field label'),
      '#default_value' => $settings['pr_label'],
      '#description' => $this->t('Label for the primary reference field.'),
    ];
	
	$form['pr_field_hide'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide reference field'),
      '#default_value' => ($settings['pr_field_hide']) ? $settings['pr_field_hide'] : '',
      '#description' => $this->t('Check to hide the reference field.'),
    ];

    $form['added_reference'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Added reference settings'),
    ];

    $options = [];
    $bundle_options = \Drupal::service('entity_type.bundle.info')->getBundleInfo($ar_target_type);
    foreach ($bundle_options as $key => $option) {
      $options[$key] = $option['label'];
    }
    $form['added_reference']['ar_bundles'] = [
      '#type' => 'checkboxes',
      '#options' => $options,
      '#title' => $this->t('Added reference: Bundles'),
      '#default_value' => $settings['added_reference']['ar_bundles'],
      '#description' => $this->t('Bundles for the added reference field to reference.'),
      '#required' => TRUE,
    ];

    $form['added_reference']['ar_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Added reference: Label'),
      '#default_value' => $settings['added_reference']['ar_label'],
      '#description' => $this->t('Label for the added reference field.'),
    ];

    $form['added_reference']['ar_weight'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Added reference: Weight'),
      '#default_value' => $settings['added_reference']['ar_weight'],
      '#description' => $this->t('Set a negative number to have the added reference come first or positive to have it come second.'),
    ];

    $form['added_reference']['ar_required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Added reference: Required'),
      '#default_value' => $settings['added_reference']['ar_required'],
      '#description' => $this->t('Enable to make the added reference required.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    parent::setValue($values, FALSE);

    if (!isset($values) || is_array($values)) {
      // Support setting the field item with only one property, but make sure
      // values stay in sync if only property is passed.
      // NULL is a valid value, so we use array_key_exists().
      if (is_array($values) && array_key_exists('ar_target_id', $values) && !isset($values['ar_entity'])) {
        $this->onChange('ar_target_id', FALSE);
      }

      elseif (is_array($values) && !array_key_exists('ar_target_id', $values) && isset($values['ar_entity'])) {
        $this->onChange('ar_entity', FALSE);
      }

      elseif (is_array($values) && array_key_exists('ar_target_id', $values) && isset($values['ar_entity'])) {
        // If both properties are passed, verify the passed values match. The
        // only exception we allow is when we have an added reference: in this
        // case its actual id and ar_target_id will be different, due to the new
        // entity marker.
        $ar_entity_id = $this->get('ar_entity')->getTargetIdentifier();

        // If the entity has been saved and we're trying to set both the
        // ar_target_id and the entity values with a non-null target ID,
        // then the value for ar_target_id should match the ID of the
        // added reference entity value. The entity ID as returned by
        // $entity->id() might be a string, but the provided ar_target_id might
        // be an integer - therefore we have to do a non-strict comparison.
        if (!$this->ar_entity->isNew() && $values['ar_target_id'] !== NULL && ($ar_entity_id != $values['ar_target_id'])) {
          throw new \InvalidArgumentException('The added reference target id and entity passed to the entity reference item do not match.');
        }
      }

      // Notify the parent if necessary.
      if ($notify && $this->parent) {
        $this->parent->onChange($this->getName());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name, $notify = TRUE) {
    // Make sure that the target ID and the target property stay in sync.
    if ($property_name === 'ar_entity') {
      $property = $this->get($property_name);
      $target_id = $property->isTargetNew() ? NULL : $property->getTargetIdentifier();
      $this->writePropertyValue('ar_target_id', $target_id);
    }
    elseif ($property_name == 'ar_target_id') {
      $this->writePropertyValue('ar_entity', $this->{$property_name});
    }

    parent::onChange($property_name, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    parent::preSave();

    if (!$this->isEmpty()
      && $this->ar_target_id === NULL
      && $this->ar_entity instanceof EntityInterface
    ) {
      $this->ar_target_id = $this->ar_entity->id();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    // Don't let the parent function run as that will double up all the
    // entity references.
    return [];
  }

}
