<?php

namespace Drupal\elemental_dash;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining an ElementalDash entity.
 *
 * @ingroup elemental_dash
 */
interface ElementalDashInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the Dashboard name.
   *
   * @return string
   *   Name of the Dashboard.
   */
  public function getName();

  /**
   * Sets the Dashboard name.
   *
   * @param string $name
   *   The Dashboard name.
   */
  public function setName($name);

  /**
   * Gets the Dashboard creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Dashboard.
   */
  public function getCreatedTime();

}
