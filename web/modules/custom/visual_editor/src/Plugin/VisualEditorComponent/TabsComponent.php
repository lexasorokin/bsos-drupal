<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\TabsComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * Provides a 'tabs' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "tabs",
 *   type = "widget",
 *   name = @Translation("Tabs"),
 *   description = @Translation("Add tabs to the content."),
 *   iconClass = "fa fa-folder",
 * )
 */
class TabsComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['tabs'] = [
      '#type' => 'table',
      '#tree' => true,
      '#prefix' => '<div id="tabs-wrapper">',
      '#suffix' => '</div>',
      '#header' => [t('Item'), t('Weight'), t('Tab content'), t('Actions')],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'tab-weight',
        ],
      ],
    ];
    $tabs = $form_state->getValue(['content', 'tabs']);
    if (empty($tabs) && empty($content['tabs'])) {
      $tabs[] = [
        'weight' => 0,
        'tab' => [],
      ];
    }
    elseif (empty($tabs) && !empty($content['tabs'])) {
      $tabs = $content['tabs'];
    }
    uasort($tabs, 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    foreach ($tabs as $delta => $tab) {
      $tab_content = $content['tabs'][$delta]['tab'];
      // TableDrag: Weight column element.
      $form['content']['tabs'][$delta]['#attributes']['class'][] = 'draggable';
      $form['content']['tabs'][$delta]['#weight'] = isset($tab['weight']) ? $tab['weight'] : 0;
      $form['content']['tabs'][$delta]['item'] = ['#plain_text' => ''];
      $form['content']['tabs'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => 'tab']),
        '#title_display' => 'invisible',
        '#attributes' => ['class' => ['tab-weight']],
        '#default_value' => isset($tab['weight']) ? $tab['weight'] : 0
      ];
      $form['content']['tabs'][$delta]['tab'] = [
        '#type' => 'details',
        '#tree' => true,
        '#collapsible' => true,
        '#title' => !empty($tab_content['title']) ? $tab_content['title'] : t('Tab @count', ['@count' => ($delta + 1)])
      ];
      $form['content']['tabs'][$delta]['tab']['title'] = [
        '#type' => 'textfield',
        '#title' => t('Tab title'),
        '#required' => true,
        '#default_value' => isset($tab_content['title']) ? $tab_content['title'] : ''
      ];
      $form['content']['tabs'][$delta]['tab']['text'] = [
        '#type' => 'text_format',
        '#title' => t('Tab content'),
        '#required' => true,
        '#format' => isset($tab_content['text']['format']) ? $tab_content['text']['format'] : 'basic_html',
        '#allowed_formats' => unserialize(ALLOWED_TEXT_FORMATS),
        '#default_value' => isset($tab_content['text']['value']) ? $tab_content['text']['value'] : ''
      ];
      if (count($tabs) > 1) {
        $form['content']['tabs'][$delta]['delete'] = [
          '#type' => 'submit',
          '#title' => t('Remove'),
          '#name' => 'delete_' . $delta,
          '#value' => 'Remove',
          '#submit' => [get_class($this) . '::ajaxSubmit'],
          '#ajax' => [
            'callback' => get_class($this) . '::addMoreSet',
            'wrapper' => 'tabs-wrapper',
          ]
        ];
      }
      else {
        $form['content']['tabs'][$delta]['delete'] = [];
      }
    }
    $form['content']['add'] = [
      '#type' => 'submit',
      '#title' => t('Add tab'),
      '#value' => t('Add more'),
      '#submit' => [get_class($this) . '::ajaxSubmit'],
      '#ajax' => [
        'callback' => get_class($this) . '::addMoreSet',
        'wrapper' => 'tabs-wrapper',
      ]
    ];
  }

  public static function addMoreSet(array &$form, FormStateInterface $form_state) {
    return $form['content']['tabs'];
  }

  /**
   * {@inheritdoc}
   */
  public static function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $tabs = $form_state->getValue('content');
    $parents = $form_state->getTriggeringElement();
    $parents = $parents['#parents'];
    if (isset($parents[1]) && $parents[1] == 'add') {
      $tabs['tabs'][] = [
        'weight' => 0,
        'tab' => [],
      ];
    }
    if (isset($parents[3]) && $parents[3] == 'delete') {
      unset($tabs['tabs'][$parents[2]]);
    }
    $form_state->setValue('content', $tabs);
    $form_state->setRebuild(TRUE);
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    if (!empty($settings['content']['tabs'])) {
      uasort($settings['content']['tabs'], 'Drupal\Component\Utility\SortArray::sortByWeightElement');
      $build['#theme'] = 've_tabs';
      foreach ($settings['content']['tabs'] as $key => $tab) {
        $build['#content'][$key]['id'] = Html::getUniqueId('ve-tabs');
        $build['#content'][$key]['title'] = $tab['tab']['title'];
        $build['#content'][$key]['content']['#type'] = 'processed_text';
        $build['#content'][$key]['content']['#text'] = $tab['tab']['text']['value'];
        $build['#content'][$key]['content']['#format'] = $tab['tab']['text']['format'];
      }
    }
    return $build;
  }

}