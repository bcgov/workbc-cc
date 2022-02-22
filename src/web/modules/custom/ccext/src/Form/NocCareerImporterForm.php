<?php

namespace Drupal\ccext\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Archiver\Zip;
use Drupal\Core\Archiver\ArchiverException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides CSV importer form.
 */
class NocCareerImporterForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * ImporterForm class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file handler.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileSystemInterface $file_system) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'noc_career_importer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['importer']['entity_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select entity type'),
      '#options' => ['career_profile' => t('Career Profile'), 'noc' => t('NOC'),],
      '#empty_value' => '_none',
      '#required' => TRUE,
    ];

     //this textfield will only be shown when the option 'Other'
    //is selected from the radios above.
    $form['importer']['career_profile_opening_year'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening Start End year'),
      '#size' => '60',
      '#placeholder' => '(2022-2032)',
      '#attributes' => [
        'id' => 'opening-year',
      ],
    ];

    // $form['importer']['img_folder'] = [
    //   '#type' => 'managed_file',
    //   '#title' => $this->t('Upload Zip image folder(Only for noc)'),
    //   // '#autoupload' => TRUE,
    //   '#upload_validators' => ['file_validate_extensions' => ['zip']],
    //   '#upload_location' => 'public://importer/zip/',
    // ];
    $form['importer']['csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Select CSV file'),
      '#required' => TRUE,
      '#autoupload' => TRUE,
      '#upload_validators' => ['file_validate_extensions' => ['csv']],
      '#upload_location' => 'temporary://',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('entity_type') == 'noc') {
      $csv = current($form_state->getValue('csv'));
      $csvData = $this->getCsvById($csv, ',');

      foreach ($csvData as $key => $value) {
        if ($key != 0) {
          $title = trim(str_replace('#', '', $value[0]));
          $field_description = trim($value[1]);
          $field_image = trim($value[2]);
          $field_video_id = trim($value[3]) != 'NULL' ? trim($value[3]) : '';
          $query = \Drupal::entityQuery('node')
            ->condition('type', 'noc')
            ->condition('title', $title)->execute();
          $Noc_node = NULL;
          if (empty($query)) {
            $Noc_node = Node::create(['type' => 'noc']);
            $Noc_node->enforceIsNew();
            $Noc_node->set('title', $title);
          }
          else {
            $Noc_node = Node::load(reset($query));
          }
          $Noc_node->set('field_description', ['format' =>'basic_html', 'value' => $field_description]);
          $Noc_node->set('field_video_id', $field_video_id);
          // Make sure the directory exists and is writable.
          if (!empty($field_image)) {
            $imageDirectory = 'public://';
            $this->fileSystem->prepareDirectory($imageDirectory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
            $imagePath = $imageDirectory . basename($field_image);
            // Store images.
            $fileData = file_get_contents($field_image);
            $file = file_save_data($fileData, $imagePath, FileSystemInterface::EXISTS_REPLACE);
            if (!empty($file)) {
              $Noc_node->set('field_image', ['target_id' => $file->id()]);
            }
          }
          $Noc_node->save();
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCsvById(int $id, string $delimiter) {
    /* @var \Drupal\file\Entity\File $entity */
    $entity = $this->entityTypeManager->getStorage('file')->load($id);
    $return = [];

    if (($csv = fopen($entity->uri->getString(), 'r')) !== FALSE) {
      while (($row = fgetcsv($csv, 0, $delimiter)) !== FALSE) {
        $return[] = $row;
      }

      fclose($csv);
    }

    return $return;
  }
}
