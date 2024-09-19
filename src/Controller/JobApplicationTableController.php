<?php

  namespace Drupal\job_application\Controller;

  use Drupal\Core\Controller\ControllerBase;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Drupal\job_application\Service\JobApplicationService;

  /**
   * Controller for the Job Applications table.
   */
  class JobApplicationTableController extends ControllerBase {

    protected $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService) {
      $this->jobApplicationService = $jobApplicationService;
    }

    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('job_application.job_application_service')
      );
    }

    //Display the Job Applications table with pagination and sorting.
    public function content() {
      $header = [
        'id' => [
          'data' => $this->t('Id'),
          'field' => 'jaid',
        ],
        'name' => [
          'data' => $this->t('Name'),
          'field' => 'name',
        ],
        'email' => [
          'data' => $this->t('Email'),
          'field' => 'mail',
        ],
        'type' => [
          'data' => $this->t('Type'),
          'field' => 'type',
        ],
        'technology' => [
          'data' => $this->t('Technology'),
          'field' => 'technology',
        ],
        'message' => $this->t('Message'),
        'created' => [
          'data' => $this->t('Sent'),
          'field' => 'created',
          'specifier' => 'long',
        ],
      ];

      // Use the service to get job applications data
      $rows = $this->jobApplicationService->getJobApplications($header, 5);

      $form['table'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

      $form['pager'] = [
        '#type' => 'pager',
      ];

      return $form;
    }
  }
