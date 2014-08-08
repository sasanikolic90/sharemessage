<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Plugin\Block\ShareMessageBlock.
 */

namespace Drupal\sharemessage\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ShareMessage' block with the addthis widgets.
 *
 * @Block(
 *   id = "sharemessage_block",
 *   admin_label = @Translation("Share message"),
 *   category = @Translation("Sharing"),
 * )
 */
class ShareMessageBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage controller for share messages.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
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
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityManagerInterface $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storageController = $entity_manager->getStorage('sharemessage');
    $this->viewBuilder = $entity_manager->getViewBuilder('sharemessage');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')
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
  public function blockAccess(AccountInterface $account) {
    // Only grant access to users with the 'access news feeds' permission.
    return $account->hasPermission('view sharemessages');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
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
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
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

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array(
      'entity' => array('sharemessage.sharemessage.' . $this->configuration['sharemessage']),
    );
  }

}
