<?php
/**
 * @file
 * Provides Drupal\security_review\SecurityCheckInterface.
 */
namespace Drupal\security_review;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;

interface SecurityCheckInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Executes the check and returns the results.
   */
  public function run();

  /**
   * Returns a translated string for the check title.
   * @return string
   */
  public function getTitle();

  /**
   * Returns a translated description for the check description.
   * @return string
   */
  public function getDescription();

  /**
   * Returns a translated namespace for the check.
   * @return string
   */
  public function getNamespace();

  /**
   * Returns a set of controls associated with the check.
   * @return array
   */
  public function getControls();

  /**
   * Returns the pass / fail message.
   * @return string
   */
  public function getStatus();

  /**
   * Returns render array of help for the check.
   * @return array
   */
  public function getHelp();

  /**
   * Returns render array of details for the results.
   * @return array
   */
  public function getDetails();

  /**
   * Returns string of details for the results.
   * @return string
   */
  public function getDetailsAsPlainText();

  /**
   * Sets the default configuration.
   */
  public function defaultConfiguration();

  /**
   * Returns the configuration form.
   * @return array
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state);

  /**
   * Updates the form state after validating the configuration form.
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state);

  /**
   * Handles the processing of the config form.
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state);

  /**
   * Returns the configuration of the check.
   * @return array
   */
  public function getConfiguration();

  /**
   * Updates the configuration object.
   */
  public function setConfiguration(array $configuration);

}
