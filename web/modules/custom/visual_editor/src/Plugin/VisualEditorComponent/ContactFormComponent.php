<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ContactFormComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'contact_form' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "contact_form",
 *   type = "widget",
 *   name = @Translation("Contact form"),
 *   description = @Translation("Add contact form to the content."),
 *   iconClass = "fa fa-envelope",
 * )
 */
class ContactFormComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $view_modes = \Drupal::entityManager()->getViewModes('contact_form');
    $options = [];
    foreach ($view_modes as $key => $view_mode){
      $options[$key] = $view_mode['label'];
    }
    $entity = isset($content['contact_form'])?\Drupal::entityTypeManager()
                      ->getStorage('contact_form')
                      ->load($content['contact_form']):'';
    $form['content']['contact_form'] = [
      '#title' => t('Contact form'),
      '#description' => t('Enter contact form name except personal.'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'contact_form',
      '#required' => true,
      '#default_value' => $entity,
    ];
  }

  public function render($settings) {
    if($settings['content']['contact_form'] == 'personal'){ return []; }
    $build = parent::getBuild($settings);
    $contact_form = \Drupal::entityTypeManager()
                      ->getStorage('contact_form')
                      ->load($settings['content']['contact_form']);
    
    $message = \Drupal::entityManager()
      ->getStorage('contact_message')
      ->create(array(
        'contact_form' => $settings['content']['contact_form'],
      ));
    
    $form = \Drupal::service('entity.form_builder')->getForm($message);
    $form['#title'] = $contact_form
      ->label();
    $form['#cache']['contexts'][] = 'user.permissions';
    $build['#theme'] = 've_component';
    $build['#content'] = $form;
    return $build;
  }
}