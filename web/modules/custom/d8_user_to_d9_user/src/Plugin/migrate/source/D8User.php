<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for Drupal 8 user migration.
 *
 * @MigrateSource(
 *   id = "d8_user",
 *   source_module = "d8_user_to_d9_user",
 * )
 */
class D8User extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('users_field_data', 'u')
      ->fields('u', ['uid', 'name', 'mail', 'created', 'changed', 'status']);

    // Join the user roles from the "user_roles" table.
    //$query->leftJoin('user__roles', 'ur', 'u.uid = ur.entity_id');
    //$query->addField('ur', 'roles_target_id', 'roles');

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
      //'roles' => $this->t('User roles (comma-separated)'),
      // Add other user fields you want to migrate
      //'langcode' => $this->t('Langcode'),
      //'timezone' => $this->t('Timezone'),
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
