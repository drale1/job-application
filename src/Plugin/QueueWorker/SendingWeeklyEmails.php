<?php declare(strict_types = 1);

namespace Drupal\job_application\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines 'job_application_sending_weekly_emails' queue worker.
 *
 * @QueueWorker(
 *   id = "job_application_sending_weekly_emails",
 *   title = @Translation("Sending weekly emails"),
 *   cron = {"time" = 60},
 * )
 */
final class SendingWeeklyEmails extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The mail email service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Constructs a new SendingWeeklyEmails instance.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The plugin.manager.mail service.
   */

  public function __construct(MailManagerInterface $mail_manager)
  {
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data): void
  {
    $email = $data->email;
    $subject = $data->subject;
    $body = $data->body;

    $module = 'job_application';
    $key = 'job_application_weekly_news_for_applicants';
    $to = $email;
    $params = [
      'subject' => $subject,
      'body' => $body,
    ];
    $langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();

    $this->mailManager->mail($module, $key, $to, $langcode, $params);
  }
}
