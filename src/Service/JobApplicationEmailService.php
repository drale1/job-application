<?php

  namespace Drupal\job_application\Service;

  use Drupal\Core\Database\Connection;
  use Drupal\Core\Mail\MailManagerInterface;
  use Drupal\Core\Config\ConfigFactoryInterface;

  class JobApplicationEmailService {

    protected $database;
    protected $mailManager;
    protected $configFactory;

    public function __construct(Connection $database, MailManagerInterface $mail_manager, ConfigFactoryInterface $config_factory) {
      $this->database = $database;
      $this->mailManager = $mail_manager;
      $this->configFactory = $config_factory;
    }

    /**
     * Retrieves emails from job applications created in the last 7 days.
     */
    public function getRecentJobApplications($days = 7) {
      $timeAgo = strtotime("-$days days");

      $query = $this->database->select('job_applications', 'ja')
        ->fields('ja', ['mail', 'created'])
        ->condition('ja.created', $timeAgo, '>=');

      return $query->execute()->fetchAll();
    }

    /**
     * Sends emails to applicants.
     */
    public function sendEmailsToApplicants($results) {
      $config = $this->configFactory->get('job_application.settings');
      $subject = $config->get('email_title');
      $body = $config->get('email_body');

      foreach ($results as $result) {
        $to = $result->mail;

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
