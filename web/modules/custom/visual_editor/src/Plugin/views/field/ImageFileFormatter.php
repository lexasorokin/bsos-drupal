<?php
 
/**
 * @file
 * Definition of Drupal\visual_editor\Plugin\views\field\ImageFileFormatter
 */
 
namespace Drupal\visual_editor\Plugin\views\field;
 
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
 
/**
 * Field handler to show image file.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("image_file_formatter")
 */
class ImageFileFormatter extends FieldPluginBase {
 
  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }
 
  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['image_styles'] = ['thumbnail'];
    return $options;
  }
 
  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $options = [];
    $styles = \Drupal::entityTypeManager()->getStorage('image_style')->loadMultiple();
    foreach ($styles as $style) {
      $options[$style->id()] = $style->getName();
    }
    $form['image_styles'] = array(
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->options['image_styles'],
      '#options' => $options,
    );
 
    parent::buildOptionsForm($form, $form_state);
  }
 
  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $file = $values->_entity;
    $build = [];
    $build['#theme'] = 'image_style';
    $build['#attributes']['data-fid'] = $file->id();
    $build['#style_name'] = $this->options['image_styles'];
    $build['#uri'] = $file->getFileUri();
    return $build;
  }
}