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
      '#options' => ['career_profile' => t('Career Profile'), 'noc' => t('NOC Image'),],
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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('entity_type') == 'career_profile' && empty($form_state->getValue('career_profile_opening_year'))) {
      $form_state->setErrorByName('career_profile_opening_year', $this->t('Opening (from-to) year is empty. Please enter.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity_type = $form_state->getValue('entity_type');
    $csv = current($form_state->getValue('csv'));
    $csvData = $this->getCsvById($csv, ',');

    foreach ($csvData as $key => $value) {
      if ($key != 0) {
        $title = trim(str_replace('#', '', $value[0]));

        $query = $this->getNodeId($title);
        $Noc_node = NULL;
        if (empty($query)) {
          $Noc_node = Node::create(['type' => 'career_profile']);
          $Noc_node->enforceIsNew();
          $Noc_node->set('title', $title);
        }
        else {
          $Noc_node = Node::load(reset($query));
        }
        if ($entity_type == 'noc') {
          $field_image = trim($value[2]);
          $field_video_id = trim($value[3]) != 'NULL' ? trim($value[3]) : '';
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
        }
        if ($entity_type == 'career_profile') {
          $field_noc_name = trim($value[1]);
          $field_job_summary = trim($value[2]);
          $field_job_openings = trim($value[3]);
          $field_education_level = $this->getTermByName(trim($value[4]), 'education_level');
          $field_median_salary = trim($value[5]);
          $field_workbc_link = trim($value[6]);
          $field_find_job = trim($value[7]);
          $field_opening_from_to = $form_state->getValue('career_profile_opening_year');

          $Noc_node->set('field_job_summary', ['format' =>'basic_html', 'value' => $field_job_summary]);
          $Noc_node->set('field_noc_name', $field_noc_name);
          $Noc_node->set('field_job_openings', $field_job_openings);
          $Noc_node->set('field_education_level', $field_education_level);
          $Noc_node->set('field_median_salary', $field_median_salary);
          $Noc_node->set('field_opening_from_to', $field_opening_from_to);
          $Noc_node->set('field_workbc_link',  ["uri" => $field_workbc_link]);
          $Noc_node->set('field_find_job', ["uri" => $field_find_job]);
        }
        $Noc_node->save();
      }
    }
  }

  /**
   * Get Node Id.
   *
   * Node existing in data.
   */
  public function getNodeId($title = NULL) {

    if (empty($title) && $type) {
      return;
    }

    $entityStorage = $this->entityTypeManager->getStorage('node');
    $query = $entityStorage->getQuery();
    $query->condition('type', 'career_profile');
    $query->condition('title', $title);
    $result = $query->accessCheck(FALSE)->execute();
    return $result;
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

    /**
   * To find term by name and vid.
   *
   * @param string $name
   *   Term name.
   * @param string $vid
   *   Term vid.
   * @param string $pid
   *   Term parent id.
   *
   * @return int
   *   Term id or 0.
   */
  public function getTermByName($name = NULL, $vid = NULL, $pid = NULL) {
    $properties = [];
    if (empty($name) && empty($vid)) {
      return [];
    }
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    if (!empty($pid)) {
      $properties['parent'] = $pid;
    }

    // Load term.
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadByProperties($properties);
    $term = reset($terms);
    if (empty($term)) {
      // Set term data.
      $data = [
        'name' => $name,
        'vid' => $vid,
      ];

      // Create a term.
      $term = Term::create($data);
      if (!empty($pid)) {
        $term->set('parent', $pid);
      }

      $term->save();
      // $tid = $term->id();
    }

    return !empty($term) ? $term->id() : 0;
  }
}
