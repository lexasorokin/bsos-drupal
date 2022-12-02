<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\IconContentComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'icon_content' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "icon_content",
 *   type = "widget",
 *   name = @Translation("Icon content"),
 *   description = @Translation("Add content with icon to the content."),
 *   iconClass = "fa fa-square",
 * )
 */
class IconContentComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['rich_text'] = [
      '#type' => 'text_format',
      '#required' => true,
      '#title' => t('Rich text'),
      '#default_value' => isset($content['rich_text']['value']) ? $content['rich_text']['value'] : '',
      '#format' => isset($content['rich_text']['format']) ? $content['rich_text']['format'] : 'basic_html',
      '#allowed_formats' => unserialize(ALLOWED_TEXT_FORMATS),
    ];
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
  
  public function submitForm(array &$form, FormStateInterface $form_state, array $settings) {
    $settings = parent::submitForm($form, $form_state, $settings);
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $text = $form_state->getValue('content');
    $uuids = _editor_parse_file_uuids($text['rich_text']['value']);
    _editor_record_file_usage($uuids, $user);
    return $settings;
  }

  public function render($settings) {
    $rich_text = $settings['content']['rich_text'];
    $build = parent::getBuild($settings);
    $build['#attributes']['class'][] = 've-icon-content';
    $build['#theme'] = 've_icon_content';
    $build['#content']['text']['#type'] = 'processed_text';
    $build['#content']['text']['#text'] = $rich_text['value'];
    $build['#content']['text']['#format'] = $rich_text['format'];
    $build['#content']['icon']['#type'] = 'html_tag';
    $build['#content']['icon']['#tag'] = 'i';
    $build['#content']['icon']['#attributes']['class'][] = $settings['content']['icon'];
    $build['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
    $build['#attached']['library'][] = 'visual_editor/visual_editor.front';
    return $build;
  }

  public function getLibraries() {
    $libraries = parent::getLibraries();
    $libraries[] = 'visual_editor/visual_editor.pickicon';
    return $libraries;
  }

}