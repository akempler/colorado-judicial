<?php

namespace Drupal\elemental_dash\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\elemental_dash\Entity\ElementalDash;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Link;

/**
 * Provides a 'My Dashboards' Block.
 *
 * @Block(
 *   id = "my_dashboards_block",
 *   admin_label = @Translation("My Dashboards block"),
 *   category = @Translation("Dashboard"),
 * )
 */
class MyDashboardsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Current User object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new SwitchUserBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $user = \Drupal::currentUser();
    $user_roles = $user->getRoles();

    $output = '';
    $dashboards = $this->entityTypeManager->getStorage('elemental_dash')
      ->loadMultiple();
    if (($dashboards) && count($dashboards)) {

      $current_dashboard = \Drupal::routeMatch()->getParameter('elemental_dash');
      if ($current_dashboard instanceof ElementalDash) {
        $current_id = $current_dashboard->id();
      }

      $output .= '<nav class="is-horizontal position-container is-horizontal-enabled">';
      $output .= '<ul class="tabs secondary clearfix">';
      foreach ($dashboards as $dashboard) {
        if ((\Drupal::currentUser()->hasPermission('view dashboard ' . $dashboard->id())) ||
          (in_array('administrator', $user_roles))) {

          $options = [
            'absolute' => FALSE,
            'attributes' => ['class' => 'this-class'],
          ];
          $dashboard_title = Markup::create('<span>' . $dashboard->getName() . '</span>');
          $link_object = Link::createFromRoute(t('@title', ['@title' => $dashboard_title]), 'entity.elemental_dash.canonical', ['elemental_dash' => $dashboard->id()], $options);

          $class = 'tabs__tab';
          if ($dashboard->id() == $current_id) {
            $class .= ' is-active';
          }
          $output .= '<li class="' . $class . '">' . $link_object->toString() . '</li>';
        }
      }
      $output .= '</ul>';
      $output .= '</nav>';
    }

    return [
      '#markup' => $output,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();
    $cache_tags[] = 'config:elemental_dash.dashes.' . $this->getDerivativeId();
    return $cache_tags;
  }

}
