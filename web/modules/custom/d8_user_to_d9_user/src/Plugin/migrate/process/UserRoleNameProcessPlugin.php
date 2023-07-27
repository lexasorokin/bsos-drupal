<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin to convert role IDs to role names.
 *
 * @MigrateProcessPlugin(
 *   id = "user_role_name_process"
 * )
 */
class UserRoleNameProcessPlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Load the role entity and get the role name.
    $role = \Drupal::entityTypeManager()->getStorage('user_role')->load($value);
    if ($role) {
      return $role->label();
    }
    return NULL;
  }

}
