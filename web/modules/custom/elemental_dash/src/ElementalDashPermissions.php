<?php

namespace Drupal\elemental_dash;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\elemental_dash\Entity\ElementalDash;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides permissions for the Elemental Dash module.
 */
class ElementalDashPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Service which retrieves and maintains Drupal system configurations.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new ElementalDashPermissions instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Loop through all dashboards and build an array of permissions.
   *
   * @return array
   *   Array of dashboards to assign view permissions to.
   */
  public function dashTypePermissions() {
    $permissions = [];

    // Load all dashboards.
    $dashboards = $this->entityTypeManager->getStorage('elemental_dash')
      ->loadMultiple();
    if (($dashboards) && count($dashboards)) {
      foreach ($dashboards as $dashboard) {
        $permissions['view dashboard ' . $dashboard->id()] = [
          'title' => $this->t(
            'View Dashboard: %entity_label',
            ['%entity_label' => $dashboard->getName()]
          ),
          'restrict access' => TRUE,
        ];
      }
    }

    return $permissions;
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    $dashboard = \Drupal::routeMatch()->getParameter('elemental_dash');
    if ($dashboard instanceof ElementalDash) {
      $id = $dashboard->id();
    }

    return AccessResult::allowedIf($account->hasPermission('view dashboard ' . $id));
  }

}
