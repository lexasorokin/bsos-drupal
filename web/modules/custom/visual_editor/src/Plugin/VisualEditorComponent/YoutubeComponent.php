<?php
/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\YoutubeComponent.
 */
namespace Drupal\visual_editor\Plugin\VisualEditorComponent;
use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'youtube' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "youtube",
 *   type = "widget",
 *   name = @Translation("Youtube Embed"),
 *   description = @Translation("Youtube embeded video"),
 *   iconClass = "fa fa-youtube-play",
 * )
 */
class YoutubeComponent extends VisualEditorBase {
  public function buildForm(array &$form, FormStateInterface $form_state, array $settings){
    $content = $settings['content'];
    $form['content']['embed_url'] = [
      '#type' => 'url',
      '#required' => true,
      '#title' => t('Youtube URL'),
      '#default_value' => isset($content['embed_url']) ? $content['embed_url'] : '',
    ];
    $options = \Drupal::service('plugin.manager.visual_editor')->getFrontendConfig('aspect_ratio');
    $form['content']['aspect_ratio'] = [
      '#type' => 'select',
      '#required' => true,
      '#title' => t('Aspect ratio'),
      '#options' => $options,
      '#default_value' => isset($content['aspect_ratio']) ? $content['aspect_ratio'] : '',
    ];
  }
  public function render($settings){
    preg_match(YOUTUBE_REGX, $settings['content']['embed_url'], $matches);
    $settings['content']['embed_url'] = '//youtube.com/embed/'.$matches[1].'?rel=0';
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_embeded_video';
    $build['#content'] = $settings['content'];
    return $build;
  }
}