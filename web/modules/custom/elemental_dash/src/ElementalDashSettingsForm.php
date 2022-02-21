<?php

namespace Drupal\elemental_dash;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\RoleInterface;

/**
 * Configure dashboard settings for this site.
 *
 * @internal
 */
class ElementalDashSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a \Drupal\elemental_dash\ElementalDashSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'elemental_dash_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'elemental_dash.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->configFactory->get('elemental_dash.settings');

    $options = [];
    $dashboards = $this->entityTypeManager->getStorage('elemental_dash')->loadMultiple();
    if (($dashboards) && count($dashboards)) {
      foreach ($dashboards as $dashboard) {
        $options[$dashboard->id()] = $dashboard->getName();
      }
    }

    $roles = $this->getAvailableUserRoleNames();

    $form['role_dashboard'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Dashboard Roles'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
    ];

    $form['role_dashboard']['description'] = [
      '#type' => 'markup',
      '#markup' => $this->t('For each role, select a dashboard to redirect to upon logging in. If none is selected, the role will not be redirected.'),
    ];

    foreach ($roles as $role_machine_name => $role_name) {
      $form['role_dashboard'][$role_machine_name] = [
        '#type' => 'select',
        '#empty_value' => '',
        '#title' => $this->t('@role_name', ['@role_name' => $role_name]),
        '#default_value' => [$config->get($role_machine_name)],
        '#options' => $options,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->configFactory()->getEditable('elemental_dash.settings');

    $role_dashes = $form_state->getValue('role_dashboard');
    if (($role_dashes) && count($role_dashes)) {
      foreach ($role_dashes as $role_machine_name => $dashboard_id) {
        $config->set($role_machine_name, $dashboard_id);
      }
    }

    $config->save();
    $this->messenger()->addMessage($this->t('The configuration options have been saved.'));
  }

  /**
   * Return available user role names keyed by role id.
   *
   * @return array
   *   Available user role names.
   */
  protected function getAvailableUserRoleNames() {
    $names = [];

    $roles = $this->entityTypeManager->getStorage('user_role')->loadMultiple();

    if (isset($roles[RoleInterface::ANONYMOUS_ID])) {
      unset($roles[RoleInterface::ANONYMOUS_ID]);
    }

    foreach ($roles as $role) {
      if ($role instanceof RoleInterface) {
        $names[$role->id()] = $role->label();
      }
    }

    return $names;
  }

}
