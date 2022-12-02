<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\BlockPluginComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'block_plugin' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "block_plugin",
 *   type = "widget",
 *   name = @Translation("Block plugin"),
 *   description = @Translation("Add block plugin to the content."),
 *   iconClass = "fa fa-plug",
 * )
 */
class BlockPluginComponent extends VisualEditorBase {

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $options = $this->getAllBlocks();
    $form['content']['block_plugin'] = [
      '#title' => t('Block plugin'),
      '#type' => 'select',
      '#options' => $options,
      '#required' => true,
      '#empty_option' => t('- None -'),
      '#default_value' => isset($content['block_plugin']) ? $content['block_plugin'] : '',
    ];
    $form['content']['display_title'] = [
      '#type' => 'checkbox',
      '#title' => t('Display title?'),
      '#default_value' => isset($content['display_title']) ? $content['display_title'] : false,
    ];
  }

  public function render($settings) {
    $build = parent::getBuild($settings);
    if (isset($settings['content']['block_plugin'])) {
      $output = $this->renderBlock($settings['content']['block_plugin'], $settings['content']['display_title']);
      $build['#theme'] = 've_component';
      $build['#content'] = $output;
    }
    return $build;
  }

  /**
   * Get all block of drupal.
   *
   * @staticvar array $_list_blocks_array
   *
   * @return array
   *   Public static function getAllBlocks array list_blocks_array.
   */
  public function getAllBlocks() {
    $_list_blocks_array = [];
    if (empty($_list_blocks_array)) {
      $theme_default = \Drupal::config('system.theme')->get('default');
      $block_storage = \Drupal::entityManager()->getStorage('block');
      $entity_ids = $block_storage->getQuery()->condition('theme', $theme_default)->execute();
      $entities = $block_storage->loadMultiple($entity_ids);
      $_list_blocks_array = [];
      foreach ($entities as $block_id => $block) {
        $_list_blocks_array[$block_id] = $block->label();
      }
      asort($_list_blocks_array);
    }
    return $_list_blocks_array;
  }
  
  /**
   * Render drupal block.
   *
   * @param string $bid
   *   Public static function renderBlock bid.
   * @param bool $title_enable
   *   Public static function renderBlock title_enable.
   * @param string $section
   *   Public static function renderBlock section.
   *
   * @return string [markuphtml]
   *   Public static function renderBlock string.
   */
  public function renderBlock($bid, $title_enable = TRUE, $section = '') {
    $block_content = [];
    if ($bid && !empty($bid)) {
      $block = \Drupal\block\Entity\Block::load($bid);
      if (isset($block) && !empty($block)) {
        $title = $block->label();
        $block_content = \Drupal::entityManager()
          ->getViewBuilder('block')
          ->view($block);
      }
    }
    return $block_content;
  }
}