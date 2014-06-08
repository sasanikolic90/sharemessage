<?php

/**
 * @file
 * Definition of \Drupal\sharemessage\Entity\Controller\ShareMessageFormController.
 */

namespace Drupal\sharemessage\Entity\Controller;

use Drupal\Core\Entity\EntityConfirmFormBase;

/**
 * Builds the form to delete a ShareMessage config entity.
 */
class ShareMessageDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'sharemessage.sharemessage_list',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    drupal_set_message(t('ShareMessage %label has been deleted.', array('%label' => $this->entity->label())));
    watchdog('contact', 'ShareMessage %label has been deleted.', array('%label' => $this->entity->label()), WATCHDOG_NOTICE);
    $form_state['redirect'] = 'admin/config/services/sharemessage/list';
  }

}
