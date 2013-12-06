<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Plugin\Block\ShareMessageBlock.
 */

namespace Drupal\sharemessage\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ShareMessage' block with the addthis widgets.
 *
 * @Block(
 *   id = "sharemessage_block",
 *   admin_label = @Translation("ShareMessage")
 * )
 */
class ShareMessageBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage controller for feeds.
   *
   * @var \Drupal\Core\Entity\EntityStorageControllerInterface
   */
  protected $storageController;

  /**
   * The entity view builder for sharemessage.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $viewBuilder;

  /**
   * Constructs an ShareMessageBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageControllerInterface $storage_controller
   *   The entity storage controller for feeds.
   * @param \Drupal\Core\Entity\EntityViewBuilderInterface $view_builder
   *   The entity view builder for sharemessage.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageControllerInterface $storage_controller, Connection $connection) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storageController = $storage_controller;
    $this->viewBuilder = \Drupal::entityManager()->getViewBuilder('sharemessage');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')->getStorageController('sharemessage'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // By default, the block will contain 10 feed items.
    return array(
      'sharemessage' => NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    // Only grant access to users with the 'access news feeds' permission.
    return $account->hasPermission('view sharemessages');
  }

  /**
   * Overrides \Drupal\block\BlockBase::blockForm().
   */
  public function blockForm($form, &$form_state) {
    $sharemessages = $this->storageController->loadMultiple();
    $options = array();
    foreach ($sharemessages as $sharemessage) {
      $options[$sharemessage->id] = $sharemessage->getLabel();
    }
    $form['sharemessage'] = array(
      '#type' => 'select',
      '#title' => t('Select the sharemessage that should be displayed'),
      '#default_value' => $this->configuration['sharemessage'],
      '#options' => $options,
    );
    return $form;
  }

  /**
   * Overrides \Drupal\block\BlockBase::blockSubmit().
   */
  public function blockSubmit($form, &$form_state) {
    $this->configuration['sharemessage'] = $form_state['values']['sharemessage'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Only display the block if there are items to show.
    if ($sharemessage = $this->storageController->load($this->configuration['sharemessage'])) {
      return $this->viewBuilder->view($sharemessage);
    }
  }

}
