<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Annotation\VisualEditorComponent.
 */

namespace Drupal\visual_editor\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a element item annotation object.
 *
 * Plugin Namespace: Plugin\VisualEditorComponent
 *
 * @see \Drupal\visual_editor\Plugin\VisualEditorComponent
 * @see plugin_api
 *
 * @Annotation
 */
class VisualEditorComponent extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The plugin type.
   *
   * @var string
   */
  public $type;

  /**
   * The name of the component.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

  /**
   * The description of the component.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * Acceptable plugin types
   *
   * @var string
   */
  public $accepts;
  
  /**
   * Icon class
   *
   * @var string
   */
  public $iconClass;

}
