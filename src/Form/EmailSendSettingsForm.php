<?php declare(strict_types = 1);

namespace Drupal\job_application\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Job application settings for this site.
 */
final class EmailSendSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'job_application_email_send_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['job_application.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['intro'] = [
      '#markup' => '<p>' . t('Please enter the text that will be automatically sent weekly to all job applicants.') . '</p>',
    ];
    $form['email_title'] = [
      '#type' => 'textfield',
      '#title' => t('Email title'),
      '#description' => t('Please insert email title'),
      '#placeholder' => t('Email title'),
      '#required' => TRUE,
      '#default_value' => $this->config('job_application.settings')->get('email_title'),
    ];
    $form['email_body'] = [
      '#type' => 'textarea',
      '#title' => t('Email body'),
      '#description' => t('Please insert email body'),
      '#placeholder' => t('Email body'),
      '#required' => TRUE,
      '#default_value' => $this->config('job_application.settings')->get('email_body'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if (mb_strlen($form_state->getValue('email_title')) < 5) {
      $form_state->setErrorByName('email_title', $this->t('Email title should be at least 5 characters.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('job_application.settings')
      ->set('email_title', $form_state->getValue('email_title'))
      ->set('email_body', $form_state->getValue('email_body'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
