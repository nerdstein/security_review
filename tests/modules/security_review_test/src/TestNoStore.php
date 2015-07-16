<?php

/**
 * @file
 * Contains \Drupal\security_review_test\TestNoStore.
 */

namespace Drupal\security_review_test;

/**
 * A test security check for testing extensibility.
 * This one does not store findings.
 */
class TestNoStore extends Test {

  /**
   * @inheritDoc
   */
  public function getTitle() {
    return 'Test without storing findings';
  }

  /**
   * {@inheritdoc}
   */
  public function storesFindings() {
    return FALSE;
  }

}
