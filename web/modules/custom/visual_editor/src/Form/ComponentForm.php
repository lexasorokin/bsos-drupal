<?php

/**
 * @file
 * Contains \Drupal\visual_editor\Form\ComponentForm.
 */

namespace Drupal\visual_editor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\visual_editor\VisualEditorConfig;

class ComponentForm extends FormBase {
  
  protected $effects = [
    'bounce',
    'flash',
    'pulse',
    'rubberBand',
    'shake',
    'headShake',
    'swing',
    'tada',
    'wobble',
    'jello',
    'bounceIn',
    'bounceInDown',
    'bounceInLeft',
    'bounceInRight',
    'bounceInUp',
    'bounceOut',
    'bounceOutDown',
    'bounceOutLeft',
    'bounceOutRight',
    'bounceOutUp',
    'fadeIn',
    'fadeInDown',
    'fadeInDownBig',
    'fadeInLeft',
    'fadeInLeftBig',
    'fadeInRight',
    'fadeInRightBig',
    'fadeInUp',
    'fadeInUpBig',
    'fadeOut',
    'fadeOutDown',
    'fadeOutDownBig',
    'fadeOutLeft',
    'fadeOutLeftBig',
    'fadeOutRight',
    'fadeOutRightBig',
    'fadeOutUp',
    'fadeOutUpBig',
    'flipInX',
    'flipInY',
    'flipOutX',
    'flipOutY',
    'lightSpeedIn',
    'lightSpeedOut',
    'rotateIn',
    'rotateInDownLeft',
    'rotateInDownRight',
    'rotateInUpLeft',
    'rotateInUpRight',
    'rotateOut',
    'rotateOutDownLeft',
    'rotateOutDownRight',
    'rotateOutUpLeft',
    'rotateOutUpRight',
    'hinge',
    'jackInTheBox',
    'rollIn',
    'rollOut',
    'zoomIn',
    'zoomInDown',
    'zoomInLeft',
    'zoomInRight',
    'zoomInUp',
    'zoomOut',
    'zoomOutDown',
    'zoomOutLeft',
    'zoomOutRight',
    'zoomOutUp',
    'slideInDown',
    'slideInLeft',
    'slideInRight',
    'slideInUp',
    'slideOutDown',
    'slideOutLeft',
    'slideOutRight',
    'slideOutUp',
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'visual_editor_component_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $settings = NULL) {
    $form['visual_editor_settings'] = [
      '#type' => 'hidden',
      '#value' => $settings,
    ];
    $form['status_messages'] = [ 
      '#type' => 'status_messages', 
      '#weight' => -10, 
    ];
    $settings = Json::decode($settings);
    $form['settings'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['content'] = [
      '#type' => 'details',
      '#title' => t('Content'),
      '#group' => 'settings',
      '#tree' => true
    ];
    
    if($settings['type'] == 'row'){
      $veConfig = new VisualEditorConfig();
      $components = $veConfig->getComponents();
      $row_options = [];
      foreach($components['row'] as $row){
        if($row['id'] != $settings['id']){
          $row_options[$row['id']] = $row['name']->__toString();
        }
      }
      $form['content']['swith_row'] = [
        '#title' => t('Switch to row'),
        '#type' => 'select',
        '#empty_option' => t('- select -'),
        '#options' => $row_options,
      ];
    }

    $manager = \Drupal::service('plugin.manager.visual_editor');
    $manager->createInstance($settings['id'], [])->buildForm($form, $form_state, $settings);
    if($settings['type'] != 'code'){
      $form['attributes'] = [
        '#type' => 'details',
        '#title' => t('Attributes'),
        '#group' => 'settings',
        '#tree' => true
      ];

      $attributes = $settings['attributes'];
      $form['attributes']['title'] = [
        '#type' => 'textfield',
        '#title' => t('Component title'),
        '#default_value' => isset($settings['attributes']['title'])?$settings['attributes']['title']:'',
        '#description' => t('Enter component title to identify it while editing.')
      ];
      $form['attributes']['id'] = [
        '#type' => 'textfield',
        '#title' => t('Id'),
        '#default_value' => isset($attributes['id']) ? $attributes['id'] : '',
      ];
      $form['attributes']['class'] = [
        '#type' => 'textfield',
        '#title' => t('Class'),
        '#default_value' => isset($attributes['class']) ? $attributes['class'] : '',
      ];
      $form['attributes']['style'] = [
        '#type' => 'textarea',
        '#title' => t('Style'),
        '#default_value' => isset($attributes['style']) ? $attributes['style'] : '',
      ];

      $animation = isset($settings['animation'])?$settings['animation']:[];
      $form['animation'] = [
        '#type' => 'details',
        '#title' => t('Animation'),
        '#group' => 'settings',
        '#tree' => true
      ];
      $form['animation']['effect'] = [
        '#type' => 'select',
        '#title' => t('Effect'),
        '#options' => array_combine($this->effects, $this->effects),
        '#empty_option' => t('- None -'),
        '#default_value' => isset($animation['effect']) ? $animation['effect'] : '',
      ];
    }
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#ajax' => [
        'callback' => [$this, 'submitModalFormAjax'], 
        'event' => 'click',
      ]
    ];
    
    $form['#prefix'] = '<div id="component-form-wrapper">';
    $form['#suffix'] = '</div>';
    return $form;
  }
  
  /** 
   * {@inheritdoc} 
   */ 
  public function validateForm(array &$form, FormStateInterface $form_state) {}
  
  /** 
   * {@inheritdoc} 
   */ 
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitModalFormAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#component-form-wrapper', $form));
    }else{
      $settings = Json::decode($form_state->getValue('visual_editor_settings'));
      $settings['animation'] = $form_state->getValue('animation');
      $manager = \Drupal::service('plugin.manager.visual_editor');
      $settings = $manager->createInstance($settings['id'], [])->submitForm($form, $form_state, $settings);
      if(isset($settings['content']['swith_row']) && !empty($settings['content']['swith_row'])){
        $veConfig = new VisualEditorConfig();
        $components = $veConfig->getComponents();
        foreach($components['row'] as $row){
          if($row['id'] == $settings['content']['swith_row']){
            $settings['name'] = $row['name'];
          }
        }
        $settings['id'] = $settings['content']['swith_row'];
        unset($settings['content']['swith_row']);
      }
      $command = new CloseDialogCommand('#component-settings');
      $response = new AjaxResponse();
      $response->addCommand($command);
      $response->addCommand(new InvokeCommand('.visual_editor_settings', 'updateComponent', [Json::encode($settings)]));
    }
    return $response;
  }

}
