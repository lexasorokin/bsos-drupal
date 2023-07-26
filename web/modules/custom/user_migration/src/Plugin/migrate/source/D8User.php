<?php

namespace Drupal\user_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for Drupal 8 user migration.
 *
 * @MigrateSource(
 *   id = "d8_user",
 *   source_module = "user_migration",
 * )
 */
class D8User extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('users_field_data', 'u')
      ->fields('u', ['uid', 'name', 'mail', 'created', 'changed', 'status']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'mail' => $this->t('Email'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Changed timestamp'),
      'status' => $this->t('User status (0 for blocked, 1 for active)'),
      // Add other user fields you want to migrate
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'u',
      ],
    ];
  }

}
