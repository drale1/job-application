<?php

  namespace Drupal\job_application\Service;

  use Drupal\Core\Database\Connection;
  use Drupal\Core\Mail\MailManagerInterface;
  use Drupal\Core\Datetime\DateFormatterInterface;
  use Drupal\Core\Config\ConfigFactoryInterface;
  use Drupal\Core\Messenger\MessengerInterface;
  use Drupal\Component\Datetime\Time;

  class JobApplicationFormService {

    protected $database;
    protected $mailManager;
    protected $dateFormatter;
    protected $configFactory;
    protected $messenger;
    protected $time;

    public function __construct(
      Connection $database,
      MailManagerInterface $mail_manager,
      DateFormatterInterface $date_formatter,
      ConfigFactoryInterface $config_factory,
      MessengerInterface $messenger,
      Time $time
    ) {
      $this->database = $database;
      $this->mailManager = $mail_manager;
      $this->dateFormatter = $date_formatter;
      $this->configFactory = $config_factory;
      $this->messenger = $messenger;
      $this->time = $time;
    }

    public function saveJobApplication($name, $email, $type, $technology, $message) {
      $timestamp = $this->time->getRequestTime();

      // Save data to the database
      $this->database->insert('job_applications')
        ->fields([
          'name' => $name,
          'mail' => $email,
          'type' => $type,
          'technology' => $technology,
          'message' => $message,
          'created' => $timestamp,
        ])
        ->execute();

      return $timestamp;
    }

    public function sendEmailNotification($name, $email, $type, $technology, $message, $timestamp) {
      // Get main site mail
      $site_mail = $this->configFactory->get('system.site')->get('mail');

      $formatted_timestamp = $this->dateFormatter->format($timestamp, 'custom', 'Y-m-d H:i:s');

      // Send email to main site
      $params = [
        'subject' => 'New Job Application',
        'body' => t('A new job application has been submitted.<br> Details: <br>Name: @name
<br>Email: @email <br>Type: @type <br>Technology: @technology <br>Message: @message <br>Sent: @created', [
          '@name' => $name,
          '@email' => $email,
          '@type' => $type,
          '@technology' => $technology,
          '@message' => $message,
          '@created' => $formatted_timestamp,
        ]),
      ];

      $this->mailManager->mail('job_application', 'job_application_submission', $site_mail, 'en', $params);

      // Add success message
      $this->messenger->addStatus(t('The job application has been sent.'));
    }
  }
