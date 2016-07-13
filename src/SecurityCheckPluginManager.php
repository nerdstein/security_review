<?php
/**
 * @file
 * Contains Drupal\security_review\SecurityCheckPluginManager.
 */

namespace Drupal\security_review;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;


class SecurityCheckPluginManager extends \Drupal\Core\Plugin\DefaultPluginManager {
  /**
   * Constructs a new SecurityCheckPluginManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/SecurityCheck', $namespaces, $module_handler, 'Drupal\security_review\SecurityCheckInterface', 'Drupal\security_review\Annotation\SecurityCheck');
    $this->alterInfo('security_review_check_info');
    $this->setCacheBackend($cache_backend, 'security_review_check');
  }

}