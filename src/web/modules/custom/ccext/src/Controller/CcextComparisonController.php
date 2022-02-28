<?php

namespace Drupal\ccext\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\entity_comparison\Entity\EntityComparison;
use Drupal\entity_comparison\Entity\EntityComparisonInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * The comparison page controller.
 */
class CcextComparisonController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The renderer service.
   *
   * @var Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Session service.
   *
   * @var Symfony\Component\HttpFoundation\Session\Session
   */
  protected $session;

  /**
   * Current user service.
   *
   * @var Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Entity field manager service.
   *
   * @var Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Entity type manager service.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Language manager service.
   *
   * @var Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Module handler service.
   *
   * @var Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Symfony\Component\HttpFoundation\Session\Session $session
   *   Session service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   Current user service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   Entity field manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   Language manager service.
   * @param Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler service.
   */
  public function __construct(RendererInterface $renderer, Session $session, AccountProxyInterface $current_user, EntityFieldManagerInterface $entity_field_manager, EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager, ModuleHandlerInterface $module_handler) {
    $this->renderer = $renderer;
    $this->session = $session;
    $this->currentUser = $current_user;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('session'),
      $container->get('current_user'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('module_handler')
    );
  }

  /**
   * Display the markup.
   *
   * @param int $entity_comparison_id
   *   Entity comparison ID.
   * @param int $entity_id
   *   Entity ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Page request object.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|Drupal\Core\Ajax\AjaxResponse
   *   Return action response.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Exception
   */
  public function action($entity_comparison_id, $entity_id, Request $request) {
    // Load entity comparission entity.
    $entity_comparison = EntityComparison::load($entity_comparison_id);

    // Process the current request.
    $message_list = $this->processRequest($entity_comparison, $entity_id);

    // Get destination.
    $destination = $request->query->get('destination');
    $destination = 'internal:' . ($destination ?: '/');
    // Get route from the uri.
    try {
      $redirect_url = Url::fromUri($destination);
      if (!$redirect_url->isRouted()) {
        throw new \UnexpectedValueException('External URLs do not have an internal route name.');
      }
    }
    // Catch errors to avoid white screen of death,
    // It's needed to pass unit tests.
    catch (\Exception $e) {
      $redirect_url = Url::fromUri('internal:/');
    }

    // Redirect back the user.
    if ($request->get(MainContentViewSubscriber::WRAPPER_FORMAT) == 'drupal_ajax') {
      // Create a new AJAX response.
      $response = new AjaxResponse();
      // Generate the link render array.
      $link = $entity_comparison->getLink($entity_id, TRUE);
      $link = $link->toRenderable();
      // Generate a CSS selector to use in a JQuery Replace command.
      $selector = '[data-entity-comparison=' . $entity_comparison->id() . '-' . $entity_id . ']';

      // Create a new JQuery Replace command to update the link display.
      $replace = new ReplaceCommand($selector, $this->renderer->renderPlain($link));
      $response->addCommand($replace);

      // Update compare table.
      if (strpos($destination, '/career-compare/')) {
        $compare_content = $this->compare($entity_comparison_id);
        $updateTable = new ReplaceCommand('#comparison-table', $this->renderer->renderPlain($compare_content));
        $response->addCommand($updateTable);
      }

      // Ajax messages.
      foreach ($message_list as $message) {
        $response->addCommand(new MessageCommand($message['message'], NULL, $message['options']));
      }

      // Replace block comparison link.
      $blocks = $this->entityTypeManager->getStorage('block')->loadMultiple();
      $blocks = array_filter($blocks, function ($block) {
        $block = $block->toArray();
        return isset($block['settings']['provider'], $block['settings']['link_text']) && $block['settings']['provider'] == 'entity_comparison';
      });
      foreach ($blocks as $block) {
        $plugin = $block->getPlugin();
        $build = $plugin->build();
        $class = $plugin->getLinkClass();

        $response->addCommand(new ReplaceCommand('a.' . $class, $this->renderer->renderPlain($build)));
      }

      return $response;
    }

    $messenger = \Drupal::messenger();
    foreach ($message_list as $message) {
      $type = isset($message['options']['type']) ? $message['options']['type'] : NULL;
      $messenger->addMessage($message['message'], $type);
    }

    return $this->redirect($redirect_url->getRouteName(), $redirect_url->getRouteParameters());
  }

  /**
   * Clear all entity_comparison value.
   */
  public function actionRemove() {
    // Get current user's id.
    $uid = $this->currentUser->id();
    $this->session->set('entity_comparison_' . $uid, NULL);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Clear all entity from comparison'),
    ];
  }

  /**
   * Process the request.
   *
   * @param \Drupal\entity_comparison\Entity\EntityComparisonInterface $entity_comparison
   *   Entity Comparission entity.
   * @param int $entity_id
   *   Entity ID.
   */
  protected function processRequest(EntityComparisonInterface $entity_comparison, $entity_id) {
    // Get current user's id.
    $uid = $this->currentUser->id();

    // Get entity type and bundle type.
    $entity_type = $entity_comparison->getTargetEntityType();
    $bundle_type = $entity_comparison->getTargetBundleType();

    // Get current entity comparison list.
    $entity_comparison_list = $this->session->get('entity_comparison_' . $uid);

    // Get entity.
    $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
    $message_list = [];
    if (empty($entity_comparison_list)) {
      $add = TRUE;
    }
    else {
      if (!empty($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()]) && in_array($entity_id, $entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()])) {
        $add = FALSE;
      }
      else {
        $add = TRUE;
      }
    }

    if ($add) {

      // Get the limit.
      $limit = $entity_comparison->getLimit();

      // If the increased number of the list is lower or equal than the limit
      // OR limit is 0 (no limit).
      if ((isset($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()])
          && (count($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()]) + 1) <= $limit)
        || ($limit == 0)
        || (!isset($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()]) && $limit >= 1)) {

        // Add to the list.
        $entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()][] = $entity_id;
        $message['message'] = $this->t('You have successfully added %entity_name to %entity_comparison list.', [
          '%entity_name' => $entity->label(),
          '%entity_comparison' => $entity_comparison->label(),
        ]);
        $message['options'] = ['type' => MessengerInterface::TYPE_STATUS];
        $message_list[] = $message;
      }
      else {
        $message['message'] = $this->t('You can only add @limit items to the %entity_comparison list.', [
          '@limit' => $limit,
          '%entity_comparison' => $entity_comparison->label(),
        ]);
        $message['options'] = ['type' => MessengerInterface::TYPE_ERROR];
        $message_list[] = $message;
      }

    }
    else {
      $key = array_search($entity_id, $entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()]);
      unset($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison->id()][$key]);
      $message['message'] = $this->t('You have successfully removed %entity_name from %entity_comparison.', [
        '%entity_name' => $entity->label(),
        '%entity_comparison' => $entity_comparison->label(),
      ]);
      $message['options'] = ['type' => MessengerInterface::TYPE_STATUS];
      $message_list[] = $message;
    }

    $this->session->set('entity_comparison_' . $uid, $entity_comparison_list);
    return $message_list;
  }

  /**
   * Compare page.
   *
   * @param int $_entity_comparison_id
   *   Entity comparission ID.
   *
   * @return array
   *   Array to render table for the current page.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function compare($_entity_comparison_id = NULL) {
    // Load the related entity comparison.
    $entity_comparison = $this->getEntityComparisonEntity($_entity_comparison_id);
    $entity_comparison_id = $entity_comparison->id();

    // Declare table header and rows.
    $header = [''];
    $rows = [];
    $raw = [];

    // Get current user's id.
    $uid = $this->currentUser->id();

    // Get entity type and bundle type.
    $entity_type = $entity_comparison->getTargetEntityType();
    $bundle_type = $entity_comparison->getTargetBundleType();

    // Get the related entity view display.
    $entity_view_display = EntityViewDisplay::load($entity_type . '.' . $bundle_type . '.' . $bundle_type . '_' . $entity_comparison_id);

    // Load field definitions.
    $field_definitions = $this->entityFieldManager
      ->getFieldDefinitions($entity_comparison->getTargetEntityType(), $entity_comparison->getTargetBundleType());

    // Get fields.
    $fields = $this->getTargetFields($field_definitions, $entity_view_display, $entity_comparison_id);

    // Get current entity comparison list.
    $entity_comparison_list = $this->session->get('entity_comparison_' . $uid);

    $entities = [];
    $comparison_fields = [];

    if (isset($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison_id])) {

      // Go through entities.
      foreach ($entity_comparison_list[$entity_type][$bundle_type][$entity_comparison_id] as $entity_id) {
        // Get entity.
        $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
        if ($entity) {
          if ($entity->hasTranslation($this->languageManager->getCurrentLanguage()->getId())) {
            $entity = $entity->getTranslation($this->languageManager->getCurrentLanguage()->getId());
          }

          // Get view builder.
          $view_builder = $this->entityTypeManager->getViewBuilder($entity_type);

          $entities[$entity_id] = $entity;
          $comparison_fields[$entity_id] = [];

          // Add entity's label to the header.
          if ($entity->hasLinkTemplate('canonical')) {
            $header[] = [
              'data' => $entity->toLink($entity->label())->toRenderable(),
            ];

          }
          $field_value = [];
          foreach ($fields as $field_name => $display_component) {
            if (isset($entity->{$field_name}) && $field = $entity->{$field_name}) {
              $field_value[$field_name] = $entity->get($field_name)->value;
              switch ($field_name) {
                case 'field_education_level':
                  $field_value[$field_name] = !$entity->get('field_education_level')->isEmpty() ? $entity->get('field_education_level')->referencedEntities()[0]->name->value : '';
                  break;

                case 'field_image':
                  $field_value[$field_name] = !$entity->field_image->isEmpty() ? $entity->field_image->entity->getFileUri() : '';
                  break;
                case 'field_workbc_link':
                  $field_value[$field_name] = !$entity->get('field_workbc_link')->isEmpty() ? $entity->get('field_workbc_link')->first()->uri : '';
                  break;
                case 'field_find_job':
                  $field_value[$field_name] = !$entity->get('field_find_job')->isEmpty() ? $entity->get('field_find_job')->first()->uri : '';
                  break;

                default:
                  $field_value[$field_name] = $entity->get($field_name)->value;
                  break;
              }
            }
          }
          $node_title = ['title' => $entity->label()];
          $raw[] = array_merge($node_title, $field_value);
        }
      }

      // If there are at least one entity in the list.
      if (count($comparison_fields)) {
        // Add the first row, where the user can remove the selected content.
        $row = [$this->t('Remove from the list')];
        foreach (Element::children($comparison_fields) as $key) {
          $row[] = $entity_comparison->getLink($key, TRUE)->toString();
        }
        $rows[] = $row;

        // Go through the selected fields.
        foreach ($fields as $field_name => $display_component) {

          // Set the field's label.
          if (is_a($field_definitions[$field_name], 'Drupal\field\Entity\FieldConfig')) {
            // Get label from FieldConfig object.
            $row = [$field_definitions[$field_name]->label()];
          }
          elseif (is_a($field_definitions[$field_name], 'Drupal\Core\Field\BaseFieldDefinition')) {
            // Get label from BaseFieldDefinition object.
            $row = [$field_definitions[$field_name]->getLabel()];
          }
          else {
            // Do not write inse the first column.
            $row = [''];
          }

          // Set the fields' values.
          foreach (Element::children($comparison_fields) as $key) {
            $row[] = $comparison_fields[$key][$field_name];
          }
          $rows[] = $row;
        }
      }
    }

    $context = [
      'entity_comparison' => $entity_comparison,
      'entities' => $entities,
      'comparison_fields' => $comparison_fields,
    ];
    $this->moduleHandler
      ->alter('entity_comparison_rows', $header, $rows, $context);
    return [
      '#type' => 'table',
      '#id' => $bundle_type . '-' . $entity_type . '-comparison-table',
      '#rows' => $raw,
      '#header' => $header,
      '#empty' => $this->t('No content available to compare.'),
      '#cache' => [
        'max-age' => 0,
      ],
      '#prefix' => '<div id="comparison-table">',
      '#suffix' => '</div>',
    ];
  }

  /**
   * Compare title.
   *
   * @param int $_entity_comparison_id
   *   Entity comparission ID.
   *
   * @return string
   *   The entity comparison entity title.
   */
  public function title($_entity_comparison_id = NULL) {
    // Load the related entity comparison.
    $entity_comparison = $this->getEntityComparisonEntity($_entity_comparison_id);

    return $entity_comparison->label();
  }

  /**
   * Get entity comparison entity.
   *
   * @param int $_entity_comparison_id
   *   Entity comparission ID.
   *
   * @return \Drupal\entity_comparison\Entity\EntityComparison
   *   The entity comparison entity.
   */
  protected function getEntityComparisonEntity($_entity_comparison_id = NULL) {
    // Get the entity comparison id from the current path.
    $current_path = \Drupal::service('path.current')->getPath();
    $current_path_array = explode('/', $current_path);
    $entity_comparison_id = $_entity_comparison_id ?? str_replace('-', '_', array_pop($current_path_array));

    // Load the related entity comparison.
    $entity_comparison = EntityComparison::load($entity_comparison_id);

    return $entity_comparison;
  }

  /**
   * Get target fields.
   *
   * @param array $field_definitions
   *   Array of field definitions.
   * @param \Drupal\Core\Entity\Entity\EntityViewDisplay $entity_view_display
   *   Entity view display.
   * @param int $entity_comparison_id
   *   Entity comparison ID.
   *
   * @return array
   *   Filterd fields.
   */
  protected function getTargetFields(array $field_definitions, EntityViewDisplay $entity_view_display, $entity_comparison_id) {
    $content_fields = $entity_view_display->get('content');

    $filtered_fields = [];

    foreach ($content_fields as $field_name => $field_settings) {

      if (isset($field_definitions[$field_name]) && isset($content_fields[$field_name]) && $field_definitions[$field_name]->isDisplayConfigurable('view')) {
        $filtered_fields[$field_name] = $content_fields[$field_name];
      }
    }

    // Sort the fields by weight.
    uasort($filtered_fields, 'Drupal\Component\Utility\SortArray::sortByWeightElement');

    return $filtered_fields;
  }

  /**
   * Create table header from given fields.
   */
  protected function createTableHeaderFromFields($fields) {
    $header = [];

    $header = $fields;

    return $header;
  }

}
