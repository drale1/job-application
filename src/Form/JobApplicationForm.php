<?php declare(strict_types = 1);

  namespace Drupal\job_application\Form;

  use Drupal\Core\Form\FormBase;
  use Drupal\Core\Form\FormStateInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Drupal\job_application\Service\JobApplicationFormService;

  /**
   * Provides a Job application form.
   */
  final class JobApplicationForm extends FormBase {

    protected $jobApplicationFormService;

    public function __construct(JobApplicationFormService $jobApplicationFormService) {
      $this->jobApplicationFormService = $jobApplicationFormService;
    }

    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('job_application.job_application_form_service')
      );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string {
      return 'job_application_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array {
      // Form elements...
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
          'Backend' => $this->t('Backend'),
          'Frontend' => $this->t('Frontend'),
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
          'PHP' => 'PHP',
          'Java' => 'Java',
        ],
        '#states' => [
          'visible' => [
            ':input[name="type"]' => ['value' => 'Backend'],
          ],
        ],
      ];

      $form['technology_frontend'] = [
        '#type' => 'select',
        '#title' => $this->t('Technology Frontend'),
        '#options' => [
          'AngularJS' => 'AngularJS',
          'ReactJS' => 'ReactJS',
        ],
        '#states' => [
          'visible' => [
            ':input[name="type"]' => ['value' => 'Frontend'],
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

      //validate email
      $email = $form_state->getValue('email');

      if ($email == !\Drupal::service('email.validator')->isValid($email)) {
        $form_state->setErrorByName('email', t('This email is not correct', ['%mail' => $email]));
      }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void {
      $name = $form_state->getValue('name');
      $email = $form_state->getValue('email');
      $type = $form_state->getValue('type');
      $technology = $form_state->getValue('type') == 'Backend' ? $form_state->getValue('technology_backend') :
        $form_state->getValue('technology_frontend');
      $message = $form_state->getValue('message');

      // Save the application and get the timestamp
      $timestamp = $this->jobApplicationFormService->saveJobApplication($name, $email, $type, $technology, $message);

      // Send email notification
      $this->jobApplicationFormService->sendEmailNotification($name, $email, $type, $technology, $message, $timestamp);

      // Redirect to homepage
      $form_state->setRedirect('<front>');
    }
  }
