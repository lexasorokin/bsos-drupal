<?php
namespace Drupal\visual_editor;
class VisualEditorConfig {
  protected $components = [];
  protected $libraries = [];
  protected $jsSettings = [];
  
  public function __construct() {
    $manager = \Drupal::service('plugin.manager.visual_editor');
    $plugins = $manager->getDefinitions();
    $this->components = [];
    if ($plugins != NULL) {
      ksort($plugins);
      foreach ($plugins as $key => $plugin) {
        $buttons = $manager->createInstance($plugin['id'], [])->getButtons();  
        $this->libraries = array_merge($this->libraries, $manager->createInstance($plugin['id'], [])->getLibraries());  
        $this->jsSettings[$plugin['id']] = $manager->createInstance($plugin['id'], [])->getJSSettings();
        $first_key = key($buttons);
        if(is_numeric($first_key)){
          foreach($buttons as $button){
            $this->components[$plugin['type']][] = $button;
          }
        }else{
          $this->components[$plugin['type']][] = $buttons;
        }
      }
    }
  }
  public function getComponents() {
    return $this->components;
  }
  public function getLibraries() {
    return array_unique($this->libraries);
  }
  public function getJSSettings() {
    return $this->jsSettings;
  }
}
