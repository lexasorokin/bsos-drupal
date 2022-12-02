<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\SkillbarComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'skillbar' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "skillbar",
 *   type = "widget",
 *   name = @Translation("Skillbar"),
 *   description = @Translation("Add skillbar to the content."),
 *   iconClass = "fa fa-tasks",
 * )
 */
class SkillbarComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['skillbars'] = [
      '#type' => 'table',
      '#tree' => true,
      '#prefix' => '<div id="skillbars-wrapper">',
      '#suffix' => '</div>',
      '#header' => [t('Item'), t('Weight'), t('Title'), t('Percent'), t('Color (HEX code)'), t('Actions')],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'skillbar-weight',
        ],
      ],
    ];
    $skillbars = $form_state->getValue(['content', 'skillbars']);
    if (empty($skillbars) && empty($content['skillbars'])) {
      $skillbars[] = [
        'weight' => 0,
        'skillbar' => [],
      ];
    }
    elseif (empty($skillbars) && !empty($content['skillbars'])) {
      $skillbars = $content['skillbars'];
    }
    uasort($skillbars, 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    foreach ($skillbars as $delta => $skillbar) {
      // TableDrag: Weight column element.
      $form['content']['skillbars'][$delta]['#attributes']['class'][] = 'draggable';
      $form['content']['skillbars'][$delta]['#weight'] = isset($skillbar['weight']) ? $skillbar['weight'] : 0;
      $form['content']['skillbars'][$delta]['item'] = ['#plain_text' => ''];
      $form['content']['skillbars'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => 'skillbar']),
        '#title_display' => 'invisible',
        '#attributes' => ['class' => ['skillbar-weight']],
        '#default_value' => isset($skillbar['weight']) ? $skillbar['weight'] : 0
      ];
      $form['content']['skillbars'][$delta]['title'] = [
        '#type' => 'textfield',
        '#title' => t('Skillbar title'),
        '#size' => 40,
        '#required' => true,
        '#default_value' => isset($skillbar['title']) ? $skillbar['title'] : ''
      ];
      $form['content']['skillbars'][$delta]['percent'] = [
        '#type' => 'select',
        '#title' => t('Skillbar percent'),
        '#required' => true,
        '#options' => range(0, 100),
        '#default_value' => isset($skillbar['percent']) ? $skillbar['percent'] : ''
      ];
      $form['content']['skillbars'][$delta]['color'] = [
        '#type' => 'textfield',
        '#title' => t('Skillbar color'),
        '#required' => true,
        '#default_value' => isset($skillbar['color']) ? $skillbar['color'] : '',
        '#size' => 8,
      ];
      if (count($skillbars) > 1) {
        $form['content']['skillbars'][$delta]['delete'] = [
          '#type' => 'submit',
          '#title' => t('Remove'),
          '#name' => 'delete_' . $delta,
          '#value' => 'Remove',
          '#submit' => [get_class($this) . '::ajaxSubmit'],
          '#ajax' => [
            'callback' => get_class($this) . '::addMoreSet',
            'wrapper' => 'skillbars-wrapper',
          ]
        ];
      }
      else {
        $form['content']['skillbars'][$delta]['delete'] = [];
      }
    }
    $form['content']['add'] = [
      '#type' => 'submit',
      '#title' => t('Add skillbar'),
      '#value' => t('Add more'),
      '#submit' => [get_class($this) . '::ajaxSubmit'],
      '#ajax' => [
        'callback' => get_class($this) . '::addMoreSet',
        'wrapper' => 'skillbars-wrapper',
      ]
    ];
  }

  public static function addMoreSet(array &$form, FormStateInterface $form_state) {
    return $form['content']['skillbars'];
  }

  /**
   * {@inheritdoc}
   */
  public static function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    $skillbars = $form_state->getValue('content');
    $parents = $form_state->getTriggeringElement();
    $parents = $parents['#parents'];
    if (isset($parents[1]) && $parents[1] == 'add') {
      $skillbars['skillbars'][] = [
        'weight' => 0,
        'skillbar' => [],
      ];
    }
    if (isset($parents[3]) && $parents[3] == 'delete') {
      unset($skillbars['skillbars'][$parents[2]]);
    }
    $form_state->setValue('content', $skillbars);
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function render($settings) {
    $build = parent::getBuild($settings);
    uasort($settings['content']['skillbars'], 'Drupal\Component\Utility\SortArray::sortByWeightElement');
    $build['#theme'] = 've_skillbars';
    $build['#content'] = $settings['content'];
    return $build;
  }

}