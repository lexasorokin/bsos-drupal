<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Form\ComponentUtilityForm.
 */

namespace Drupal\visual_editor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ComponentUtilityForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'visual_editor_component_utility_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#action'] = \Drupal\Core\Url::fromRoute('component.form', ['method' => 'nojs'])->toString();
    $form['visual_editor_settings'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'class' => [
          'visual-editor-settings'
        ]
      ]
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#attributes' => [
        'class' => [
          'use-ajax-submit'
        ],
        'style' => 'display:none'
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}