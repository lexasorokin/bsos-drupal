<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin to set a default label for roles with an empty label.
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
    // Set a default label for roles with an empty label.
    if (empty($value)) {
      return 'Default Role';
    }

    return $value;
  }

}
