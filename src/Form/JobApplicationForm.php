<?php declare(strict_types = 1);

namespace Drupal\job_application\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Job application form.
 */
final class JobApplicationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'job_application_job_application';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => [
        '1' => $this->t('Backend'),
        '2' => $this->t('Frontend'),
      ],
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateTechnologyDropdown',
        'wrapper' => 'technology-wrapper',
      ],
    ];

    $form['technology_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'technology-wrapper'],
    ];

    $form['technology_backend'] = [
      '#type' => 'select',
      '#title' => $this->t('Technology Backend'),
      '#options' => [
        '1' => 'PHP',
        '2' => 'Java',
      ],
      '#states' => [
        'visible' => [
          ':input[name="type"]' => ['value' => '1'],
        ],
      ],
    ];

    $form['technology_frontend'] = [
      '#type' => 'select',
      '#title' => $this->t('Technology Frontend'),
      '#options' => [
        '1' => 'AngularJS',
        '2' => 'ReactJS',
      ],
      '#states' => [
        'visible' => [
          ':input[name="type"]' => ['value' => '2'],
        ],
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
    ];

    return $form;
  }

  /**
   * Implements callback for AJAX technology dropdown update.
   */
  public function updateTechnologyDropdown(array &$form, FormStateInterface $form_state) {
    return $form['technology_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
