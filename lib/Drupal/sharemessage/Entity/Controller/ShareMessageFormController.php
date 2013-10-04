<?php

/**
 * @file
 * Definition of \Drupal\sharemessage\Entity\Controller\ShareMessageFormController.
 */

namespace Drupal\sharemessage\Entity\Controller;

use Drupal\Core\Entity\EntityFormController;

/**
 * Base form controller for ShareMessage edit forms.
 */
class ShareMessageFormController extends EntityFormController {

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::form().
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    $sharemessage = $this->entity;
    $defaults = \Drupal::config('sharemessage.settings');

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#required' => TRUE,
      '#default_value' => $sharemessage->getLabel(),
      '#weight' => -3,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#title' => t('Machine Name'),
      '#machine_name' => array(
        'exists' => 'sharemessage_check_machine_name_if_exist',
        'source' => array('label'),
      ),
      '#required' => TRUE,
      '#weight' => -2,
      '#disabled' => !$sharemessage->isNew(),
      '#default_value' => $sharemessage->id(),
    );

    // Settings fieldset.
    $form['override_default_settings'] = array(
      '#type' => 'checkbox',
      '#title' => t('Override default settings'),
      '#default_value' => $sharemessage->override_default_settings,
      '#weight' => 10,
    );

    $form['settings'] = array(
      '#type' => 'fieldset',
      '#tree' => TRUE,
      '#weight' => 15,
      '#states' => array(
        'invisible' => array(
          ':input[name="override_default_settings"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['settings']['services'] = array(
      '#type' => 'select',
      '#title' => t('Visible services'),
      '#multiple' => TRUE,
      '#options' => sharemessage_get_addthis_services(),
      '#default_value' => !empty($sharemessage->settings['services']) ? $sharemessage->settings['services'] : $defaults->get('sharemessage_default_services'),
      '#size' => 10,
      '#states' => array(
        'invisible' => array(
          ':input[name="override_default_settings"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['settings']['additional_services'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show additional services button'),
      '#default_value' => isset($sharemessage->settings['additional_services']) ? $sharemessage->settings['additional_services'] : $defaults->get('additional_services'),
      '#states' => array(
        'invisible' => array(
          ':input[name="override_default_settings"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['settings']['counter'] = array(
      '#type' => 'select',
      '#title' => t('Show Addthis counter'),
      '#empty_option' => t('No'),
      '#options' => array(
        'addthis_pill_style' => t('Pill style'),
        'addthis_bubble_style' => t('Bubble style'),
      ),
      '#default_value' => isset($sharemessage->settings['counter']) ? $sharemessage->settings['counter'] : $defaults->get('counter'),
      '#states' => array(
        'invisible' => array(
          ':input[name="override_default_settings"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['settings']['icon_style'] = array(
      '#type' => 'radios',
      '#title' => t('Icon style'),
      '#options' => array(
        'addthis_16x16_style' => '16x16 pix',
        'addthis_32x32_style' => '32x32 pix',
      ),
      '#default_value' => isset($sharemessage->settings['icon_style']) ? $sharemessage->settings['icon_style'] : $defaults->get('icon_style'),
      '#states' => array(
        'invisible' => array(
          ':input[name="override_default_settings"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['block'] = array(
      '#type' => 'checkbox',
      '#title' => t('Provide a block'),
      '#default_value' => isset($sharemessage->block) ? $sharemessage->block : 0,
      '#weight' => 20,
    );

    if ($defaults->get('sharemessage_message_enforcement')) {
      $form['enforce_usage'] = array(
        '#type' => 'checkbox',
        '#title' => t('Enforce the usage of this share message on the page it points to'),
        '#description' => t('If checked, this sharemessage will be used on the page that it is referring to and override the sharemessage there.'),
        '#default_value' => isset($sharemessage->settings['enforce_usage']) ? $sharemessage->settings['enforce_usage'] : 0,
        '#weight' => 25,
      );
    }

    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['sharemessage_token_help'] = array(
        '#title' => t('Replacement patterns'),
        '#type' => 'fieldset',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#description' => t('These tokens can be used in all text fields.'),
        '#weight' => 30,
      );

      $form['sharemessage_token_help']['browser'] = array(
        '#theme' => 'token_tree',
        '#token_types' => array('node', 'sharemessage'),
      );
    }

    return $form;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    if (!$form_state['values']['override_default_settings']) {
      $form_state['values']['settings'] = array();
    }

    // Move the override field into the settings array.
    if (\Drupal::config('sharemessage_message_enforcement')) {
      $form_state['values']['settings']['enforce_usage'] = $form_state['values']['enforce_usage'];
      unset($form_state['values']['enforce_usage']);
    }

    $sharemessage = $this->entity;
    $status = $sharemessage->save();

    $uri = $sharemessage->uri();
    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('ShareMessage %label has been updated.', array('%label' => $sharemessage->label())));
      watchdog('contact', 'ShareMessage %label has been updated.', array('%label' => $sharemessage->label()), WATCHDOG_NOTICE, l(t('Edit'), $uri['path'] . '/edit'));
    }
    else {
      drupal_set_message(t('ShareMessage %label has been added.', array('%label' => $sharemessage->label())));
      watchdog('contact', 'ShareMessage %label has been added.', array('%label' => $sharemessage->label()), WATCHDOG_NOTICE, l(t('Edit'), $uri['path'] . '/edit'));
    }

    $form_state['redirect'] = 'admin/structure/services/sharemessage/list';
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect'] = 'admin/structure/services/sharemessage/' . $this->entity->id() . '/delete';
  }

}