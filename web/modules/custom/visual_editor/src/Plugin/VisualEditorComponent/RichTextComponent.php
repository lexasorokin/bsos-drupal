<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\RichTextComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'rich_text' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "rich_text",
 *   type = "widget",
 *   name = @Translation("Rich text"),
 *   description = @Translation("Rich text."),
 *   iconClass = "fa fa-file",
 * )
 */
class RichTextComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['rich_text'] = [
      '#type' => 'text_format',
      '#required' => true,
      '#title' => t('Rich text'),
      '#default_value' => isset($content['rich_text']['value']) ? $content['rich_text']['value'] : '',
      '#format' => isset($content['rich_text']['format']) ? $content['rich_text']['format'] : 'basic_html',
      '#allowed_formats' => unserialize(ALLOWED_TEXT_FORMATS),
      '#prefix' => '<div id="rich-text">',
      '#suffix' => '</div>',
    ];
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
    $build = parent::getBuild($settings);
    $rich_text = $settings['content']['rich_text'];
    $build['#theme'] = 've_component';
    $build['#content']['content']['#type'] = 'processed_text';
    $build['#content']['content']['#format'] = $rich_text['format'];
    $build['#content']['content']['#text'] = $rich_text['value'];
    return $build;
  }

}