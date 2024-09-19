<?php

  namespace Drupal\job_application\Service;

  use Drupal\Core\Database\Connection;
  use Drupal\Core\Datetime\DateFormatterInterface;

  class JobApplicationService {

    protected $database;
    protected $dateFormatter;

    public function __construct(Connection $database, DateFormatterInterface $date_formatter) {
      $this->database = $database;
      $this->dateFormatter = $date_formatter;
    }

    public function getJobApplications(array $header, $limit = 5) {
      $query = $this->database->select('job_applications', 'ja')
        ->fields('ja')
        ->extend('Drupal\Core\Database\Query\TableSortExtender')
        ->orderByHeader($header)
        ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
        ->limit($limit);

      $result = $query->execute();

      $rows = [];
      foreach ($result as $row) {
        $row = (array) $row;
        $row['created'] = $this->dateFormatter->format($row['created'], 'custom', 'Y-m-d H:i:s');
        $rows[] = $row;
      }

      return $rows;
    }
  }
