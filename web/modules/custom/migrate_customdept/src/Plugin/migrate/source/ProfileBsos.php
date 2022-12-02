<?php

namespace Drupal\migrate_customdept\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 profile source from database.
 *
 * @MigrateSource(
 *   id = "d7_profile",
 *   source_module = "profile2",
 * )
 */
class ProfileBsos extends FieldableEntity {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('profile', 'pr')
      ->fields('pr', [
        'pid',
        'type',
        'uid',
        'label',
        'created',
        'changed'
    ]);
    if (isset($this->configuration['profile_type'])) {
      $query->condition('pr.type', $this->configuration['profile_type']);
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Get Field API field values.
    foreach (array_keys($this->getFields('profile2', $row->getSourceProperty('type'))) as $field) {
      $pid = $row->getSourceProperty('pid');
      $row->setSourceProperty($field, $this->getFieldValues('profile2', $field, $pid));
    }

    $row->setSourceProperty('profile_id', $row->getSourceProperty('pid'));
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'pid' => $this->t('Profile ID'),
      'type' => $this->t('Type'),
      'uid' => $this->t('User ID'),
      'label' => $this->t('Label'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['pid']['type'] = 'integer';
    $ids['pid']['alias'] = 'pr';
    return $ids;
  }

}
