<?php
namespace Drupal\visual_editor\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure visual editor settings.
 */
class VisualEditorConfigForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'visual_editor_config';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'visual_editor.config',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('visual_editor.config'); 
    $form['ve_config'] = [
      '#type' => 'vertical_tabs',
    ];
    
    $gmap_config = $config->get('gmap');
    $form['gmap'] = [
      '#type' => 'details',
      '#title' => t('Google Map Configuration'),
      '#description' => t('Manage google map configuration like API key & marker icon.'),
      '#group' => 've_config',
      '#tree' => true
    ];
    $form['gmap']['key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $gmap_config['key'],
    ];
    $form['gmap']['icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Marker icon'),
      '#description' => $this->t('Enter absolute URL of the marker icon. e.g. http://localhost/sites/default/files/marker.png'),
      '#default_value' => $gmap_config['icon'],
    ];
    
    $frontend_config = $config->get('frontend');
    $form['frontend'] = [
      '#type' => 'details',
      '#title' => t('Frontend Configuration'),
      '#description' => t('Choose which frontend framework is used like bootstrap 3 or bootstrap 4.'),
      '#group' => 've_config',
      '#tree' => true
    ];
    $form['frontend']['framework'] = [
      '#type' => 'select',
      '#title' => $this->t('Framework'),
      '#options' => [
        'bootstrap_3' => t('Bootstrap 3'),
        'bootstrap_4' => t('Bootstrap 4'),
      ],
      '#default_value' => $frontend_config['framework'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('visual_editor.config')
      ->set('frontend', $form_state->getValue('frontend'))
      ->set('gmap', $form_state->getValue('gmap'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}