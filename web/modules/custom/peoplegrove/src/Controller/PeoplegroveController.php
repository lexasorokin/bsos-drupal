<?php

namespace Drupal\peoplegrove\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for peoplegrove routes.
 */
class PeoplegroveController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
