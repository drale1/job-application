<?php declare(strict_types = 1);

  namespace Drupal\job_application\Controller;

  use Drupal\Core\Controller\ControllerBase;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Drupal\job_application\Service\JobApplicationEmailService;

  /**
   * Returns responses for Job application routes.
   */
  final class SendEmailsController extends ControllerBase {

    protected $jobApplicationEmailService;

    public function __construct(JobApplicationEmailService $jobApplicationEmailService) {
      $this->jobApplicationEmailService = $jobApplicationEmailService;
    }

    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('job_application.job_application_email_service')
      );
    }

    /**
     * Builds the response.
     */
    public function sendEmails() {
      // Get emails from the service.
      $results = $this->jobApplicationEmailService->getRecentJobApplications(7);

      // Use the service to send emails.
      $this->jobApplicationEmailService->sendEmailsToApplicants($results);
    }
  }
