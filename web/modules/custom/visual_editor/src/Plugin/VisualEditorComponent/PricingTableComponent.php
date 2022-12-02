<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\PricingTableComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'pricing_table' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "pricing_table",
 *   type = "widget",
 *   name = @Translation("Pricing table"),
 *   description = @Translation("Add pricing table to the content."),
 *   iconClass = "fa fa-money",
 * )
 */
class PricingTableComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['plan_title'] = [
      '#title' => t('Plan title'),
      '#type' => 'textfield',
      '#required' => true,
      '#prefix' => '<div class="row"><div class="col-lg-6">',
      '#default_value' => isset($content['plan_title']) ? $content['plan_title'] : '',
    ];
    $form['content']['plan_subtitle'] = [
      '#title' => t('Plan subtitle'),
      '#type' => 'textfield',
      '#default_value' => isset($content['plan_subtitle']) ? $content['plan_subtitle'] : '',
    ];
    $form['content']['currency'] = [
      '#title' => t('Currency'),
      '#type' => 'textfield',
      '#default_value' => isset($content['currency']) ? $content['currency'] : '',
    ];
    $form['content']['price'] = [
      '#title' => t('Price'),
      '#type' => 'textfield',
      '#default_value' => isset($content['price']) ? $content['price'] : '',
    ];
    $form['content']['cycle'] = [
      '#title' => t('Cycle'),
      '#type' => 'textfield',
      '#suffix' => '</div><div class="col-lg-6">',
      '#default_value' => isset($content['cycle']) ? $content['cycle'] : '',
    ];
    $form['content']['button_title'] = [
      '#title' => t('Button title'),
      '#type' => 'textfield',
      '#default_value' => isset($content['button_title']) ? $content['button_title'] : '',
    ];
    $form['content']['button_subtitle'] = [
      '#title' => t('Button subtitle'),
      '#type' => 'textfield',
      '#default_value' => isset($content['button_subtitle']) ? $content['button_subtitle'] : '',
    ];
    $form['content']['button_url'] = [
      '#title' => t('Button URL'),
      '#type' => 'url',
      '#default_value' => (isset($content['button_url'])) ? static::getUriAsDisplayableString($content['button_url']) : NULL,
      '#element_validate' => [[get_called_class(), 'validateUriElement']],
    ];
    $form['content']['button_class'] = [
      '#title' => t('Button classes'),
      '#type' => 'textfield',
      '#default_value' => isset($content['button_class']) ? $content['button_class'] : '',
    ];
    $form['content']['button_icon'] = [
      '#title' => t('Button icon'),
      '#type' => 'textfield',
      '#suffix' => '</div></div>',
      '#default_value' => isset($content['button_icon']) ? $content['button_icon'] : '',
      '#attributes' => [
        'class' => [
          'icp-auto'
        ],
        'placeholder' => t('Select icon from the list below')
      ],
    ];
    $form['content']['features'] = [
      '#type' => 'table',
      '#tree' => true,
      '#prefix' => '<div id="features-wrapper">',
      '#suffix' => '</div>',
      '#header' => [t('Item'), t('Weight'), t('Feature'), t('Availability'), t('Actions')],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'feature-weight',
        ],
      ],
    ];
    $features = $form_state->getValue(['content', 'features']);
    if (empty($features) && empty($content['features'])) {
      $features[] = [
        'weight' => 0,
        'feature' => [],
      ];
    }elseif (empty($features) && !empty($content['features'])) {
      $features = $content['features'];
    }
    uasort($features, 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    foreach ($features as $delta => $feature) {
      $feature_content = $content['features'][$delta];
      // TableDrag: Weight column element.
      $form['content']['features'][$delta]['#attributes']['class'][] = 'draggable';
      $form['content']['features'][$delta]['#weight'] = isset($feature['weight'])?$feature['weight']:0;
      $form['content']['features'][$delta]['item'] = ['#plain_text' => ''];
      $form['content']['features'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => 'feature']),
        '#title_display' => 'invisible',
        '#attributes' => ['class' => ['feature-weight']],
        '#default_value' => isset($feature['weight'])?$feature['weight']:0
      ];
      $form['content']['features'][$delta]['title'] = [
        '#type' => 'textfield',
        '#title' => t('Feature'),
        '#title_display' => 'invisible',
        '#default_value' => isset($feature_content['title'])?$feature_content['title']:''
      ];
      $form['content']['features'][$delta]['availability'] = [
        '#type' => 'textfield',
        '#size' => 10,
        '#title_display' => 'invisible',
        '#title' => t('Availability'),
        '#description' => t('Enter [y] for check, enter [n] for cross.'),
        '#default_value' => isset($feature_content['availability'])?$feature_content['availability']:'',
      ];
      if(count($features) > 1){
        $form['content']['features'][$delta]['delete'] = [
          '#type' => 'submit',
          '#title' => t('Remove'),
          '#name' => 'delete_' . $delta,
          '#value' => 'Remove',
          '#submit' => [get_class($this).'::ajaxSubmit'],
          '#ajax' => [
            'callback' => get_class($this).'::addMoreSet',
            'wrapper' => 'features-wrapper',
          ]
        ];
      }else{
        $form['content']['features'][$delta]['delete'] = [];
      }
    }
    $form['content']['add'] = [
      '#type' => 'submit',
      '#title' => t('Add feature'),
      '#value' => t('Add more'),
      '#submit' => [get_class($this).'::ajaxSubmit'],
      '#ajax' => [
        'callback' => get_class($this).'::addMoreSet',
        'wrapper' => 'features-wrapper',
      ]
    ];
  }
  
  public static function addMoreSet(array &$form, FormStateInterface $form_state) {
    return $form['content']['features'];
  }
  
  /**
   * {@inheritdoc}
   */
  public static function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $features = $form_state->getValue('content');
    $parents = $form_state->getTriggeringElement();
    $parents = $parents['#parents'];
    if (isset($parents[1]) && $parents[1] == 'add') {
      $features['features'][] = [
        'weight' => 0,
        'accordion' => [],
      ];
    }
    if (isset($parents[3]) && $parents[3] == 'delete') {
      unset($features['features'][$parents[2]]);
    }
    $form_state->setValue('content', $features);
    $form_state->setRebuild(TRUE);
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    $content = $settings['content'];
    if(!empty($content['button_url'])){
      $content['button_url'] = ($content['button_url'] == '<front>')?'internal:/':$content['button_url'];
      $content['button_url'] = Url::fromUri($content['button_url'])->toString();
    }else{
      unset($content['button_url']);
    }
    if(!empty($content['features'])){
      uasort($content['features'], 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    }
    $build['#theme'] = 've_pricing_table';
    $build['#content'] = $content;
    $build['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
    $build['#attached']['library'][] = 'visual_editor/visual_editor.front';
    return $build;
  }
}