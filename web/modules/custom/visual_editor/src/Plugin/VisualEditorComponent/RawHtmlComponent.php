<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\RawHtmlComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'raw_html' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "raw_html",
 *   type = "widget",
 *   name = @Translation("Raw html"),
 *   description = @Translation("Raw html"),
 *   iconClass = "fa fa-html5",
 * )
 */
class RawHtmlComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['raw_html'] = [
      '#type' => 'textarea',
      '#required' => true,
      '#title' => t('Raw html'),
      '#default_value' => isset($content['raw_html']) ? $content['raw_html'] : '',
    ];
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    $html = $settings['content']['raw_html'];
    $build['#theme'] = 've_component';
    $build['#content']['content']['#markup'] = $html;
    return $build;
  }

}