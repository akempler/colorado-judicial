<?php

namespace Drupal\elemental_dash;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the ElementalDash entity.
 *
 * @see \Drupal\elemental_dash\Entity\ElementalDash.
 */
class ElementalDashAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'access dashboard');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit dashboard');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete dashboard');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add dashboard entity');
  }

}
