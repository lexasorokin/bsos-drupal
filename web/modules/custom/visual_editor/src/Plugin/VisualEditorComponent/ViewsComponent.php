<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ViewsComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\views\Entity\View;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'views' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "views",
 *   type = "widget",
 *   name = @Translation("Views"),
 *   description = @Translation("Render a view."),
 *   iconClass = "fa fa-square",
 * )
 */
class ViewsComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $views = View::loadMultiple();
    $select_views = array();
    foreach ($views as $view_id => $view) {
      foreach ($view->get('display') as $display_id => $display) {
        $select_views[$view->label()][$view_id . '|' . $display_id] = $view->label() . ' - ' . $display['display_title'];
      }
    }

    $content = $settings['content'];
    $form['content']['view_display'] = [
      '#type' => 'select',
      '#required' => true,
      '#title' => t('View with display'),
      '#options' => $select_views,
      '#default_value' => isset($content['view_display']) ? $content['view_display'] : '',
    ];
    $form['content']['arguments'] = [
      '#type' => 'textarea',
      '#title' => t('Views arguments'),
      '#description' => t('Enter each argument on new line in the same sequence as of views.'),
      '#default_value' => isset($content['arguments']) ? $content['arguments'] : '',
    ];
  }

  public function render($settings) {
    $view_args = explode('|', $settings['content']['view_display']);
    $filters = array_map('trim', explode("\n", $settings['content']['arguments']));
    $view_args = array_merge($view_args, $filters);
    $view_result = call_user_func_array('views_embed_view', $view_args);
    $view_result = $view_result?$view_result:'';
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_component';
    $build['#content'] = $view_result;
    return $build;
  }

}