<?php

  namespace Drupal\Tests\comment_on_top\ExistingSite;

  use weitzman\DrupalTestTraits\ExistingSiteBase;

  class JobApplicationTest extends ExistingSiteBase
  {

    protected function setUp(): void
    {
      parent::setUp();

      // Cause tests to fail if an error is sent to Drupal logs.
      $this->failOnLoggedErrors();
    }

    public function testJobApplication()
    {
      //Filling application form
      $this->drupalGet('/job-application/form');
      $page = $this->getCurrentPage();
      $page->fillField('name', 'Name Surname');
      $page->fillField('email', 'mail@email.com');
      $page->fillField('type', 'Backend');
      $page->fillField('technology_backend', 'PHP');
      $page->fillField('message', 'This is a test message');
      $submit_button = $page->findButton('Send');
      $submit_button->press();

      //Fetch db table
      $DbTable = \Drupal::database()->select('job_applications', 'ja');

      // Get max value of ID
      $maxJaid = $DbTable->fields('ja', ['jaid'])
        ->orderBy('jaid', 'DESC')
        ->range(0, 1)
        ->execute()
        ->fetchField();

      // Get all columns values
      $query = $DbTable->fields('ja')->condition('ja.jaid', $maxJaid, '=');

      $results = $query->execute()->fetchAll();
      $name = $results[0]->name;
      $mail = $results[0]->mail;
      $type = $results[0]->type;
      $technology = $results[0]->technology;
      $message = $results[0]->message;

      //Test every field against db
      $this->assertEquals('Name Surname', $name, 'Name from DB is not correct');
      $this->assertEquals('mail@email.com', $mail, 'Email from DB is not correct');
      $this->assertEquals('Backend', $type, 'Type from DB is not correct');
      $this->assertEquals('PHP', $technology, 'Technology from DB is not correct');
      $this->assertEquals('This is a test message', $message, 'Message from DB is not correct');

      $delete_query = \Drupal::database()->delete('job_applications');
      $delete_query->condition('jaid', $maxJaid, '=');
      $delete_query->execute();
    }
  }
