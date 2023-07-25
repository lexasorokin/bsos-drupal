<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Query\SelectInterface;

/**
 * Source plugin for retrieving user passwords from Drupal 8.
 *
 * @MigrateSource(
 *   id = "d8_user_password",
 *   source_module = "d8_user_to_d9_user",
 * )
 */
class D8UserPassword extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query(): SelectInterface {
    $query = $this->select('users', 'u')
      ->fields('u', ['uid', 'pass']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
    return [
      'uid' => $this->t('User ID'),
      'pass' => $this->t('User password'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'u',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row): bool {
    // Add any additional data processing or transformations here if needed.
    return parent::prepareRow($row);
  }

}
