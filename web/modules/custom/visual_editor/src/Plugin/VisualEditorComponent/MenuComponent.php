<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\MenuComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Provides a 'menu' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "menu",
 *   type = "widget",
 *   name = @Translation("Menu"),
 *   description = @Translation("Add menu to page"),
 *   iconClass = "fa fa-bars",
 * )
 */
class MenuComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $form['content']['menu'] = [
      '#type' => 'select',
      '#title' => t('Menu'),
      '#empty_option' => t('- Select menu -'),
      '#required' => true,
      '#options' => menu_ui_get_menus(),
      '#default_value' => isset($content['menu'])?$content['menu']:'',
    ];
    
  }

  public function render($settings) {
    $build_parent = parent::getBuild($settings);
    $build = [];
    if(!empty($settings['content']['menu'])){
      // Format and add main menu to theme.
      $menu = \Drupal::menuTree()->load($settings['content']['menu'], new MenuTreeParameters());
      $build = \Drupal::menuTree()->build($menu);
      $build['#attributes'] = $build_parent['#attributes'];
      $build['#attached'] = isset($build_parent['#attached'])?$build_parent['#attached']:[];
    }
    return $build;
  }

}