<?php

/**
 * @file
 * Contains \Drupal\security_review\Plugin\SecurityCheck\AdminPermissions.
 */

namespace Drupal\security_review\Plugin\SecurityCheck;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\security_review\SecurityCheckBase;
use Drupal\user\Entity\Role;

/**
 * Checks whether untrusted roles have restricted permissions.
 *
 * @SecurityCheck(
 *   id = "admin_permissions",
 *   title = @Translation("Administrative Permissions Check"),
 *   description = @Translation("Checks whether untrusted roles have restricted permissions."),
 *   namespace = @Translation("Security Review"),
 *   success_message = @Translation("Untrusted roles do not have administrative or trusted Drupal permissions."),
 *   failure_message = @Translation("Untrusted roles have been granted administrative or trusted Drupal permissions."),
 *   controls = {
 *     @Translation("SC1"),
 *     @Translation("SC2"),
 *     @Translation("SC3"),
 *   },
 *   help = {
 *    @Translation("Drupal's permission system is extensive and allows for varying degrees of control. Certain permissions would allow a user total control, or the ability to escalate their control, over your site and should only be granted to trusted users."),
 *   }
 * )
 */
class AdminPermissions extends SecurityCheckBase {

  /**
   * {@inheritdoc}
   */
  public function run() {
    // Get every permission.
    $all_permissions = \Drupal::service('security_review.security')->permissions(TRUE);
    $all_permission_strings = array_keys($all_permissions);

    // Get permissions for untrusted roles.
    $untrusted_permissions = \Drupal::service('security_review.security')->untrustedPermissions(TRUE);
    foreach ($untrusted_permissions as $rid => $permissions) {
      $intersect = array_intersect($all_permission_strings, $permissions);
      foreach ($intersect as $permission) {
        if (isset($all_permissions[$permission]['restrict access'])) {
          $this->findings[$rid][] = $permission;
        }
      }
    }

    if (!empty($findings)) {
      $this->success = FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // TODO - Put any default sensitive perms in here.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Get configuration.
    $config = $this->getConfiguration();

    // TODO - Build the "permissions" in here, remove from service.

    return [
      'test' => [
        '#type' => 'value',
        '#markup' => 'test 1, 2, wuttt!'
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDetails() {
    $output = [];

    foreach ($this->findings as $rid => $permissions) {
      $role = Role::load($rid);
      /** @var Role $role */
      $paragraphs = [];
      $paragraphs[] = $this->t(
        "@role has the following restricted permissions:",
        [
          '@role' => Link::fromTextAndUrl(
            $role->label(),
            Url::fromRoute(
              'entity.user_role.edit_permissions_form',
              ['user_role' => $role->id()]
            )
          ),
        ]
      );

      $output[] = [
        '#theme' => 'check_evaluation',
        '#paragraphs' => $paragraphs,
        '#items' => $permissions,
      ];
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getDetailsAsPlainText() {
    $output = '';

    foreach ($this->findings as $rid => $permissions) {
      $role = Role::load($rid);
      /** @var Role $role */

      $output .= $this->t(
        '@role has @permissions',
        [
          '@role' => $role->label(),
          '@permissions' => implode(', ', $permissions),
        ]
      );
      $output .= "\n";
    }

    return $output;
  }

}
