<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\AccordionsComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * Provides a 'accordions' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "accordions",
 *   type = "widget",
 *   name = @Translation("Accordions"),
 *   description = @Translation("Add accordions to the content."),
 *   iconClass = "fa fa-square",
 * )
 */
class AccordionsComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['accordions'] = [
      '#type' => 'table',
      '#tree' => true,
      '#prefix' => '<div id="accordions-wrapper">',
      '#suffix' => '</div>',
      '#header' => [t('Item'), t('Weight'), t('Accordion content'), t('Actions')],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'accordion-weight',
        ],
      ],
    ];
    $accordions = $form_state->getValue(['content', 'accordions']);
    if (empty($accordions) && empty($content['accordions'])) {
      $accordions[] = [
        'weight' => 0,
        'accordion' => [],
      ];
    }
    elseif (empty($accordions) && !empty($content['accordions'])) {
      $accordions = $content['accordions'];
    }
    uasort($accordions, 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    foreach ($accordions as $delta => $accordion) {
      $accordion_content = $content['accordions'][$delta]['accordion'];
      // TableDrag: Weight column element.
      $form['content']['accordions'][$delta]['#attributes']['class'][] = 'draggable';
      $form['content']['accordions'][$delta]['#weight'] = isset($accordion['weight']) ? $accordion['weight'] : 0;
      $form['content']['accordions'][$delta]['item'] = ['#plain_text' => ''];
      $form['content']['accordions'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => 'accordion']),
        '#title_display' => 'invisible',
        '#attributes' => ['class' => ['accordion-weight']],
        '#default_value' => isset($accordion['weight']) ? $accordion['weight'] : 0
      ];
      $form['content']['accordions'][$delta]['accordion'] = [
        '#type' => 'details',
        '#tree' => true,
        '#collapsible' => true,
        '#title' => !empty($accordion_content['title']) ? $accordion_content['title'] : t('Accordion @count', ['@count' => ($delta + 1)])
      ];
      $form['content']['accordions'][$delta]['accordion']['title'] = [
        '#type' => 'textfield',
        '#title' => t('Accordion title'),
        '#required' => true,
        '#default_value' => isset($accordion_content['title']) ? $accordion_content['title'] : ''
      ];
      $form['content']['accordions'][$delta]['accordion']['text'] = [
        '#type' => 'text_format',
        '#title' => t('Accordion content'),
        '#required' => true,
        '#format' => isset($accordion_content['text']['format']) ? $accordion_content['text']['format'] : 'basic_html',
        '#default_value' => isset($accordion_content['text']['value']) ? $accordion_content['text']['value'] : '',
        '#allowed_formats' => unserialize(ALLOWED_TEXT_FORMATS),
      ];
      $form['content']['accordions'][$delta]['accordion']['is_collapsed'] = [
        '#type' => 'checkbox',
        '#title' => t('Is collapsed?'),
        '#default_value' => isset($accordion_content['is_collapsed']) ? $accordion_content['is_collapsed'] : false,
      ];
      if (count($accordions) > 1) {
        $form['content']['accordions'][$delta]['delete'] = [
          '#type' => 'submit',
          '#title' => t('Remove'),
          '#name' => 'delete_' . $delta,
          '#value' => 'Remove',
          '#submit' => [get_class($this) . '::ajaxSubmit'],
          '#ajax' => [
            'callback' => get_class($this) . '::addMoreSet',
            'wrapper' => 'accordions-wrapper',
          ]
        ];
      }
      else {
        $form['content']['accordions'][$delta]['delete'] = [];
      }
    }
    $form['content']['add'] = [
      '#type' => 'submit',
      '#title' => t('Add accordion'),
      '#value' => t('Add more'),
      '#submit' => [get_class($this) . '::ajaxSubmit'],
      '#ajax' => [
        'callback' => get_class($this) . '::addMoreSet',
        'wrapper' => 'accordions-wrapper',
      ]
    ];
  }

  public static function addMoreSet(array &$form, FormStateInterface $form_state) {
    return $form['content']['accordions'];
  }

  /**
   * {@inheritdoc}
   */
  public static function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $accordions = $form_state->getValue('content');
    $parents = $form_state->getTriggeringElement();
    $parents = $parents['#parents'];
    if (isset($parents[1]) && $parents[1] == 'add') {
      $accordions['accordions'][] = [
        'weight' => 0,
        'accordion' => [],
      ];
    }
    if (isset($parents[3]) && $parents[3] == 'delete') {
      unset($accordions['accordions'][$parents[2]]);
    }
    $form_state->setValue('content', $accordions);
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function render($settings) {
    $build = parent::getBuild($settings);
    if(!empty($settings['content']['accordions'])){
      uasort($settings['content']['accordions'], 'Drupal\Component\Utility\SortArray::sortByWeightElement');
      $build['#theme'] = 've_accordions';
      $build['#content']['accordions_id'] = Html::getUniqueId('ve-accordions');
      foreach ($settings['content']['accordions'] as $key => $accordion) {
        $build['#content']['accordion'][$key]['id'] = Html::getUniqueId('ve-accordion');
        $build['#content']['accordion'][$key]['title'] = $accordion['accordion']['title'];
        $build['#content']['accordion'][$key]['is_collapsed'] = $accordion['accordion']['is_collapsed'];
        $build['#content']['accordion'][$key]['content']['#type'] = 'processed_text';
        $build['#content']['accordion'][$key]['content']['#text'] = $accordion['accordion']['text']['value'];
        $build['#content']['accordion'][$key]['content']['#format'] = $accordion['accordion']['text']['format'];
      }
    }
    return $build;
  }

}