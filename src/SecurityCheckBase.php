<?php
/**
 * @file
 * Provides Drupal\security_review\SecurityCheckBase.
 */

namespace Drupal\security_review;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

abstract class SecurityCheckBase extends PluginBase implements SecurityCheckInterface {
  use StringTranslationTrait;

  protected $findings = array();
  protected $success = TRUE;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['title'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function getNamespace() {
    return $this->pluginDefinition['namespace'];
  }

  /**
   * {@inheritdoc}
   */
  public function getControls() {
    return $this->pluginDefinition['controls'];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Get configuration.
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array(
      'id' => $this->getPluginId(),
    ) + $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {

    if ($this->success) {
      return $this->pluginDefinition['success_message'];
    }

    return $this->pluginDefinition['failure_message'];
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return [
      '#theme' => 'check_help',
      '#title' => $this->pluginDefinition['title'],
      '#paragraphs' => $this->pluginDefinition['help'],
    ];
  }
}
