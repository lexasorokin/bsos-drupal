<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\TaxonomyComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'taxonomy' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "taxonomy",
 *   type = "widget",
 *   name = @Translation("Taxonomy term"),
 *   description = @Translation("Add taxonomy term to the content."),
 *   iconClass = "fa fa-tags",
 * )
 */
class TaxonomyComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $view_modes = \Drupal::entityManager()->getViewModes('taxonomy_term');
    $options = [];
    foreach ($view_modes as $key => $view_mode){
      $options[$key] = $view_mode['label'];
    }
    $entity = isset($content['taxonomy_term'])?\Drupal::entityTypeManager()
                      ->getStorage('taxonomy_term')
                      ->load($content['taxonomy_term']):'';
    $form['content']['taxonomy_term'] = [
      '#title' => t('Taxonomy term'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
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
                      ->getStorage('taxonomy_term')
                      ->load($settings['content']['taxonomy_term']);
    $view_builder = \Drupal::entityManager()->getViewBuilder('taxonomy_term');
    $output = $view_builder->view($entity, $settings['content']['view_mode']);
    $build['#theme'] = 've_component';
    $build['#content'] = $output;
    return $build;
  }
}