<?php

/**
 * @file
 * Definition of \Drupal\sharemessage\Entity\Controller\ShareMessageFormController.
 */

namespace Drupal\sharemessage\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

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
  public function getCancelUrl() {
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
  public function submit(array $form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message(t('ShareMessage %label has been deleted.', array('%label' => $this->entity->label())));
    \Drupal::logger('sharemessage')->notice('ShareMessage %label has been deleted.', array('%label' => $this->entity->label()));
    $form_state->setRedirect('sharemessage.sharemessage_list');
  }

}
