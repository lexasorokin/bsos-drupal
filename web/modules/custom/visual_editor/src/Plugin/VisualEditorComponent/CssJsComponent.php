<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\CssJsComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'cssjs' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "cssjs",
 *   type = "code",
 *   name = @Translation("CssJs"),
 *   description = @Translation("Manage css/js"),
 *   iconClass = "fa fa-code",
 * )
 */
class CssJsComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['css'] = [
      '#title' => t('Css'),
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['codemirror'],
        'data-mode' => 'css',
      ],
      '#default_value' => isset($content['css']) ? $content['css'] : '',
    ];
    $form['content']['js'] = [
      '#title' => t('Js'),
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['codemirror'],
        'data-mode' => 'javascript',
      ],
      '#default_value' => isset($content['js']) ? $content['js'] : '',
    ];
    $form['content']['uuid'] = [
      '#type' => 'hidden',
      '#default_value' => isset($content['uuid']) ? $content['uuid'] : \Drupal::service('uuid')->generate(),
    ];
    $form['#attached']['library'][] = 'visual_editor/visual_editor.codemirror';
  }

  public function submitForm(array &$form, FormStateInterface $form_state, array $settings) {
    $settings = parent::submitForm($form, $form_state, $settings);
    $content = $settings['content'];
    $config = \Drupal::service('config.factory')->getEditable('visual_editor.settings');
    $lib_uuid = $config->get('libraries');
    $css_dirname = 'public://visual-editor/css/';
    $js_dirname = 'public://visual-editor/js/';

    $css_path = $css_dirname . $content['uuid'] . '.css';
    $js_path = $js_dirname . $content['uuid'] . '.js';
    
    if (!empty($content['css'])) {
      file_prepare_directory($css_dirname, FILE_CREATE_DIRECTORY);
      file_unmanaged_save_data($content['css'], $css_path, FILE_EXISTS_REPLACE);
      $lib_uuid[$content['uuid']]['css'] = TRUE;
    }

    if (!empty($content['js'])) {
      file_prepare_directory($js_dirname, FILE_CREATE_DIRECTORY);
      file_unmanaged_save_data($content['js'], $js_path, FILE_EXISTS_REPLACE);
      $lib_uuid[$content['uuid']]['js'] = TRUE;
    }
    
    $config->set('libraries', $lib_uuid)->save();
    
    \Drupal\Core\Cache\Cache::invalidateTags(['library_info']);
    return $settings;
  }

  public function render($settings) {
    $build = [];
    $content = $settings['content'];
    $theme = \Drupal::service('theme.manager')->getActiveTheme();
    
    if(!empty($content['css']) || !empty($content['js'])){
      $config = \Drupal::service('config.factory')->getEditable('visual_editor.settings');
      $lib_uuid = $config->get('libraries');
      if(!isset($lib_uuid[$content['uuid']]['path'])){
        $lib_uuid[$content['uuid']]['path'] = \Drupal::service('path.current')->getPath();
        $config->set('libraries', $lib_uuid)->save();
      }
    }
    
    if (!empty($content['css'])) {
      $build['assets']['#markup'] = '';
      $build['#attached']['library'][] = $theme->getName() . '/' . $content['uuid'];
    }
    
    if (!empty($content['js'])) {
      $build['assets']['#markup'] = '';
      $build['#attached']['library'][] = $theme->getName() . '/' . $content['uuid'];
    }
    
    return $build;
  }

}