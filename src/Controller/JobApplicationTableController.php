<?php

  namespace Drupal\job_application\Controller;

  use Drupal\Core\Controller\ControllerBase;
  use Drupal\Core\Database\Connection;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Drupal\Core\Database\Query\PagerSelectExtender;
  use Drupal\Core\Database\Query\TableSortExtender;

  /**
   * Controller for the Job Applications table.
   */
  class JobApplicationTableController extends ControllerBase
  {

    protected $database;

    public function __construct(Connection $database)
    {
      $this->database = $database;
    }

    public static function create(ContainerInterface $container)
    {
      return new static(
        $container->get('database')
      );
    }

    /**
     * Display the Job Applications table with pagination and sorting.
     */
    public function content()
    {
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
      ];

      $query = $this->database->select('job_applications', 'ja')
        ->fields('ja')
        ->extend(TableSortExtender::class)
        ->orderByHeader($header)
        ->extend(PagerSelectExtender::class)
        ->limit(5);

      $result = $query->execute();

      $rows = [];
      foreach ($result as $row) {
        $rows[] = (array)$row;
      }

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
