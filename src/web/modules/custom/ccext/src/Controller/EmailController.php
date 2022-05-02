<?php

namespace Drupal\ccext\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\StreamWrapper\PrivateStream;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The Submit Email controller.
 */
class EmailController extends ControllerBase implements ContainerInjectionInterface {

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
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Used for logging errors.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_factory, MessengerInterface $messenger) {
    $this->loggerFactory = $logger_factory;
    $this->messenger = $messenger;
  }

  /**
   * Creates a new TweetFetcher.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('messenger')
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
    $referer = $request->headers->get('referer');
    // Getting the base url.
    $base_url = Request::createFromGlobals()->getSchemeAndHttpHost();
    // Getting the alias or the relative path.
    $alias = substr($referer, strlen($base_url));
    // Getting the node.
    $route_params = Url::fromUri("internal:" . $alias)->getRouteParameters();
    $entity_type = key($route_params);
    $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($route_params[$entity_type]);
    $node_id = $node->id();
    // dd($node->type->entity->label());

    $pdf = new Pdf($referer);
    $file_save_path_stream_directory =  'public://quizpdf'; // temporary
    \Drupal::service('file_system')->prepareDirectory($file_save_path_stream_directory, FileSystemInterface::CREATE_DIRECTORY && FileSystemInterface::MODIFY_PERMISSIONS);
    $fileLocation = $file_save_path_stream_directory . '/' . str_replace(' ', '-', $node->type->entity->label()) . "-$node_id.pdf";
    $absolute_path = \Drupal::service('file_system')->realpath($fileLocation);
    $pdf->saveAs($absolute_path);
    $this->loggerFactory->get('email_controller')->error($pdf->getError());
    $email_to = 'gurjinder_12@hotmail.com';
    if (empty($email_to)) {
      return;
    }
    $lang = "en";
    $params['subject'] = t('Quiz result from WorkBC.ca');
    $params['body'] = t('<p>Your @quiz results have been shared with you.</p> <p> Looking to discover more? Finish our other career and personality quizzes to learn more about your preferences and discover careers that suit you.</p>', ['@quiz' => $node->type->entity->label()]);

    // -------------------- Attachment Logic -----------------------
    $attachment = [
      'filepath' => 'sites/default/files/quizpdf/' . str_replace(' ', '-', $node->type->entity->label()) . "-$node_id.pdf",
      'filename' => str_replace(' ', '-', $node->type->entity->label()) . "-$node_id.pdf",
      'filemime' => 'application/pdf'
    ];
    $params['attachments'][] = $attachment;

    // Send email.
    $result = \Drupal::service('plugin.manager.mail')->mail('ccext', 'email', $email_to, $lang, $params, NULL, TRUE);
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
