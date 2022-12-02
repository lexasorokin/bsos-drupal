<?php

/**
 * @file
 * Provides Drupal\visual_editor\VisualEditorBase.
 */

namespace Drupal\visual_editor;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

abstract class VisualEditorBase extends PluginBase implements VisualEditorInterface {

  /**
   * Update form elements
   */
  abstract public function buildForm(array &$form, FormStateInterface $form_state, array $settings);

  /**
   * Return the rendered element.
   * @param $settings
   * @return array
   */
  abstract public function render($settings);

  /**
   * Get buttons for editor from plugin
   * @return array
   */
  public function getButtons() {
    $info = [
      'id' => $this->pluginDefinition['id'],
      'type' => $this->pluginDefinition['type'],
      'name' => $this->pluginDefinition['name'],
      'description' => $this->pluginDefinition['description'],
      'iconClass' => $this->pluginDefinition['iconClass'],
      'attributes' => [],
      'content' => [],
    ];
    $info['accepts'] = isset($this->pluginDefinition['accepts']) ? $this->pluginDefinition['accepts'] : [];
    if (isset($this->pluginDefinition['accepts'])) {
      $info['children'] = [];
    }

    return $info;
  }
  
  /**
   * Link component
   */
  protected function getLinkElement(&$form, $link_content){
    $form['link'] = [
      '#type' => 'details',
      '#title' => t('Link component'),
      '#group' => 'settings',
      '#tree' => true
    ];
    $form['link']['url'] = [
      '#title' => t('URL'),
      '#type' => 'url',
      '#default_value' => (isset($link_content['url'])) ? static::getUriAsDisplayableString($link_content['url']) : NULL,
      '#element_validate' => [[get_called_class(), 'validateUriElement']],
    ];
    $form['link']['target'] = [
      '#title' => t('Target'),
      '#type' => 'select',
      '#empty_option' => t(' - select - '),
      '#options' => [
        '_blank' => '_blank',
        '_self' => '_self',
        '_parent' => '_parent',
        '_top' => '_top',
      ],
      '#default_value' => isset($link_content['target']) ? $link_content['target'] : '',
    ];
    $form['link']['id'] = [
      '#type' => 'textfield',
      '#title' => t('Id'),
      '#default_value' => isset($link_content['id']) ? $link_content['id'] : '',
    ];
    $form['link']['class'] = [
      '#type' => 'textfield',
      '#title' => t('Class'),
      '#default_value' => isset($link_content['class']) ? $link_content['class'] : '',
    ];
  }

  /**
   * Submit handler for component form
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param array $settings
   * @return type
   */
  public function submitForm(array &$form, FormStateInterface $form_state, array $settings) {
    $settings['content'] = $form_state->getValue('content');
    $settings['link'] = (!empty($form_state->getValue(['link', 'url'])))?$form_state->getValue('link'):null;
    $settings['attributes'] = $form_state->getValue('attributes');
    $settings['animation'] = $form_state->getValue('animation');
    return $settings;
  }

  /**
   * Create build array
   * @param type $settings
   * @return type
   */
  protected function getBuild($settings) {
    $build = [
      '#settings' => $settings,
      '#attributes' => [],
      '#content' => [],
    ];
    if (!empty($settings['attributes']['id'])) {
      $build['#attributes']['id'] = $settings['attributes']['id'];
    }
    $build['#attributes']['class'][] = 've--'.$settings['id'];
    if (!empty($settings['attributes']['class'])) {
      $build['#attributes']['class'][] = $settings['attributes']['class'];
    }
    if(isset($settings['animation']['effect']) && !empty($settings['animation']['effect'])){
      $build['#attributes']['class'][] = 've-animate';
      $build['#attributes']['data-effect'] = $settings['animation']['effect'];
      $build['#attached']['library'][] = 'visual_editor/visual_editor.animate';
    }
    if (!empty($settings['attributes']['style'])) {
      $build['#attributes']['style'] = $settings['attributes']['style'];
    }
    return $build;
  }

  /**
   * Get libraries for editor
   * @return type
   */
  public function getLibraries() {
    return [
      'core/jquery.form',
      'core/jquery.ui.sortable',
      'visual_editor/drupal.visual_editor',
      'visual_editor/visual_editor.fontawesome',
      'core/drupal.vertical-tabs',
      'core/drupal.dialog.ajax',
      'core/drupal.dialog'
    ];
  }

