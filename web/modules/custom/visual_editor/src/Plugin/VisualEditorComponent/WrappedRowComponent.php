<?php
/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\WrappedRowComponent.
 */
namespace Drupal\visual_editor\Plugin\VisualEditorComponent;
use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'row' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "wrapped_row",
 *   type = "row",
 *   name = @Translation("Full width section"),
 *   description = @Translation("A bootstrap row with wrappers which accepts columns"),
 *   iconClass = "fa fa-bars",
 *   accepts = "column",
 * )
 */
class WrappedRowComponent extends VisualEditorBase {
  public function buildForm(array &$form, FormStateInterface $form_state, array $settings){
    $content = $settings['content'];
    $form['content']['container_fluid'] = [
      '#type' => 'checkbox',
      '#title' => t('Is container fluid?'),
      '#default_value' => isset($content['container_fluid'])?$content['container_fluid']:'',
    ];
    $form['content']['row_class'] = [
      '#type' => 'textfield',
      '#title' => t('Row class'),
      '#default_value' => isset($content['row_class'])?$content['row_class']:'',
    ];
  }
  
  public function render($settings){
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_wrapped_row';
    return $build;
  }
}