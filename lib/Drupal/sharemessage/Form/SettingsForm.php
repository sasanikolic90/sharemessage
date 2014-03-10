<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Form\SettingsForm.
 */

namespace Drupal\sharemessage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\system\SystemConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form that configures ShareMessage settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'sharemessage_addthis_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, Request $request = NULL) {

    $config = $this->configFactory->get('sharemessage.settings');

    $form['sharemessage_addthis_profile_id'] = array(
      '#title' => t('AddThis Profile ID'),
      '#type' => 'textfield',
      '#default_value' => $config->get('sharemessage_addthis_profile_id'),
    );

    $form['sharemessage_default_services'] = array(
      '#title' => t('Default visible services'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => sharemessage_get_addthis_services(),
      '#default_value' => $config->get('sharemessage_default_services'),
      '#size' => 10,
    );

    $form['sharemessage_default_additional_services'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show additional services button'),
      '#default_value' => $config->get('sharemessage_default_additional_services'),
    );

    $form['sharemessage_default_counter'] = array(
      '#type' => 'select',
      '#title' => t('Show Addthis counter'),
      '#empty_option' => t('No'),
      '#options' => array(
        'addthis_pill_style' => t('Pill style'),
        'addthis_bubble_style' => t('Bubble style'),
      ),
      '#default_value' => $config->get('sharemessage_default_counter'),
    );

    $form['sharemessage_default_icon_style'] = array(
      '#title' => t('Default icon style'),
      '#type' => 'radios',
      '#options' => array(
        'addthis_16x16_style' => '16x16 pix',
        'addthis_32x32_style' => '32x32 pix',
      ),
      '#default_value' => $config->get('sharemessage_default_icon_style'),
    );

    $form['sharemessage_message_enforcement'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow to enforce share messages'),
      '#description' => t('This will enforce loading of a sharemessage if the ?smid argument is present in an URL. If something else on your site is using this argument, disable this this option.'),
      '#default_value' => $config->get('sharemessage_message_enforcement'),
    );

    $form['sharemessage_local_services_definition'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use local service definitions file'),
      '#description' => t('Check this if you are behind a firewall and the module cannot access the services definition at http://cache.addthiscdn.com/services/v1/sharing.en.json.'),
      '#default_value' => $config->get('sharemessage_local_services_definition'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->configFactory->get('sharemessage.settings')
      ->set('addthis_profile_id', $form_state['values']['sharemessage_addthis_profile_id'])
      ->set('services', $form_state['values']['sharemessage_default_services'])
      ->set('additional_services', $form_state['values']['sharemessage_default_additional_services'])
      ->set('counter', $form_state['values']['sharemessage_default_counter'])
      ->set('icon_style', $form_state['values']['sharemessage_default_icon_style'])
      ->set('message_enforcement', $form_state['values']['sharemessage_message_enforcement'])
      ->set('local_services_definition', $form_state['values']['sharemessage_local_services_definition'])
      ->save();

    drupal_set_message(t('ShareMessage settings have been updated.'));
  }
}
