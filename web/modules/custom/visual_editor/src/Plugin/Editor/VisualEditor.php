<?php

namespace Drupal\visual_editor\Plugin\Editor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\editor\Plugin\EditorBase;
use Drupal\visual_editor\VisualEditorConfig;

/**
 * Defines a CKEditor-based text editor for Drupal.
 *
 * @Editor(
 *   id = "visual_editor",
 *   label = @Translation("Visual editor"),
 *   supports_content_filtering = FALSE,
 *   supports_inline_editing = FALSE,
 *   supported_element_types = {
 *     "textarea"
 *   },
 *   is_xss_safe = TRUE
 * )
 */
class VisualEditor extends EditorBase {

  public function getJSSettings(Editor $editor) {
    $settings = $editor->getSettings();
    // Get all the visual editor components definitions.
    $veConfig = new VisualEditorConfig();
    $settings['buttons'] = $veConfig->getComponents();
    $settings['pluginSettings'] = $veConfig->getJSSettings();
    $settings['basePath'] = \Drupal\Core\Url::fromRoute('<front>')->toString();
    $settings['editorPath'] = drupal_get_path('module', 'visual_editor');
    $form = \Drupal::formBuilder()->getForm('Drupal\visual_editor\Form\ComponentUtilityForm');
    $settings['componentUtilityForm'] = \Drupal::service('renderer')->render($form);
    $settings['componentFormID'] = VE_COMPONENT_FORM_WRAP;
    $settings['componentUtilityFormWrap'] = VE_COMPONENT_UTILITY_FORM;
    $settings['ve_config']['gmap'] = \Drupal::config('visual_editor.config')->get('gmap');
    $settings['ve_config']['frontend'] = \Drupal::config('visual_editor.config')->get('frontend');
    return $settings;
  }

  public function getLibraries(Editor $editor) {
    $veConfig = new VisualEditorConfig();
    return $veConfig->getLibraries();
  }

  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();
    return $form;
  }

}