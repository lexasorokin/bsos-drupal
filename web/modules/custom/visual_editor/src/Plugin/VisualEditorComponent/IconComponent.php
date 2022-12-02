<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\IconComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'icon' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "icon",
 *   type = "widget",
 *   name = @Translation("Icon"),
 *   description = @Translation("Add icon to the content."),
 *   iconClass = "fa fa-font-awesome",
 * )
 */
class IconComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => isset($content['icon']) ? $content['icon'] : '',
      '#attributes' => [
        'class' => [
          'icp-auto'
        ],
        'placeholder' => t('Select icon from the list below')
      ],
    ];
    $form['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    $build['#type'] = 'html_tag';
    $build['#tag'] = 'i';
    $build['#attributes']['class'][] = $settings['content']['icon'];
    $build['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
    return $build;
  }

  public function getLibraries() {
    $libraries = parent::getLibraries();
    $libraries[] = 'visual_editor/visual_editor.pickicon';
    return $libraries;
  }

}