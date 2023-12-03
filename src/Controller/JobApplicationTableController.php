<?php

	namespace Drupal\job_application\Controller;

  use Drupal\Core\Controller\ControllerBase;
  use Drupal\Core\Database\Connection;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * Controller for the Job Applications table.
   */
  class JobApplicationTableController extends ControllerBase {

    protected $database;

    public function __construct(Connection $database) {
      $this->database = $database;
    }

    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('database')
      );
    }

    /**
     * Display the Job Applications table.
     */
    public function content() {
      $header = [
        'id' => $this->t('Id'),
        'name' => $this->t('Name'),
        'email' => $this->t('Email'),
        'type' => $this->t('Type'),
        'technology' => $this->t('Technology'),
        'message' => $this->t('Message'),
      ];

      $rows = $this->jobApplicationGetDataFromDatabase();

      $form['table'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

      return $form;
    }

    /**
     * Get data from the job_applications table.
     */
    protected function jobApplicationGetDataFromDatabase() {
      $query = $this->database->select('job_applications', 'ja')
        ->fields('ja')
        ->execute();

      $data = $query->fetchAllAssoc('jaid');

      $data = array_map(function ($row) {
        return (array) $row;
      }, $data);

      return $data;
    }
  }