  /**
   * Get js settings for editor
   * @return type
   */
  public function getJSSettings() {
    return [];
  }
  
  /**
   * Gets the URI without the 'internal:' or 'entity:' scheme.
   *
   * The following two forms of URIs are transformed:
   * - 'entity:' URIs: to entity autocomplete ("label (entity id)") strings;
   * - 'internal:' URIs: the scheme is stripped.
   *
   * This method is the inverse of ::getUserEnteredStringAsUri().
   *
   * @param string $uri
   *   The URI to get the displayable string for.
   *
   * @return string
   *
   * @see static::getUserEnteredStringAsUri()
   */
  protected static function getUriAsDisplayableString($uri) {
    $scheme = parse_url($uri, PHP_URL_SCHEME);

    // By default, the displayable string is the URI.
    $displayable_string = $uri;

    // A different displayable string may be chosen in case of the 'internal:'
    // or 'entity:' built-in schemes.
    if ($scheme === 'internal') {
      $uri_reference = explode(':', $uri, 2)[1];

      // @todo '<front>' is valid input for BC reasons, may be removed by
      //   https://www.drupal.org/node/2421941
      $path = parse_url($uri, PHP_URL_PATH);
      if ($path === '/') {
        $uri_reference = '<front>' . substr($uri_reference, 1);
      }

      $displayable_string = $uri_reference;
    }

    return $displayable_string;
  }

  /**
   * Gets the user-entered string as a URI.
   *
   * The following two forms of input are mapped to URIs:
   * - entity autocomplete ("label (entity id)") strings: to 'entity:' URIs;
   * - strings without a detectable scheme: to 'internal:' URIs.
   *
   * This method is the inverse of ::getUriAsDisplayableString().
   *
   * @param string $string
   *   The user-entered string.
   *
   * @return string
   *   The URI, if a non-empty $uri was passed.
   *
   * @see static::getUriAsDisplayableString()
   */
  protected static function getUserEnteredStringAsUri($string) {
    // By default, assume the entered string is an URI.
    $uri = $string;

    // Detect entity autocomplete string, map to 'entity:' URI.
    if (!empty($string) && parse_url($string, PHP_URL_SCHEME) === NULL) {
      // @todo '<front>' is valid input for BC reasons, may be removed by
      //   https://www.drupal.org/node/2421941
      // - '<front>' -> '/'
      // - '<front>#foo' -> '/#foo'
      if (strpos($string, '<front>') === 0) {
        $string = '/' . substr($string, strlen('<front>'));
      }
      $uri = 'internal:' . $string;
    }

    return $uri;
  }

  /**
   * Form element validation handler for the 'uri' element.
   *
   * Disallows saving inaccessible or untrusted URLs.
   */
  public static function validateUriElement($element, FormStateInterface $form_state, $form) {
    $uri = static::getUserEnteredStringAsUri($element['#value']);
    $form_state->setValueForElement($element, $uri);

    // If getUserEnteredStringAsUri() mapped the entered value to a 'internal:'
    // URI , ensure the raw value begins with '/', '?' or '#'.
    // @todo '<front>' is valid input for BC reasons, may be removed by
    //   https://www.drupal.org/node/2421941
    if (parse_url($uri, PHP_URL_SCHEME) === 'internal' && !in_array($element['#value'][0], ['/', '?', '#'], TRUE) && substr($element['#value'], 0, 7) !== '<front>') {
      $form_state->setError($element, t('Manually entered paths should start with /, ? or #.'));
      return;
    }
  }
  
  /**
   * Renderable array for image
   * @param int $fid
   * @param string $style
   * @param array $build
   * @return string
   */
  public function renderImage($fid, $style, $build){
    $file = File::load($fid);
    if($file){
      $path = $file->getFileUri();
    }else{
      return [];
    }
    if(!empty($style)){
      $build['#theme'] = 'image_style';
      $build['#style_name'] = $style;
    }else{
      $build['#theme'] = 'image';
    }
    $build['#uri'] = $path;
    return $build;
  }

}