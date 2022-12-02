<?php

/**
 * @file
 * Provides Drupal\visual_editor\VisualEditorInterface
 */

namespace Drupal\visual_editor;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

interface VisualEditorInterface extends PluginInspectionInterface {
  
  /**
   * Return buttons.
   *
   * @return array
   */
  public function getButtons();
  
  /**
   * Return settings.
   *
   * @return array
   */
  public function submitForm(array &$form, FormStateInterface $form_state, array $settings);
}
