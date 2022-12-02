<?php

namespace Drupal\visual_editor\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Component\Serialization\Json;

/**
 * @Filter(
 *   id = "visual_editor_filter",
 *   title = @Translation("Visual editor filter"),
 *   description = @Translation("Enable visual editor."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class VisualEditorFilter extends FilterBase {

  public function process($text, $langcode) {
    $manager = \Drupal::service('plugin.manager.visual_editor');
    $text = Json::decode($text);
    $build = $this->renderText($manager, $text);
    $renderedText = '';
    if(!empty($build)){
      $content = \Drupal::service('renderer')->render($build);
      $renderedText = is_string($content)? $content : $content->__toString();
    }
    return new FilterProcessResult($renderedText);
  }

  private function renderText($manager, $text) {
    $build = [];
    foreach ($text as $key => $settings) {
      $build["{$settings['id']}_{$key}"] = $manager->createInstance($settings['id'], [])->render($settings);
      if (isset($settings['children']) && count($settings['children'])) {
        $build["{$settings['id']}_{$key}"]['#content'] = $this->renderText($manager, $settings['children']);
      }
    }
    return $build;
  }

}