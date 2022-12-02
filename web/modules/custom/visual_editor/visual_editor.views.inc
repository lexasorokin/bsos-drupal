<?php

/**
 * Implements hook_views_data_alter().
 */
function visual_editor_views_data_alter(array &$data) {
  $data['file_managed']['image_file_formatter'] = array(
    'title' => t('Image file formatter'),
    'help' => t('Render file as image'),
    'group' => t('Content'),
    'field' => array(
      'id' => 'image_file_formatter',
    ),
  );
}