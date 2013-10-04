<?php
/**
 * @file
 * Definition of ShareMessage entity class.
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
 *     "render" = "Drupal\sharemessage\Entity\Controller\ShareMessageRenderController",
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
   * @todo add getter
   */
  public $override_default_settings;

  /**
   * The settings of the sharemessage.
   *
   * @var string
   */
  public $settings;

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

  /**
   * Overrides \Drupal\Core\Config\Entity\ConfigEntityBase::getExportProperties();
   */
  public function getExportProperties() {
    $names = array(
      'status',
      'label',
      'id',
      'uuid',
      'langcode',
    );
    $properties = array();
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }
}
