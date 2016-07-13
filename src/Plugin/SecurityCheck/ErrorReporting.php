<?php

/**
 * @file
 * Contains \Drupal\security_review\Plugin\SecurityCheck\ErrorReporting.
 */

namespace Drupal\security_review\Plugin\SecurityCheck;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\security_review\SecurityCheckBase;

/**
 * Defines a security check that checks the error reporting setting.
 *
 * @SecurityCheck(
 *   id = "error_reporting",
 *   title = @Translation("Error Reporting Settings"),
 *   description = @Translation("Defines a security check that checks the error reporting setting."),
 *   namespace = @Translation("Security Review"),
 *   success_message = @Translation("Error reporting set to log only."),
 *   failure_message = @Translation("Errors are written to the screen."),
 *   controls = {
 *     @Translation("SC1"),
 *     @Translation("SC4"),
 *     @Translation("SC5"),
 *   },
 *   help = {
 *    @Translation("As a form of hardening your site you should avoid information disclosure. Drupal by default prints errors to the screen and writes them to the log. Error messages disclose the full path to the file where the error occurred."),
 *   }
 * )
 */
class ErrorReporting extends SecurityCheckBase {

  /**
   * {@inheritdoc}
   */
  public function run() {
    // Get the error level.
    $error_level = \Drupal::configFactory()->get('system.logging')
      ->get('error_level');

    // Determine the result.
    if (is_null($error_level) || $error_level != 'hide') {
      $this->success = FALSE;
    }

    $this->findings = ['level' => $error_level];
  }

  /**
   * {@inheritdoc}
   */
  public function getDetails() {
    if ($this->success) {
      return [];
    }

    $paragraphs = [];
    $paragraphs[] = $this->t('You have error reporting set to both the screen and the log.');
    $paragraphs[] = Link::fromTextAndUrl(
      $this->t('Alter error reporting settings.'),
      Url::fromRoute('system.logging_settings')
    );

    return [
      '#theme' => 'check_evaluation',
      '#paragraphs' => $paragraphs,
      '#items' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDetailsAsPlainText() {
    if ($this->success) {
      return '';
    }

    if (isset($this->findings['level'])) {
      return $this->t('Error level: @level', [
        '@level' => $this->findings['level'],
      ]);
    }
    return '';
  }

}
