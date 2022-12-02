<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\BlockContentComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'block_content' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "block_content",
 *   type = "widget",
 *   name = @Translation("Block content"),
 *   description = @Translation("Add block content to the content."),
 *   iconClass = "fa fa-square",
 * )
 */
class BlockContentComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $view_modes = \Drupal::entityManager()->getViewModes('block_content');
    $options = [];
    foreach ($view_modes as $key => $view_mode) {
      $options[$key] = $view_mode['label'];
    }
    $entity = \Drupal::entityTypeManager()
        ->getStorage('block_content')
        ->load($content['block_content']);
    $form['content']['block_content'] = [
      '#title' => t('Block content'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'block_content',
      '#required' => true,
      '#default_value' => isset($content['block_content']) ? $entity : '',
    ];
    $form['content']['view_mode'] = [
      '#title' => t('View mode'),
      '#type' => 'select',
      '#options' => $options,
      '#required' => true,
      '#empty_option' => t('- None -'),
      '#default_value' => isset($content['view_mode']) ? $content['view_mode'] : '',
    ];
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    if (isset($settings['content']['block_content']) && isset($settings['content']['view_mode'])) {
      $entity = \Drupal::entityTypeManager()
          ->getStorage('block_content')
          ->load($settings['content']['block_content']);

      $view_builder = \Drupal::entityManager()->getViewBuilder('block_content');
      $output = $view_builder->view($entity, $settings['content']['view_mode']);
      $build['#theme'] = 've_component';
      $build['#content'] = $output;
    }
    return $build;
  }

}