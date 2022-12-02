<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\LinkComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'link' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "link",
 *   type = "widget",
 *   name = @Translation("Link"),
 *   description = @Translation("Add link to the content."),
 *   iconClass = "fa fa-link",
 * )
 */
class LinkComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['button_title'] = [
      '#title' => t('Link title'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => isset($content['button_title']) ? $content['button_title'] : '',
    ];
    $form['content']['button_subtitle'] = [
      '#title' => t('Link subtitle'),
      '#type' => 'textfield',
      '#default_value' => isset($content['button_subtitle']) ? $content['button_subtitle'] : '',
    ];
    $form['content']['button_url'] = [
      '#title' => t('URL'),
      '#type' => 'url',
      '#required' => true,
      '#default_value' => (isset($content['button_url'])) ? static::getUriAsDisplayableString($content['button_url']) : NULL,
      '#element_validate' => [[get_called_class(), 'validateUriElement']],
    ];
    $form['content']['button_icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#default_value' => isset($content['button_icon']) ? $content['button_icon'] : '',
      '#attributes' => [
        'class' => [
          'icp-auto'
        ],
        'placeholder' => t('Select icon from the list below')
      ],
    ];
    $form['content']['target'] = [
      '#title' => t('Target'),
      '#type' => 'select',
      '#empty_option' => t(' - select - '),
      '#options' => [
        '_blank' => '_blank',
        '_self' => '_self',
        '_parent' => '_parent',
        '_top' => '_top',
      ],
      '#default_value' => isset($content['target']) ? $content['target'] : '',
    ];
    $form['content']['is_modal'] = [
      '#type' => 'checkbox',
      '#title' => t('Open in modal'),
      '#default_value' => isset($content['is_modal']) ? $content['is_modal'] : 0,
    ];
    $form['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_link';
    $content = $settings['content'];
    $content['button_url'] = ($content['button_url'] == '<front>')?'internal:/':$content['button_url'];
    $content['button_url'] = Url::fromUri($content['button_url'])->toString();
    $build['#content'] = $content;
    $build['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
    $build['#attached']['library'][] = 'visual_editor/visual_editor.front';
    if(!empty($content['target'])){
      $build['#attributes']['target'] = $content['target'];
    }
    if(isset($settings['content']['is_modal']) && $content['is_modal']){
      $build['#attributes']['data-dialog-type'] = 'modal';
      $build['#attributes']['class'][] = 'use-ajax';
      $build['#attached']['library'][] = 'core/drupal.dialog.ajax';
    }
    return $build;
  }

  public function getLibraries() {
    $libraries = parent::getLibraries();
    $libraries[] = 'visual_editor/visual_editor.pickicon';
    return $libraries;
  }

}