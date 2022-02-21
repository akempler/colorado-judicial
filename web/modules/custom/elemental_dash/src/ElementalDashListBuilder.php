<?php

namespace Drupal\elemental_dash;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for ElementalDash entity.
 *
 * @ingroup elemental_dash
 */
class ElementalDashListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('You can manage the Dashboard fields on the <a href="@adminlink">Dashboards admin page</a>.', [
        '@adminlink' => \Drupal::urlGenerator()
          ->generateFromRoute('entity.elemental_dash.admin_form'),
      ]),
    ];

    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the dashboard list.
   */
  public function buildHeader() {
    $header['id'] = $this->t('Dashboard ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\elemental_dash\Entity\ElementalDash $entity */
    $row['id'] = $entity->id();
    $row['name'] = $entity->toLink()->toString();
    return $row + parent::buildRow($entity);
  }

}
