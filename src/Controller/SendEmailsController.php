<?php declare(strict_types = 1);

namespace Drupal\job_application\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Job application routes.
 */
final class SendEmailsController extends ControllerBase {

  protected $database;

  protected $mailManager;

  protected $configFactory;

  public function __construct(Connection $database, MailManagerInterface $mail_manager, ConfigFactoryInterface $config_factory) {
    $this->database = $database;
    $this->mailManager = $mail_manager;
    $this->configFactory = $config_factory;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('plugin.manager.mail'),
      $container->get('config.factory')
    );
  }

  /**
   * Builds the response.
   */
  public function sendEmails() {

    // Get emails from database, last 7 days
    $oneWeekAgo = strtotime('-7 days');
    $query = $this->database->select('job_applications', 'ja');
    $query->fields('ja', ['mail', 'created']);
    $query->condition('ja.created', $oneWeekAgo, '>=');
    $results = $query->execute()->fetchAll();

    // Get data from email config form
    $config = $this->configFactory->get('job_application.settings');
    $subject = $config->get('email_title');
    $body = $config->get('email_body');

    foreach ($results as $result) {
      $to = $result->mail;

      // Send emails.
      $this->mailManager->mail(
        'job_application',
        'job_application_weekly_news_for_applicants',
        $to,
        'en',
        [
          'subject' => $subject,
          'body' => $body,
        ],
        NULL,
        TRUE
      );
    }
  }
}
