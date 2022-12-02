<?php

/**
 * @file
 * Contains VisualEditorManager.
 */

namespace Drupal\visual_editor;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Serialization\Yaml;

/**
 * VisualEditor plugin manager.
 */
class VisualEditorManager extends DefaultPluginManager {

  /**
   * Constructs an VisualEditorManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations,
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/VisualEditorComponent', $namespaces, $module_handler, 'Drupal\visual_editor\VisualEditorInterface', 'Drupal\visual_editor\Annotation\VisualEditorComponent');
    $this->alterInfo('visual_editor_info');
    $this->setCacheBackend($cache_backend, 'visual_editor');
  }

  /**
   * Constructs parts of the form needed to use Entity Browser.
   *
   * @param array $files
   *   An array representing the current configuration + form state.
   *
   * @return array
   *   A render array representing Entity Browser components.
   */
  public function browserForm($fids, $table_data) {
    $selection = [
      '#type' => 'container',
      '#attributes' => ['id' => 'image-embed-block-browser'],
    ];
    $selection['fids'] = [
      '#type' => 'entity_browser',
      '#entity_browser' => 'files',
      '#entity_browser_validators' => [
        'entity_type' => ['type' => 'file'],
      ],
      '#selection_mode' => 'selection_edit',
      '#process' => [
        ['\Drupal\entity_browser\Element\EntityBrowserElement', 'processEntityBrowser'],
        [get_called_class(), 'processEntityBrowser'],
      ],
    ];

    $order_class = 'image-embed-block-delta-order';

    $selection['table'] = [
      '#type' => 'table',
      '#header' => [
        t('Preview'),
        t('Filename'),
        t('Metadata'),
        t('Order', [], ['context' => 'Sort order']),
      ],
      '#empty' => t('No files yet'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => $order_class,
        ],
      ],
    ];

    $delta = 0;
    if(!empty($fids)){
      $files = File::loadMultiple($fids);
      foreach ($files as $file) {
        $uri = $file->getFileUri();
        $image = \Drupal::service('image.factory')->get($uri);
        if ($image->isValid()) {
          $width = $image->getWidth();
          $height = $image->getHeight();
        }
        else {
          $width = $height = NULL;
        }

        $display = [
          '#theme' => 'image_style',
          '#width' => $width,
          '#height' => $height,
          '#style_name' => 'thumbnail',
          '#uri' => $uri,
        ];

        $fid = $file->id();
        $selection['table'][$fid] = [
          '#attributes' => [
            'class' => ['draggable'],
            'data-entity-id' => $file->getEntityTypeId() . ':' . $fid,
          ],
          'display' => $display,
          'filename' => ['#markup' => $file->label()],
          'alt' => [
            '#type' => 'textfield',
            '#title' => t('Alternative text'),
            '#default_value' => isset($table_data[$fid]['alt']) ? $table_data[$fid]['alt'] : '',
            '#size' => 45,
            '#maxlength' => 512,
            '#description' => t('This text will be used by screen readers, search engines, or when the image cannot be loaded.'),
          ],
          '_weight' => [
            '#type' => 'weight',
            '#title' => t('Weight for row @number', ['@number' => $delta + 1]),
            '#title_display' => 'invisible',
            '#delta' => count($files),
            '#default_value' => $delta,
            '#attributes' => ['class' => [$order_class]],
          ],
        ];

        $delta++;
      }
    }
    return $selection;
  }

  /**
   * Render API callback: Processes the entity browser element.
   */
  public static function processEntityBrowser(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['entity_ids']['#ajax'] = [
      'callback' => [get_called_class(), 'updateCallback'],
      'wrapper' => 'image-embed-block-browser',
      'event' => 'entity_browser_value_updated',
    ];
    return $element;
  }

  /**
   * AJAX callback: Re-renders the Entity Browser button/table.
   */
  public static function updateCallback(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $parents = array_slice($trigger['#array_parents'], 0, -2);
    $selection = NestedArray::getValue($form, $parents);
    return $selection;
  }

  public function getFids($files) {
    $fids = [];
    if (!empty($files)) {
      $files = explode(' ', $files);
      $fids = array_map(function($fid) {
            $fid = explode(':', $fid);
            return $fid[1];
          }, $files);
    }
    return $fids;
  }

  public function getFrontendConfig($setting){
    $core_path = \Drupal::service('file_system')->realpath(
      drupal_get_path('module', 'visual_editor')
    );
    $file = $core_path.DIRECTORY_SEPARATOR.'visual_editor.frontend.yml';
    $content = file_get_contents($file);
    $settings = Yaml::decode($content); 
    $frontend_config = \Drupal::config('visual_editor.config')->get('frontend');
    return $settings[$frontend_config['framework']][$setting];
  }
}