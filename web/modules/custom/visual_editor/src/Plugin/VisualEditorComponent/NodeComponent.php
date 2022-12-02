<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\NodeComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'node' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "node",
 *   type = "widget",
 *   name = @Translation("Node"),
 *   description = @Translation("Add node to the content."),
 *   iconClass = "fa fa-square",
 * )
 */
class NodeComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $view_modes = \Drupal::entityManager()->getViewModes('node');
    $options = [];
    foreach ($view_modes as $key => $view_mode){
      $options[$key] = $view_mode['label'];
    }
    $entity = isset($content['node'])?\Drupal::entityTypeManager()
                      ->getStorage('node')
                      ->load($content['node']):'';
    $form['content']['node'] = [
      '#title' => t('Node'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#required' => true,
      '#default_value' => $entity,
    ];
    $form['content']['view_mode'] = [
      '#title' => t('View mode'),
      '#type' => 'select',
      '#options' => $options,
      '#empty_option' => t('- None -'),
      '#required' => true,
      '#default_value' => isset($content['view_mode']) ? $content['view_mode'] : '',
    ];
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    $entity = \Drupal::entityTypeManager()
                      ->getStorage('node')
                      ->load($settings['content']['node']);
    $view_builder = \Drupal::entityManager()->getViewBuilder('node');
    $output = $view_builder->view($entity, $settings['content']['view_mode']);
    $build['#theme'] = 've_component';
    $build['#content'] = $output;
    return $build;
  }
}