<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\WebformComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'webform' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "webform",
 *   type = "widget",
 *   name = @Translation("Webform"),
 *   description = @Translation("Embed webform."),
 *   iconClass = "fa fa-check-square-o",
 * )
 */
class WebformComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['webform'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#title' => t('Webform Id'),
      '#default_value' => isset($content['webform']) ? $content['webform'] : '',
    ];
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_component';
    if(!\Drupal::moduleHandler()->moduleExists('webform')){
      $build['#content']['webform']['#markup'] = t('Install and configure webform module.');
      return $build;
    }
    if(!empty($settings['content']['webform'])){
      $build['#content']['webform']['#type'] = 'webform';
      $build['#content']['webform']['#webform'] = $settings['content']['webform'];
    }else{
      $build['#content']['webform']['#markup'] = t('Enter webform id.');
    }
    return $build;
  }

}