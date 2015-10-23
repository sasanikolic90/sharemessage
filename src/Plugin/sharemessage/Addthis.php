<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Plugin\sharemessage\Addthis.
 */

namespace Drupal\sharemessage\Plugin\sharemessage;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;
use Drupal\sharemessage\SharePluginBase;
use Drupal\sharemessage\SharePluginInterface;

/**
 * AddThis plugin.
 *
 * @SharePlugin(
 *   id = "addthis",
 *   label = @Translation("AddThis"),
 *   description = @Translation("AddThis plugin for ShareMessage module."),
 * )
 */
class Addthis extends SharePluginBase implements  SharePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build($context, $plugin_attributes) {

    $attributes = new Attribute(array('class' => array(
      'addthis_toolbox',
      'addthis_default_style',
      $this->shareMessage->getSetting('icon_style') ?: \Drupal::config('sharemessage.addthis')->get('icon_style'),
    )));

    if ($plugin_attributes) {
      $attributes['addthis:url'] = $this->shareMessage->getUrl($context);
      $attributes['addthis:title'] = $this->shareMessage->getTokenizedField($this->shareMessage->title, $context);
      $attributes['addthis:description'] = $this->shareMessage->getTokenizedField($this->shareMessage->message_long, $context);
    }

    // Add AddThis buttons.
    $build = array(
      '#theme' => 'sharemessage_addthis',
      '#attributes' => $attributes,
      '#cache' => [
        'tags' => $this->shareMessage->getCacheTags(),
      ],
      '#services' => $this->shareMessage->getSetting('services') ?: \Drupal::config('sharemessage.addthis')->get('services'),
      '#additional_services' => $this->getSetting('additional_services') ?: \Drupal::config('sharemessage.addthis')->get('additional_services'),
      '#counter' => $this->getSetting('counter') ?: \Drupal::config('sharemessage.addthis')->get('counter'),
      '#attached' => array(
        'library' => ['sharemessage/addthis'],
        'drupalSettings' => array(
          'addthis_config' => array(
            'data_track_addressbar' => TRUE,
          ),
        ),
      ),
    );
    return $build;
  }

  /**
   * Gets the default AddThis settings.
   *
   * @return array
   */
  public function getSetting($key) {
    $override = $this->shareMessage->getSetting('override_default_settings');
    if (isset($override)) {
      return $this->shareMessage->getSetting($key);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Settings fieldset.
    $form['override_default_settings'] = array(
      '#type' => 'checkbox',
      '#title' => t('Override default settings'),
      '#default_value' => $this->getSetting('override_default_settings'),
    );

    $form['services'] = array(
      '#title' => t('Visible services'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => sharemessage_get_addthis_services(),
      '#default_value' => $this->getSetting('services'),
      '#size' => 10,
      '#states' => array(
        'invisible' => array(
          ':input[name="settings[override_default_settings]"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['additional_services'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show additional services button'),
      '#default_value' => $this->getSetting('additional_services'),
      '#states' => array(
        'invisible' => array(
          ':input[name="settings[override_default_settings]"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['counter'] = array(
      '#type' => 'select',
      '#title' => t('Show AddThis counter'),
      '#empty_option' => t('No'),
      '#options' => array(
        'addthis_pill_style' => t('Pill style'),
        'addthis_bubble_style' => t('Bubble style'),
      ),
      '#default_value' => $this->getSetting('counter'),
      '#states' => array(
        'invisible' => array(
          ':input[name="settings[override_default_settings]"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['icon_style'] = array(
      '#title' => t('Default icon style'),
      '#type' => 'radios',
      '#options' => array(
        'addthis_16x16_style' => '16x16 pix',
        'addthis_32x32_style' => '32x32 pix',
      ),
      '#default_value' => $this->getSetting('icon_style'),
      '#states' => array(
        'invisible' => array(
          ':input[name="settings[override_default_settings]"]' => array('checked' => FALSE),
        ),
      ),
    );

    return $form;
  }

}
