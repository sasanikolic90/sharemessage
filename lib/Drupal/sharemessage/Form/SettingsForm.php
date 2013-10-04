<?php

/**
 * @file
 * Contains \Drupal\sharemessage\Form\SettingsForm.
 */

namespace Drupal\sharemessage\Form;

use Drupal\Component\Utility\MapArray;
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
    $this->configFactory->get('devel.settings')
      ->set('query_display', $form_state['values']['query_display'])
      ->set('query_sort', $form_state['values']['query_sort'])
      ->set('execution', $form_state['values']['execution'])
      ->set('xhprof_enabled', $form_state['values']['xhprof_enabled'])
      ->set('xhprof_directory', $form_state['values']['xhprof_directory'])
      ->set('xhprof_url', $form_state['values']['xhprof_url'])
      ->set('api_url', $form_state['values']['api_url'])
      ->set('timer', $form_state['values']['timer'])
      ->set('memory', $form_state['values']['memory'])
      ->set('redirect_page', $form_state['values']['redirect_page'])
      ->set('page_alter', $form_state['values']['page_alter'])
      ->set('raw_names', $form_state['values']['raw_names'])
      ->set('error_handlers', $form_state['values']['error_handlers'])
      ->set('krumo_skin', $form_state['values']['krumo_skin'])
      ->set('rebuild_theme_registry', $form_state['values']['rebuild_theme_registry'])
      ->set('use_uncompressed_jquery', $form_state['values']['use_uncompressed_jquery'])
      ->save();
  }


  /**
   * @param string $severity
   */
  protected function demonstrateErrorHandlers($severity) {
    switch ($severity) {
      case 'warning':
        $undefined = $undefined;
        1/0;
        break;
      case 'error':
        $undefined = $undefined;
        1/0;
        devel_undefined_function();
        break;
    }
  }

}
