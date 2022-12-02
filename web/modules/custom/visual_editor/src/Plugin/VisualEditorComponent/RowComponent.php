<?php
/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\RowComponent.
 */
namespace Drupal\visual_editor\Plugin\VisualEditorComponent;
use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'row' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "row",
 *   type = "row",
 *   name = @Translation("Row"),
 *   description = @Translation("A bootstrap row which accepts columns"),
 *   iconClass = "fa fa-bars",
 *   accepts = "column",
 * )
 */
class RowComponent extends VisualEditorBase {
  
  public function buildForm(array &$form, FormStateInterface $form_state, array $settings){

  }
  
  public function render($settings){
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_component';
    $build['#attributes']['class'][] = 'row';
    return $build;
  }
}