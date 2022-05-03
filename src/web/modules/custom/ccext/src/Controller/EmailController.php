<?php

namespace Drupal\ccext\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The Submit Email controller.
 */
class EmailController extends ControllerBase implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The mail plugin manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The File System service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Used for logging errors.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail plugin manager service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The File system service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_factory,
                              MessengerInterface $messenger,
                              MailManagerInterface $mail_manager,
                              FileSystemInterface $file_system,
                              EntityTypeManagerInterface $entity_type_manager) {
    $this->loggerFactory = $logger_factory;
    $this->messenger = $messenger;
    $this->mailManager = $mail_manager;
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Creates a new TweetFetcher.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('messenger'),
      $container->get('plugin.manager.mail'),
      $container->get('file_system'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Get email and send.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Page request object.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|Drupal\Core\Ajax\AjaxResponse
   *   Return action response.
   */
  public function senEmail(Request $request) {
    $parameters = $request->query->all();
    $referer = $request->headers->get('referer');
    if (empty($parameters) && !isset($parameters['emailIdPop'])) {
      $this->messenger->addError($this->t("Request url don't not content required params"));
      $response = new RedirectResponse($referer);
      $response->send();
      return $response;
    }
    $email_to = $parameters['emailIdPop'];
    // Getting the base url.
    $base_url = Request::createFromGlobals()->getSchemeAndHttpHost();
    // Getting the alias or the relative path.
    $alias = substr($referer, strlen($base_url));
    // Getting the node.
    $route_params = Url::fromUri("internal:" . $alias)->getRouteParameters();
    $nodeId = !empty($route_params['node']) ? $route_params['node'] : $route_params['node_id'];
    $entity_type = key($route_params);
    $node = $this->entityTypeManager->getStorage('node')->load($nodeId);

    // Getting the base url.
    $base_url = Request::createFromGlobals()->getSchemeAndHttpHost();
    // Getting the alias or the relative path.
    $alias = substr($referer, strlen($base_url));
    // Getting the node.
    $route_params = Url::fromUri("internal:" . $alias)->getRouteParameters();
    $entity_type = key($route_params);
    $node = $this->entityTypeManager->getStorage('node')->load($route_params[$entity_type]);
    $node_id = $node->id();
    $pdf = new Pdf($referer);
    $file_save_path_stream_directory = 'public://quizpdf'; // temporary
    $this->fileSystem->prepareDirectory($file_save_path_stream_directory, FileSystemInterface::CREATE_DIRECTORY && FileSystemInterface::MODIFY_PERMISSIONS);
    $fileLocation = $file_save_path_stream_directory . '/' . str_replace(' ', '-', $node->type->entity->label()) . "-$node_id.pdf";
    $absolute_path = $this->fileSystem->realpath($fileLocation);
    $pdf->saveAs($absolute_path);
    $this->loggerFactory->get('email_controller')->error($pdf->getError());

    $lang = "en";
    $params['subject'] = $this->t('Quiz result from WorkBC.ca');
    $params['body'] = $this->t('<p>Your @quiz results have been shared with you.</p> <p> Looking to discover more? Finish our other career and personality quizzes to learn more about your preferences and discover careers that suit you.</p>', ['@quiz' => $node->type->entity->label()]);

    // -------------------- Attachment Logic -----------------------
    $attachment = [
      'filepath' => 'sites/default/files/quizpdf/' . str_replace(' ', '-', $node->type->entity->label()) . "-$node_id.pdf",
      'filename' => str_replace(' ', '-', $node->type->entity->label()) . "-$node_id.pdf",
      'filemime' => 'application/pdf',
    ];
    $params['attachments'][] = $attachment;

    // Send email.
    $result = $this->mailManager->mail('ccext', 'email', $email_to, $lang, $params, NULL, TRUE);
    if ($result['result']) {
      $this->messenger->addStatus($this->t('E-mail sent'));
    }
    else {
      $this->messenger->addStatus($this->t('Error sending e-mail'));
    }
    $response = new RedirectResponse($referer);
    $response->send();
    return $response;
  }

}
