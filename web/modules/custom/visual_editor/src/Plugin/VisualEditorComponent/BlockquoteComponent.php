<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\BlockquoteComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides a 'blockquote' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "blockquote",
 *   type = "widget",
 *   name = @Translation("Blockquote"),
 *   description = @Translation("Place blockquote in content"),
 *   iconClass = "fa fa-quote-left",
 * )
 */
class BlockquoteComponent extends VisualEditorBase {
  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['text'] = [
      '#title' => t('Quote'),
      '#type' => 'text_format',
      '#required' => true,
      '#default_value' => isset($content['text']['value']) ? $content['text']['value'] : '',
      '#format' => isset($content['text']['format']) ? $content['text']['format'] : 'basic_html',
      '#allowed_formats' => unserialize(ALLOWED_TEXT_FORMATS),
    ];
    $form['content']['footer'] = [
      '#type' => 'textfield',
      '#title' => t('Footer'),
      '#default_value' => isset($content['footer']) ? $content['footer'] : ''
    ];
    $form['content']['inverse'] = [
      '#type' => 'checkbox',
      '#title' => t('Is inverse?'),
      '#default_value' => isset($content['inverse']) ? $content['inverse'] : 0
    ];
  }
  public function render($settings) {
    $build = parent::getBuild($settings);
    $rich_text = $settings['content']['text'];
    if($settings['content']['inverse']){
      $build['#attributes']['class'][] = 'blockquote-reverse';
    }
    $content['text']['#type'] = 'processed_text';
    $content['text']['#text'] = $rich_text['value'];
    $content['text']['#format'] = $rich_text['format'];
    $content['footer'] = [
      '#type' => 'html_tag', 
      '#tag' => 'footer',
      '#value' => $settings['content']['footer']
    ];
    $build['#type'] = 'html_tag';
    $build['#tag'] = 'blockquote';
    $build['#value'] = \Drupal::service('renderer')->render($content);
    return $build;
  }
}