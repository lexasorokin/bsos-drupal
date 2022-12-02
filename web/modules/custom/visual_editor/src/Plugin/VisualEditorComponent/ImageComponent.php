<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ImageComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'image' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "image",
 *   type = "widget",
 *   name = @Translation("Image"),
 *   description = @Translation("Place image in content"),
 *   iconClass = "fa fa-picture-o",
 * )
 */
class ImageComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    parent::getLinkElement($form, $settings['link']);
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
    if(isset($content['fid']) || isset($content['media_upload']['image'][0])){
      $fid = !empty($content['fid'])?$content['fid']:$content['media_upload']['image'][0];
      $build = parent::getBuild($settings);
      $build[0] = $this->renderImage($fid, $content['image_format'], $build);
    }else{
      $files = !empty($content['media']['table']) ? array_keys($content['media']['table']) : [];
      if (!empty($files)) {
        foreach ($files as $key => $file) {
          $file_data = $content['media']['table'][$file];
          $alt = isset($file_data['alt'])?$file_data['alt']:'';
          $build[$key] = parent::getBuild($settings);
          $build[$key]['#attributes']['alt'] = $alt;
          $build[$key] = $this->renderImage($file, $content['image_format'], $build[$key]);
        }
      }
    }
    if($settings['link']['url']){
      foreach($build as $key => $img_build)
      $build[$key] = [
        '#type' => 'link',
        '#title' => $img_build,
        '#url' => Url::fromUri($settings['link']['url'], [
          'attributes' => [
            'target' => $settings['link']['target'],
            'id' => $settings['link']['id'],
            'class' => $settings['link']['class'],
          ],
        ]),
      ];
    }
    return $build;
  }

}