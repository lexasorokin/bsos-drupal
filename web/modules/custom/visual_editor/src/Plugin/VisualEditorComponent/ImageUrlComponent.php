<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ImageUrlComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\file\Entity\File;

/**
 * Provides a 'image' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "image_url",
 *   type = "widget",
 *   name = @Translation("Image Url"),
 *   description = @Translation("Place image url in content"),
 *   iconClass = "fa fa-picture-o",
 * )
 */
class ImageUrlComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $styles = \Drupal::entityTypeManager()->getStorage('image_style')->loadMultiple();
    $image_styles = [];
    foreach ($styles as $style) {
      $image_styles[$style->id()] = $style->getName();
    }
    $content = $settings['content'];
    $fids = $form_state->getValue([
      'content',
      'media',
      'fids',
      'entity_ids',
    ]);

    $files = [];
    $content['media']['table'] = empty($content['media']['table'])?[]:$content['media']['table'];
    if (!empty($fids)) {
      $files = \Drupal::service('plugin.manager.visual_editor')->getFids($fids);
    }
    else {
      $files = array_keys($content['media']['table']);
    }
    
    $form['content']['media'] = \Drupal::service('plugin.manager.visual_editor')->browserForm($files, $content['media']['table']);
    $form['content']['image_format'] = [
      '#type' => 'select',
      '#title' => t('Image format'),
      '#options' => $image_styles,
      '#empty_option' => t('- None -'),
      '#default_value' => isset($content['image_format']) ? $content['image_format'] : '',
    ];
  }

  public function render($settings) {
    $content = $settings['content'];
    $files = !empty($content['media']['table']) ? array_keys($content['media']['table']) : [];
    if (!empty($files)) {
      foreach ($files as $key => $fid) {
        $file = File::load($fid);
        if(!$file){continue;}
        $build[$key]['#markup'] = ImageStyle::load($content['image_format'])->buildUrl($file->getFileUri());
      }
    }
    return $build;
  }

}