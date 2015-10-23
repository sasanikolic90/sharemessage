<?php

/**
 * @file
 * Contains \Drupal\sharemessage\SharePluginInterface.
 */

namespace Drupal\sharemessage;

use Drupal\Component\Plugin\ConfigurablePluginInterface;

/**
 * Interface for source plugin controllers.
 *
 * @ingroup sharemessage
 */
interface SharePluginInterface extends ConfigurablePluginInterface {

  /**
   * Creates the AddThis toolbar.
   *
   * @param array $context
   *   The form structure.
   * @param $plugin_attributes
   *   Custom plugin attributes.
   *
   * @return array $build
   *   Returns the modified configuration form structure.
   */
  public function build($context, $plugin_attributes);

  /**
   * Gets the setting for $key with overrides if applicable.
   *
   * @var string $key
   *
   * @return mixed
   */
  public function getSetting($key);
}
