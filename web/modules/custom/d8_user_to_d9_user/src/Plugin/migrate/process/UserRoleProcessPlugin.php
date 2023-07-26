<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Plugin to process user roles.
 *
 * @MigrateProcessPlugin(
 *   id = "user_role_process"
 * )
 */
class UserRoleProcessPlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, array $migrate_executable, Row $row, $destination_property) {
    // Split the comma-separated role IDs into an array.
    return explode(',', $value);
  }

}
