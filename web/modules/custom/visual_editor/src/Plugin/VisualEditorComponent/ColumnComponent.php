<?php
/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ColumnComponent.
 */
namespace Drupal\visual_editor\Plugin\VisualEditorComponent;
use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'column' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "column",
 *   type = "column",
 *   name = @Translation("Column"),
 *   description = @Translation("A bootstrap column which accepts widgets."),
 *   iconClass = "fa fa-th",
 *   accepts = "widget",
 * )
 */
class ColumnComponent extends VisualEditorBase {
  public function getButtons() {
    $buttons = parent::getButtons();
    $cols = [];
    foreach(['12', '6_6','8_4', '4_8', '9_3', '3_9', '7_5', '5_7', '10_2', '2_10', '4_4_4', '6_3_3', '3_6_3', '3_3_6', '3_3_3_3'] as $i => $id){
      $colspans = explode('_', $id);
      $cols[$i] = $buttons;
      $cols[$i]['name'] .= ' '.str_replace('_','-',$id);
      $cols[$i]['grid'] = $id;
    }
    return $cols;
  }
  
  public function buildForm(array &$form, FormStateInterface $form_state, array $settings){
    $content = $settings['content'];
    $col_size = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('col_size');
    $col_offset_size = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('col_offset_size');
    $devices = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('device');
    // Column size element
    $form['content']['device'] = [
      '#type' => 'table',
      '#title' => 'Column size',
      '#header' => $devices,
    ];
    foreach($devices as $key => $title){
      $form['content']['device'][0][$key] = [
        '#type' => 'select',
        '#title' => t('Size'),
        '#options' => $col_size,
        '#default_value' => isset($content['device'][0][$key])?$content['device'][0][$key]:'12',
      ];
      if($key == 'xl'){
        $form['content']['device'][0][$key]['#default_value'] = $form['content']['device'][0][lg]['#default_value'];
      }
    }
    foreach($devices as $key => $title){
      $form['content']['device'][1][$key] = [
        '#type' => 'select',
        '#title' => t('Offset'),
        '#options' => $col_offset_size,
        '#default_value' => isset($content['device'][1][$key])?$content['device'][1][$key]:'0',
      ];      
    }
  }
 
  public function render($settings){
    $build = parent::getBuild($settings);
    $devices = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('device');
    $device_offset = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('device_offset');
    $content = $settings['content'];
    $frontend_config = \Drupal::config('visual_editor.config')->get('frontend');
    foreach($devices as $item => $label){
      $build['#attributes']['class'][] = 'col';
      if(!empty($content['device'][0][$item])){
        if($frontend_config['framework'] == 'bootstrap_4' && $item == 'xs'){
          $build['#attributes']['class'][] = 'col-'.$content['device'][0][$item];
        }else{
          $build['#attributes']['class'][] = 'col-'.$item.'-'.$content['device'][0][$item];
        }
      }
      if(!empty($content['device'][1][$item])){
        $build['#attributes']['class'][] = $device_offset[$item].$content['device'][1][$item];
      }
    }
    $build['#theme'] = 've_component';
    return $build;
  }
}