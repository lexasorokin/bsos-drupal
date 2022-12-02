<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\HeadingComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'heading' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "heading",
 *   type = "widget",
 *   name = @Translation("Heading"),
 *   description = @Translation("Heading of a section."),
 *   iconClass = "fa fa-header",
 * )
 */
class HeadingComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    parent::getLinkElement($form, $settings['link']);
    $content = $settings['content'];
    $form['content']['heading'] = [
      '#type' => 'textarea',
      '#required' => true,
      '#title' => t('Heading'),
      '#default_value' => isset($content['heading']) ? $content['heading'] : '',
    ];
    $form['content']['heading_type'] = [
      '#type' => 'select',
      '#required' => true,
      '#title' => t('Heading type'),
      '#options' => [
        'h1' => 'Heading 1',
        'h2' => 'Heading 2',
        'h3' => 'Heading 3',
        'h4' => 'Heading 4',
        'h5' => 'Heading 5',
        'h6' => 'Heading 6',
      ],
      '#default_value' => isset($content['heading_type']) ? $content['heading_type'] : '',
    ];
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    if(!empty($settings['content']['heading'])){
      $heading = $settings['content']['heading'];
      if($settings['link']['url']){
        $heading = [
          '#type' => 'link',
          '#title' => $settings['content']['heading'],
          '#url' => Url::fromUri($settings['link']['url'], [
            'attributes' => [
              'target' => $settings['link']['target'],
              'id' => $settings['link']['id'],
              'class' => $settings['link']['class'],
            ],
          ]),
        ];
        $heading = \Drupal::service('renderer')->render($heading);
      }
      $build['#type'] = 'html_tag';
      $build['#tag'] = $settings['content']['heading_type'];
      $build['#value'] = $heading;
    }
    return $build;
  }

}