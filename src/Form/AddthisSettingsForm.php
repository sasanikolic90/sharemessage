<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Form\AddthisSettingsForm.
 */

namespace Drupal\sharemessage\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form that configures ShareMessage settings.
 */
class AddthisSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sharemessage.addthis',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'sharemessage_addthis_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {

    $config = $this->config('sharemessage.addthis');

    // Global setting.
    $form['message_enforcement'] = array(
      '#type' => 'checkbox',
      '#title' => t('Global: Allow to enforce share messages'),
      '#description' => t('This will enforce loading of a sharemessage if the ?smid argument is present in an URL. If something else on your site is using this argument, disable this this option.'),
      '#default_value' => $config->get('message_enforcement'),
    );

    // AddThis specific settings.
    $form['addthis_profile_id'] = array(
      '#title' => t('AddThis Profile ID'),
      '#type' => 'textfield',
      '#default_value' => $config->get('addthis_profile_id'),
    );

    $form['default_services'] = array(
      '#title' => t('Default visible services'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => sharemessage_get_addthis_services(),
      '#default_value' => $config->get('services'),
      '#size' => 10,
    );

    $form['default_additional_services'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show additional services button'),
      '#default_value' => $config->get('additional_services'),
    );

    $form['default_counter'] = array(
      '#type' => 'select',
      '#title' => t('Show Addthis counter'),
      '#empty_option' => t('No'),
      '#options' => array(
        'addthis_pill_style' => t('Pill style'),
        'addthis_bubble_style' => t('Bubble style'),
      ),
      '#default_value' => $config->get('counter'),
    );

    $form['default_icon_style'] = array(
      '#title' => t('Default icon style'),
      '#type' => 'radios',
      '#options' => array(
        'addthis_16x16_style' => '16x16 pix',
        'addthis_32x32_style' => '32x32 pix',
      ),
      '#default_value' => $config->get('icon_style'),
    );

    $form['local_services_definition'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use local service definitions file'),
      '#description' => t('Check this if you are behind a firewall and the module cannot access the services definition at http://cache.addthiscdn.com/services/v1/sharing.en.json.'),
      '#default_value' => $config->get('local_services_definition'),
    );

    $form['shared_video_width'] = array(
      '#title' => t('Video height'),
      '#description' => t('The width of the player when sharing a video.'),
      '#type' => 'textfield',
      '#default_value' => $config->get('shared_video_width', 360),
    );

    $form['shared_video_height'] = array(
      '#title' => t('Video height'),
      '#description' => t('The height of the player when sharing a video.'),
      '#type' => 'textfield',
      '#default_value' => $config->get('shared_video_height', 270),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // If the profile id changes then we need to rebuild the library cache.
    Cache::invalidateTags(['library_info']);

    $this->config('sharemessage.addthis')
      ->set('message_enforcement', $form_state->getValue('message_enforcement'))
      ->set('addthis_profile_id', $form_state->getValue('addthis_profile_id'))
      ->set('services', $form_state->getValue('default_services'))
      ->set('additional_services', $form_state->getValue('default_additional_services'))
      ->set('counter', $form_state->getValue('default_counter'))
      ->set('icon_style', $form_state->getValue('default_icon_style'))
      ->set('local_services_definition', $form_state->getValue('local_services_definition'))
      ->set('shared_video_width', $form_state->getValue('shared_video_width'))
      ->set('shared_video_height', $form_state->getValue('shared_video_height'))
      ->save();
  }

}
