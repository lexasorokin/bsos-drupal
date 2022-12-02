<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ViewsTabsComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\views\Entity\View;

/**
 * Provides a 'views_tabs' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "views_tabs",
 *   type = "widget",
 *   name = @Translation("Views tabs"),
 *   description = @Translation("Add tabs to the views content."),
 *   iconClass = "fa fa-folder",
 * )
 */
class ViewsTabsComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $views = View::loadMultiple();
    $select_views = array();
    foreach ($views as $view_id => $view) {
      foreach ($view->get('display') as $display_id => $display) {
        $select_views[$view->label()][$view_id . '|' . $display_id] = $view->label() . ' - ' . $display['display_title'];
      }
    }
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
        '#default_value' => isset($tab_content['title']) ? check_markup($tab_content['title']) : ''
      ];
      $form['content']['tabs'][$delta]['tab']['view_display'] = [
        '#type' => 'select',
        '#required' => true,
        '#title' => t('View with display'),
        '#options' => $select_views,
        '#default_value' => isset($tab_content['view_display']) ? $tab_content['view_display'] : '',
      ];
      $form['content']['tabs'][$delta]['tab']['arguments'] = [
        '#type' => 'textarea',
        '#title' => t('Views arguments'),
        '#description' => t('Enter each argument on new line in the same sequence as of views.'),
        '#default_value' => isset($tab_content['arguments']) ? $tab_content['arguments'] : '',
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
        $view_args = explode('|', $tab['tab']['view_display']);
        $filters = array_map('trim', explode("\n", $tab['tab']['arguments']));
        $view_args = array_merge($view_args, $filters);
        $view_result = call_user_func_array('views_embed_view', $view_args);
        $view_result = $view_result?$view_result:'';
        $build['#content'][$key]['id'] = Html::getUniqueId('ve-tabs');
        $build['#content'][$key]['title'] = $tab['tab']['title'];
        $build['#content'][$key]['content'] = $view_result;
      }
    }
    return $build;
  }

}