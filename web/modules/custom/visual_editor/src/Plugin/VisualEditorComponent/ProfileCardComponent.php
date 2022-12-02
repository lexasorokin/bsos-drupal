<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Plugin\VisualEditorComponent\ProfileCardComponent.
 */

namespace Drupal\visual_editor\Plugin\VisualEditorComponent;

use Drupal\visual_editor\VisualEditorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Provides a 'profile_card' visual editor compoent.
 *
 * @VisualEditorComponent(
 *   id = "profile_card",
 *   type = "widget",
 *   name = @Translation("Profile card"),
 *   description = @Translation("Profile card containing name, picture, and social links"),
 *   iconClass = "fa fa-id-card",
 * )
 */
class ProfileCardComponent extends VisualEditorBase {
  
  protected $social_links = [
                              'facebook', 
                              'twitter', 
                              'google-plus', 
                              'linkedin',  
                              'youtube',  
                              'behance', 
                              'drupal',
                            ];

  public function buildForm(array &$form, FormStateInterface $form_state, array $settings) {
    $content = $settings['content'];
    $styles = \Drupal::entityTypeManager()->getStorage('image_style')->loadMultiple();
    $image_styles = [];
    foreach ($styles as $style) {
      $image_styles[$style->id()] = $style->getName();
    }
    $form['content']['name'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#title' => t('Name'),
      '#default_value' => isset($content['name']) ? $content['name'] : '',
    ];
    $form['content']['designation'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#title' => t('Designation'),
      '#default_value' => isset($content['designation']) ? $content['designation'] : '',
    ];
    $fids = $form_state->getValue([
      'content',
      'image',
      'fids',
      'entity_ids',
    ]);

    $files = [];
    if (!empty($fids)) {
      $files = \Drupal::service('plugin.manager.visual_editor')->getFids($fids);
    }
    else {
      $files = array_keys($settings['content']['image']['table']);
    }
    $form['content']['image'] = \Drupal::service('plugin.manager.visual_editor')->browserForm($files, $content['image']['table']);
    $form['content']['image_format'] = [
      '#type' => 'select',
      '#title' => t('Image format'),
      '#options' => $image_styles,
      '#empty_option' => t('- None -'),
      '#default_value' => isset($content['image_format']) ? $content['image_format'] : '',
    ];
    
    $form['content']['social_links'] = [
      '#type' => 'table',
      '#tree' => true,
      '#prefix' => '<div id="social-links-wrapper">',
      '#suffix' => '</div>',
      '#header' => [t('Item'), t('Weight'), t('Platform'), t('Social links')],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'social-links-weight',
        ],
      ],
    ];
    foreach ($this->social_links as $delta => $platform) {
      $social_link = isset($content['social_links'])?$content['social_links'][$delta]:[];
      $form['content']['social_links'][$delta]['#attributes']['class'][] = 'draggable';
      $form['content']['social_links'][$delta]['#weight'] = isset($social_link['weight']) ? $social_link['weight'] : $delta;
      $form['content']['social_links'][$delta]['item'] = ['#plain_text' => ''];
      $form['content']['social_links'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => 'social link']),
        '#title_display' => 'invisible',
        '#attributes' => ['class' => ['social-links-weight']],
        '#default_value' => isset($social_link['weight']) ? $social_link['weight'] : $delta
      ];
      $form['content']['social_links'][$delta]['platform'] = ['#markup' => '<i class="fa fa-'.$platform.'"></i>'];
      $form['content']['social_links'][$delta]['info']['url'] = [
        '#title' => t('URL'),
        '#type' => 'url',
        '#title_display' => 'invisible',
        
        '#default_value' => (isset($social_link['info']['url'])) ? static::getUriAsDisplayableString($social_link['info']['url']) : NULL,
        '#element_validate' => [[get_called_class(), 'validateUriElement']],
      ];
      $form['content']['social_links'][$delta]['info']['platform'] = [
        '#type' => 'hidden',
        '#default_value' => $platform,
      ];
    }
    uasort($form['content']['social_links'], ['Drupal\Component\Utility\SortArray', 'sortByWeightProperty']);
  }
  public function render($settings) {
    $build = parent::getBuild($settings);
    $build['#theme'] = 've_profile_card';
    $content = $settings['content'];
    if(isset($content['image'][0])){
      $build['#content']['image'] = $this->renderImage($content['image'][0], $content['image_format'], []);
    }else{
      $fids = array_keys($content['image']['table']);
      $img_attr = [
        '#attributes' => [
          'alt' => $content['image']['table'][$fids[0]]['alt']
          ]
        ];
      $build['#content']['image'] = $this->renderImage($fids[0], $content['image_format'], $img_attr);
    }
    $build['#content']['name'] = $content['name'];
    $build['#content']['designation'] = $content['designation'];
    
    if(!empty($content['social_links'])){
      uasort($content['social_links'], 'Drupal\Component\Utility\SortArray::sortByWeightElement');
      foreach($content['social_links'] as $delta => $social_link){
        if(!empty($social_link['info']['url'])){
          $social_link['info']['url'] = ($social_link['info']['url'] == '<front>')?'internal:/':$social_link['info']['url'];
          $social_link['info']['url'] = Url::fromUri($social_link['info']['url'])->toString();
          $build['#content']['social_links'][$delta] = $social_link['info'];
        }
      }
    }
    $build['#attached']['library'][] = 'visual_editor/visual_editor.front';
    $build['#attached']['library'][] = 'visual_editor/visual_editor.fontawesome';
    return $build;
  }
}