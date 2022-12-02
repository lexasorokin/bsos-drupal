<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ImageCarousel.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\Plugin\VisualEditorComponent\ImageComponent;
use Drupal\Component\Utility\Html;
use Drupal\file\Entity\File;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'image_carousel' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "image_carousel",
 *   type = "widget",
 *   name = @Translation("Image carousel"),
 *   description = @Translation("Create bootstrap carousel for images"),
 *   iconClass = "fa fa-picture-o",
 * )
 */
class ImageCarousel extends ImageComponent {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    parent::buildForm($form, $form_state, $settings);
    $content = isset($settings['carousel'])?$settings['carousel']:[];
    $form['carousel'] = [
      '#type' => 'details',
      '#title' => t('Carousel options'),
      '#group' => 'settings',
      '#tree' => true
    ];
    $form['carousel']['indicators'] = [
      '#type' => 'checkbox',
      '#title' => t('Show indicators'),
      '#default_value' => empty($content['indicators'])?$content['indicators']:1,
    ];
    $form['carousel']['controls'] = [
      '#type' => 'checkbox',
      '#title' => t('Show controls'),
      '#default_value' => empty($content['controls'])?$content['controls']:1,
    ];
    $form['carousel']['interval'] = [
      '#type' => 'number',
      '#title' => t('Interval'),
      '#default_value' => empty($content['interval'])?$content['interval']:3000,
    ];
    return $form;
  }
  
  public function render($settings) {
    $content = $settings['content'];
    $files = !empty($content['media']['table']) ? array_keys($content['media']['table']) : [];
    $build = parent::getBuild($settings);
    $id = Html::getUniqueId('bootstrap-image-carousel');
    $build['#theme'] = 've_image_carousel';
    $build['#attributes']['class'][] = 'carousel';
    $build['#attributes']['class'][] = 'slide';
    $build['#attributes']['data-ride'][] = 'carousel';
    $build['#attributes']['id'] = (!empty($build['#attributes']['id']))?$build['#attributes']['id']:$id;
    
    if (!empty($files)) {
      foreach ($files as $key => $fid) {
        $file_data = $content['media']['table'][$fid];
        $file = File::load($fid);
        if(!$file){continue;}
        $path = $file->getFileUri();
        $image = [];
        if (!empty($content['image_format'])) {
          $image['#theme'] = 'image_style';
          $image['#style_name'] = $content['image_format'];
        }
        else {
          $image['#theme'] = 'image';
        }
        $alt = isset($file_data['alt'])?$file_data['alt']:'';
        $image['#uri'] = $path;
        $image['#attributes']['class'][] = 'img-responsive';
        $image['#attributes']['class'][] = 'center-block';
        $image['#attributes']['alt'] = $alt;
        $build['#content'][$key]['slide'] = $image;
      }
    }
    return $build;
  }
   public function submitForm(array &$form, FormStateInterface $form_state, array $settings) {
     $settings = parent::submitForm($form, $form_state, $settings);
     $settings['carousel'] = $form_state->getValue('carousel');
     return $settings;
   }
}