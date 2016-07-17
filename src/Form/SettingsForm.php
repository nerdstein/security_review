<?php

/**
 * @file
 * Contains \Drupal\security_review\Form\SettingsForm.
 */

namespace Drupal\security_review\Form;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\security_review\Checklist;
use Drupal\security_review\Security;
use Drupal\security_review\SecurityCheckPluginManager;
use Drupal\security_review\SecurityReview;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings page for Security Review.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The security_review.checklist service.
   *
   * @var \Drupal\security_review\Checklist
   */
  protected $checklist;

  /**
   * The security_review.security service.
   *
   * @var \Drupal\security_review\Security
   */
  protected $security;

  /**
   * The security_review service.
   *
   * @var \Drupal\security_review\SecurityReview
   */
  protected $securityReview;

  /**
   * The security checks plugin manager.
   *
   * @var \Drupal\security_review\SecurityCheckPluginManager
   */
  protected $checkPluginManager;

  /**
   * Constructs a SettingsForm.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\security_review\Checklist $checklist
   *   The security_review.checklist service.
   * @param \Drupal\security_review\Security $security
   *   The security_review.security service.
   * @param \Drupal\security_review\SecurityReview $security_review
   *   The security_review service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, Checklist $checklist, Security $security, SecurityReview $security_review, SecurityCheckPluginManager $checkPluginManager) {
    parent::__construct($config_factory);
    $this->checklist = $checklist;
    $this->security = $security;
    $this->securityReview = $security_review;
    $this->checkPluginManager = $checkPluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('security_review.checklist'),
      $container->get('security_review.security'),
      $container->get('security_review'),
      $container->get('plugin.manager.security_review.security_check')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'security-review-settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Load security checks from plugin manager.
    $checks = $this->checkPluginManager->getDefinitions();

    $form['check_categories'] = [
      '#type' => 'vertical_tabs',
    ];

    $categories = [];
    $check_count = 0;
    foreach ($checks as $check) {
      $check_count++;
      /** @var $instance \Drupal\security_review\SecurityCheckInterface */
      $instance = $this->checkPluginManager->createInstance($check['id'], []);
      $category_found = array_search($instance->getNamespace(), $categories);
      $category_count = count($categories);
      if ($category_found === FALSE) {
        // Render the category as a tab.
        $form['check_category_' . $category_count] = [
          '#type' => 'details',
          '#title' => $instance->getNamespace(),
          '#group' => 'check_categories',
        ];
        $categories[] = $instance->getNamespace();
      } else {
        $category_count = $category_found;
      }

      // Render the check within the tab.
      $id = $check['id'];
      $form['check_category_' . $category_count][$id]['enabled'] = [
        '#type' => 'checkbox',
        '#title' => $instance->getTitle(),
      ];

      $form['check_category_' . $category_count][$id]['instance'] = [
        '#type' => 'value',
        '#title' => $instance,
      ];

      $id_value = 'edit-enabled';

      if ($check_count > 1) {
        $id_value .= '--' . $check_count;
      }

      $form['check_category_' . $category_count][$id]['container'] = array(
        '#type' => 'container',
        '#attributes' => array(
          'class' => 'accommodation',
        ),
        '#states' => array(
          'invisible' => array(
            'input[id="' . $id_value . '"]' => array('checked' => FALSE),
          ),
        ),
        'settings' => $instance->buildConfigurationForm($form, $form_state),
      );

      // TODO - render settings inside of the container.
    }

    /**
     * OLD STUFF
     */
    /*
    // Get the list of checks.
    $checks = $this->checklist->getChecks();

    // Get the user roles.
    $roles = user_roles();
    $options = [];
    foreach ($roles as $rid => $role) {
      $options[$rid] = SafeMarkup::checkPlain($role->label());
    }

    // Notify the user if anonymous users can create accounts.
    $message = '';
    if (in_array(AccountInterface::AUTHENTICATED_ROLE, $this->security->defaultUntrustedRoles())) {
      $message = $this->t('You have allowed anonymous users to create accounts without approval so the authenticated role defaults to untrusted.');
    }

    // Show the untrusted roles form element.
    $form['untrusted_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Untrusted roles'),
      '#description' => $this->t(
        'Define which roles are for less trusted users. The anonymous role defaults to untrusted. @message Most Security Review checks look for resources usable by untrusted roles.',
        ['@message' => $message]
      ),
      '#options' => $options,
      '#default_value' => $this->security->untrustedRoles(),
    ];

    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced'),
      '#open' => TRUE,
    ];

    // Show the logging setting.
    $form['advanced']['logging'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Log checklist results and skips'),
      '#description' => $this->t('The result of each check and skip can be logged to watchdog for tracking.'),
      '#default_value' => $this->securityReview->isLogging(),
    ];

    // Skipped checks.
    $values = [];
    $options = [];
    foreach ($checks as $check) {
      // Determine if check is being skipped.
      if ($check->isSkipped()) {
        $values[] = $check->id();
        $label = $this->t(
          '@name <em>skipped by UID @uid on @date</em>',
          [
            '@name' => $check->getTitle(),
            '@uid' => $check->skippedBy()->id(),
            '@date' => format_date($check->skippedOn()),
          ]
        );
      }
      else {
        $label = $check->getTitle();
      }
      $options[$check->id()] = $label;
    }
    $form['advanced']['skip'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checks to skip'),
      '#description' => $this->t('Skip running certain checks. This can also be set on the <em>Run & review</em> page. It is recommended that you do not skip any checks unless you know the result is wrong or the process times out while running.'),
      '#options' => $options,
      '#default_value' => $values,
    ];

    // Iterate through checklist and get check-specific setting pages.
    foreach ($checks as $check) {
      // Get the check's setting form.
      $check_form = $check->settings()->buildForm();

      // If not empty, add it to the form.
      if (!empty($check_form)) {
        // If this is the first non-empty setting page initialize the 'details'
        if (!isset($form['advanced']['check_specific'])) {
          $form['advanced']['check_specific'] = [
            '#type' => 'details',
            '#title' => $this->t('Check-specific settings'),
            '#open' => FALSE,
            '#tree' => TRUE,
          ];
        }

        // Add the form.
        $sub_form = &$form['advanced']['check_specific'][$check->id()];

        $title = $check->getTitle();
        // If it's an external check, show its namespace.
        if ($check->getMachineNamespace() != 'security_review') {
          $title .= $this->t('%namespace', [
            '%namespace' => $check->getNamespace(),
          ]);
        }
        $sub_form = [
          '#type' => 'details',
          '#title' => $title,
          '#open' => TRUE,
          '#tree' => TRUE,
          'form' => $check_form,
        ];
      }
    }

    */

    // Return the finished form.
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Run validation for check-specific settings.
    if (isset($form['advanced']['check_specific'])) {
      $check_specific_values = $form_state->getValue('check_specific');
      foreach ($this->checklist->getChecks() as $check) {
        $check_form = &$form['advanced']['check_specific'][$check->id()];
        if (isset($check_form)) {
          $check->settings()
            ->validateForm($check_form, $check_specific_values[$check->id()]);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Frequently used configuration items.
    $check_settings = $this->config('security_review.checks');

    // Save that the module has been configured.
    $this->securityReview->setConfigured(TRUE);

    // Save the new untrusted roles.
    $untrusted_roles = array_keys(array_filter($form_state->getValue('untrusted_roles')));
    $this->securityReview->setUntrustedRoles($untrusted_roles);

    // Save the new logging setting.
    $logging = $form_state->getValue('logging') == 1;
    $this->securityReview->setLogging($logging);

    // Skip selected checks.
    $skipped = array_keys(array_filter($form_state->getValue('skip')));
    foreach ($this->checklist->getChecks() as $check) {
      if (in_array($check->id(), $skipped)) {
        $check->skip();
      }
      else {
        $check->enable();
      }
    }

    // Save the check-specific settings.
    if (isset($form['advanced']['check_specific'])) {
      $check_specific_values = $form_state->getValue('check_specific');
      foreach ($check_specific_values as $id => $values) {
        // Get corresponding Check.
        $check = $this->checklist->getCheckById($id);

        // Submit parameters.
        $check_form = &$form['advanced']['check_specific'][$id]['form'];
        $check_form_values = $check_specific_values[$id]['form'];

        // Submit.
        $check->settings()->submitForm($check_form, $check_form_values);
      }
    }

    // Commit the settings.
    $check_settings->save();

    // Finish submitting the form.
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['security_review.checks'];
  }

}
