<?php

namespace Drupal\d8_user_to_d9_user\Plugin\migrate\source;

use Drupal\Core\Database\Connection;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * The source database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $sourceDatabase;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, $migration_id = NULL) {
    // Set the default migration ID to the plugin ID if not provided.
    $migration_id = $migration_id ?: $plugin_id;
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('plugin.manager.migration')->createInstance($migration_id)
    );
  }

  /**
   * Constructs a new D8User source plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Database\Connection $source_database
   *   The source database connection.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $source_database, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $source_database, $migration);
    $this->sourceDatabase = $source_database;
  }

  /**
   * {@inheritdoc}
   */
  public function query(): \Drupal\Core\Database\Query\SelectInterface {
    $query = $this->select('users', 'u')
      ->fields('u', ['uid', 'name', 'mail', 'created', 'changed', 'status']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
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
