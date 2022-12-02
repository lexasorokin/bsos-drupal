<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\VimeoComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'vimeo' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "vimeo",
 *   type = "widget",
 *   name = @Translation("Vimeo Embed"),
 *   description = @Translation("Vimeo embeded video"),
 *   iconClass = "fa fa-vimeo-square",
 * )
 */
class VimeoComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['embed_url'] = [
      '#type' => 'url',
      '#required' => true,
      '#title' => t('Vimeo URL'),
      '#default_value' => isset($content['embed_url']) ? $content['embed_url'] : '',
    ];
    $options = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('aspect_ratio');
    $form['content']['aspect_ratio'] = [
      '#type' => 'select',
      '#required' => true,
      '#title' => t('Aspect ratio'),
      '#options' => $options,
      '#default_value' => isset($content['aspect_ratio']) ? $content['aspect_ratio'] : '',
    ];
  }

  public function render($settings) {
    preg_match(VIMEO_REGX, $settings['content']['embed_url'], $matches);
    $settings['content']['embed_url'] = '//player.vimeo.com/video/' . $matches[5];
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_embeded_video';
    $build['#content'] = $settings['content'];
    return $build;
  }

}