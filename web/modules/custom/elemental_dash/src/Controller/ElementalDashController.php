<?php

namespace Drupal\elemental_dash\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Subscriber for force the system to rebuild the theme registry.
 */
class ElementalDashController extends ControllerBase {

  /**
   * The devel config.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * Constructs a ThemeInfoRebuildSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory.
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   */
  public function __construct(ConfigFactoryInterface $config, AccountProxyInterface $account) {
    $this->config = $config->get('elemental_dash.settings');
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('current_user')
    );
  }

  /**
   * Redirect the user to their default dashboard.
   *
   * @return string
   *   The entity id of the user dashboard.
   */
  public function dashboardSelector() {

    $userRoles = $this->account->getRoles();
    if (in_array('administrator', $userRoles)) {
      $role = 'administrator';
    }
    else {
      $role = array_pop($userRoles);
    }

    $config = \Drupal::config('elemental_dash.settings');
    $dashboard = $config->get($role);

    return $this->redirect('entity.elemental_dash.canonical', ['elemental_dash' => $dashboard]);
  }

}
