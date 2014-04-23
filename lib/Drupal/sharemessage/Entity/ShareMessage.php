<?php
/**
 * @file
 * Definition of ShareMessage config entity class.
 */

namespace Drupal\sharemessage\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Entity class for the Share Message entity.
 *
 * @ConfigEntityType(
 *   id = "sharemessage",
 *   label = @Translation("Share Message"),
 *   controllers = {
 *     "access" = "Drupal\sharemessage\Entity\Controller\ShareMessageAccessController",
 *     "view_builder" = "Drupal\sharemessage\Entity\Controller\ShareMessageViewBuilder",
 *     "list_builder" = "Drupal\sharemessage\Entity\Controller\ShareMessageListBuilder",
 *     "form" = {
 *       "add" = "Drupal\sharemessage\Entity\Controller\ShareMessageFormController",
 *       "edit" = "Drupal\sharemessage\Entity\Controller\ShareMessageFormController",
 *       "delete" = "Drupal\sharemessage\Entity\Controller\ShareMessageDeleteForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "sharemessage.sharemessage_edit"
 *   }
 * )
 */
class ShareMessage extends ConfigEntityBase {

  /**
   * The machine name of this sharemessage.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the sharemessage.
   *
   * @var string
   */
  public $label;

  /**
   * The flag for default overrides of the sharemessage.
   *
   * @var string
   */
  public $override_default_settings;

  /**
   * The settings of the sharemessage.
   *
   * @var string
   */
  public $settings;

  /**
   * The title of the sharemessage.
   *
   * @var string
   */
  public $title;

  /**
   * The long share text of the sharemessage.
   *
   * @var string
   */
  public $message_long;

  /**
   * The short text of the sharemessage, used for twitter.
   *
   * @var string
   */
  public $message_short;

  /**
   * The image URL that will be used for sharing.
   *
   * @var string
   */
  public $image_url;

  /**
   * Specific URL that will be shared, defaults to the current page
   *
   * @var string
   */
  public $share_url;

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->get('label');
  }

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->set('label', $label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('label');
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($label) {
    $this->set('label', $label);
    return $this;
  }

}
