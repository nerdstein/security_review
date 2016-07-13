<?php

/**
 * @file
 * Contains Drupal\security_review\Annotation\SecurityCheck.
 */

namespace Drupal\security_review\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a security check annotation object.
 *
 * @Annotation
 */
class SecurityCheck extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the check.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * The description of the check.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * The namespace of the check.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $namespace;

  /**
   * The success message of the check.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $success_message;

  /**
   * The failure message of the check.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $failure_message;

  /**
   * An array of controls that the check supports.
   *
   * @var array
   */
  public $controls = array();

  /**
   * An array of help paragraphs.
   *
   * @var array
   */
  public $help = array();

}
