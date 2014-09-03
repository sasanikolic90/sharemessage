<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Entity\Controller\ShareMessageAccessController.
 */

namespace Drupal\sharemessage\Entity\Handler;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
/**
 * Defines an access controller for the ShareMessage entity.
 *
 * @see \Drupal\contact\Entity\ShareMessage.
 */
class ShareMessageAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    if ($operation == 'delete' || $operation == 'update') {
      // Do not allow delete 'personal' category used for personal contact form.
      return $account->hasPermission('administer sharemessages');
    }
    else {
      return $account->hasPermission('administer sharemessages');
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return $account->hasPermission('administer sharemessages');
  }

}
