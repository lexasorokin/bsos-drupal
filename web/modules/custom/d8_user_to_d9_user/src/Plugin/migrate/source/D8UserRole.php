<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for Drupal 8 user roles.
 *
 * @MigrateSource(
 *   id = "d8_user_role",
 *   source_module = "user",
 * )
 */
class D8UserRole extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('user__roles', 'ur')
      ->fields('ur', ['entity_id', 'roles_target_id']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'entity_id' => $this->t('User ID'),
      'roles_target_id' => $this->t('Role ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'entity_id' => [
        'type' => 'integer',
      ],
    ];
  }

}
