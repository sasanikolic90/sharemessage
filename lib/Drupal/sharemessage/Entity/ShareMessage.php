<?php
/**
 * @file
 * Definition of ShareMessage config entity class.
 */

namespace Drupal\sharemessage\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Entity class for the ShareMessage entity.
 *
 * @EntityType(
 *   id = "sharemessage",
 *   label = @Translation("ShareMessage"),
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController",
 *     "access" = "Drupal\sharemessage\Entity\Controller\ShareMessageAccessController",
 *     "view_builder" = "Drupal\sharemessage\Entity\Controller\ShareMessageViewBuilder",
 *     "list" = "Drupal\sharemessage\Entity\Controller\ShareMessageListController",
 *     "form" = {
 *       "add" = "Drupal\sharemessage\Entity\Controller\ShareMessageFormController",
 *       "edit" = "Drupal\sharemessage\Entity\Controller\ShareMessageFormController"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status"
 *   },
 *   config_prefix = "sharemessage.sharemessage",
 *   links = {
 *     "edit-form" = "admin/structure/services/sharemessage/manage/{sharemessage}"
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
  protected $label;

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
