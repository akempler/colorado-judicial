<?php

namespace Drupal\elemental_dash\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the ElementalDash entity edit forms.
 *
 * @ingroup elemental_dash
 */
class ElementalDashForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\elemental_dash\Entity\ElementalDash $entity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    if ($status == SAVED_UPDATED) {
      $this->messenger()->addMessage($this->t('The dashboard has been updated.'));
    }
    else {
      $this->messenger()->addMessage($this->t('The dashboard has been added.'));
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $status;
  }

}
