<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a process plugin for setting a default label for user roles.
 *
 * @MigrateProcessPlugin(
 *   id = "default_role_label_process"
 * )
 */
class DefaultRoleLabelProcessPlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // If the value is not a string, convert it to a string.
    if (!is_string($value)) {
      $value = (string) $value;
    }

    // If the value is still empty, set a default label.
    if (empty($value)) {
      $value = 'Default Role Label';
    }

    return $value;
  }

}
