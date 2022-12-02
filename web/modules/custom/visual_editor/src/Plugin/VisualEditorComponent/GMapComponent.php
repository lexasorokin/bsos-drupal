<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\GMapComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * Provides a 'gmap' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "gmap",
 *   type = "widget",
 *   name = @Translation("Gmap"),
 *   description = @Translation("Add gmap to the content."),
 *   iconClass = "fa fa-map-marker",
 * )
 */
class GMapComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['zoom'] = [
      '#type' => 'select',
      '#title' => t('Zoom'),
      '#options' => range(0, 18),
      '#default_value' => isset($content['zoom']) ? $content['zoom'] : ''
    ];
    $form['content']['height'] = [
      '#type' => 'textfield',
      '#title' => t('Map height'),
      '#description' => t('Enter height e.g. 300px'),
      '#default_value' => isset($content['height']) ? $content['height'] : '300px',
    ];
    $form['content']['places'] = [
      '#type' => 'table',
      '#tree' => true,
      '#prefix' => '<div id="places-wrapper">',
      '#suffix' => '</div>',
      '#header' => [t('Item'), t('Weight'), t('Place content'), t('Actions')],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'place-weight',
        ],
      ],
    ];
    $places = $form_state->getValue(['content', 'places']);
    if (empty($places) && empty($content['places'])) {
      $places[] = [
        'weight' => 0,
        'place' => [],
      ];
    }
    elseif (empty($places) && !empty($content['places'])) {
      $places = $content['places'];
    }
    uasort($places, 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    foreach ($places as $delta => $place) {
      $place_content = $content['places'][$delta]['place'];
      // TableDrag: Weight column element.
      $form['content']['places'][$delta]['#attributes']['class'][] = 'draggable';
      $form['content']['places'][$delta]['#weight'] = isset($place['weight']) ? $place['weight'] : 0;
      $form['content']['places'][$delta]['item'] = ['#plain_text' => ''];
      $form['content']['places'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => 'place']),
        '#title_display' => 'invisible',
        '#attributes' => ['class' => ['place-weight']],
        '#default_value' => isset($place['weight']) ? $place['weight'] : 0
      ];
      $form['content']['places'][$delta]['place'] = [
        '#type' => 'details',
        '#tree' => true,
        '#attributes' => [
          'class' => ['gmap-place']
        ],
        '#collapsible' => true,
        '#title' => !empty($place_content['title']) ? $place_content['title'] : t('Place @count', ['@count' => ($delta + 1)])
      ];
      $form['content']['places'][$delta]['place']['marker'] = [
        '#type' => 'hidden',
        '#default_value' => isset($place_content['marker']) ? $place_content['marker'] : '',
        '#attributes' => [
          'id' => 'place-marker-' . $delta
        ],
      ];
      $form['content']['places'][$delta]['place']['text'] = [
        '#type' => 'text_format',
        '#title' => t('Place content'),
        '#format' => isset($place_content['text']['format']) ? $place_content['text']['format'] : 'basic_html',
        '#default_value' => isset($place_content['text']['value']) ? $place_content['text']['value'] : '',
        '#allowed_formats' => unserialize(ALLOWED_TEXT_FORMATS),
      ];
      $form['content']['places'][$delta]['place']['map_canvas'] = [
        '#type' => 'container',
        '#tree' => true,
        '#attributes' => [
          'id' => 'map-canvas-' . $delta,
          'class' => ['map-canvas'],
          'data-index' => $delta
        ]
      ];
      if (count($places) > 1) {
        $form['content']['places'][$delta]['delete'] = [
          '#type' => 'submit',
          '#title' => t('Remove'),
          '#name' => 'delete_' . $delta,
          '#value' => 'Remove',
          '#submit' => [get_class($this) . '::ajaxSubmit'],
          '#ajax' => [
            'callback' => get_class($this) . '::addMoreSet',
            'wrapper' => 'places-wrapper',
          ]
        ];
      }
      else {
        $form['content']['places'][$delta]['delete'] = [];
      }
    }
    $form['content']['add'] = [
      '#type' => 'submit',
      '#title' => t('Add place'),
      '#value' => t('Add more'),
      '#submit' => [get_class($this) . '::ajaxSubmit'],
      '#ajax' => [
        'callback' => get_class($this) . '::addMoreSet',
        'wrapper' => 'places-wrapper',
      ]
    ];
  }

  public static function addMoreSet(array &$form, FormStateInterface $form_state) {
    return $form['content']['places'];
  }

  /**
   * {@inheritdoc}
   */
  public static function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $places = $form_state->getValue('content');
    $parents = $form_state->getTriggeringElement();
    $parents = $parents['#parents'];
    if (isset($parents[1]) && $parents[1] == 'add') {
      $places['places'][] = [
        'weight' => 0,
        'place' => [],
      ];
    }
    if (isset($parents[3]) && $parents[3] == 'delete') {
      unset($places['places'][$parents[2]]);
    }
    $form_state->setValue('content', $places);
    $form_state->setRebuild(TRUE);
  }

  public function render($settings) {
    $build = parent::getBuild($settings);

    if (!empty($settings['content']['places'])) {
      $id = Html::getUniqueId('ve-gmap');
      $js_settings = Json::decode($settings['content']['places'][0]['place']['marker']);
      $build['#type'] = 'html_tag';
      $build['#tag'] = 'div';
      $build['#attributes']['class'][] = 'map-canvas';
      $build['#attributes']['id'] = $id;
      $style = '';
      if(!empty($build['#attributes']['style'])){
        $style = $build['#attributes']['style'];
      }
      $settings['content']['height'] = (!empty($settings['content']['height']))?$settings['content']['height']:'300px';
      $build['#attributes']['style'] = 'height:'.$settings['content']['height'].';'.$style;
      $build['#attached']['library'][] = 'visual_editor/visual_editor.front';
      $gmap_config = \Drupal::config('visual_editor.config')->get('gmap');
      $build['#attached']['drupalSettings']['ve_map']['apiKey'] = $gmap_config['key'];
      if(isset($gmap_config['icon'])){
        $build['#attached']['drupalSettings']['ve_map']['markerIcon'] = $gmap_config['icon'];
      }
      $build['#attached']['drupalSettings']['ve_map']['canvas'][$id]['zoom'] = $settings['content']['zoom'];
      $build['#attached']['drupalSettings']['ve_map']['canvas'][$id]['center'] = [
        'lat' => $js_settings['center']['lat'],
        'lng' => $js_settings['center']['lng']
      ];
      foreach ($settings['content']['places'] as $key => $marker) {
        $content = Json::decode($marker['place']['marker']);
        $marker_content = [
          '#type' => 'processed_text',
          '#text' => $marker['place']['text']['value'],
          '#format' => $marker['place']['text']['format']
        ];
        $marker = [
          'marker' => [
            'lat' => $content['center']['lat'],
            'lng' => $content['center']['lng'],
          ],
          'content' => \Drupal::service('renderer')->render($marker_content),
        ];
        $build['#attached']['drupalSettings']['ve_map']['canvas'][$id]['markers'][$key] = $marker;
      }
    }
    return $build;
  }

  public function getLibraries() {
    $libraries = parent::getLibraries();
    $libraries[] = 'visual_editor/visual_editor.gmap';
    return $libraries;
  }

}