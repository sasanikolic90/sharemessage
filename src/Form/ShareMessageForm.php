<?php

/**
 * @file
 * Definition of \Drupal\sharemessage\Entity\Controller\ShareMessageFormController.
 */

namespace Drupal\sharemessage\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base form controller for ShareMessage edit forms.
 */
class ShareMessageForm extends EntityForm {

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::form().
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $sharemessage = $this->entity;
    $defaults = \Drupal::config('sharemessage.settings');

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#required' => TRUE,
      '#default_value' => $sharemessage->label(),
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

    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => $sharemessage->title,
      '#description' => t('Used as title in the share message, where applicable: Facebook, E-Mail subject, ...'),
      '#weight' => 5,
    );

    $form['message_long'] = array(
      '#type' => 'textarea',
      '#title' => t('Long Description'),
      '#default_value' => $sharemessage->message_long,
      '#description' => t('Used as long description for the share message, where applicable: Facebook, Email body, ...'),
      '#weight' => 10,
    );

    $form['message_short'] = array(
      '#type' => 'textfield',
      '#title' => t('Short Description'),
      '#default_value' => $sharemessage->message_short,
      '#description' => t('Used as short description for twitter messages.'),
      '#weight' => 15,
    );

    $form['video_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Video URL'),
      '#default_value' => $sharemessage->video_url,
      '#description' => t('The video URL that will be used for sharing.'),
      '#weight' => 18,
    );

    $form['image_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Image URL'),
      '#default_value' => $sharemessage->image_url,
      '#description' => t('The image URL that will be used for sharing. If a video URL is set, the image is used as a thumbnail for the video.'),
      '#weight' => 20,
    );

    // @todo: Convert this to a file upload/selection widget.
    $form['fallback_image'] = array(
      '#type' => 'textfield',
      '#title' => t('Fallback image (File UUID)'),
      '#default_value' => $sharemessage->fallback_image,
      '#description' => t('Specify a static fallback image that is used if the Image URL is empty (For example, when tokens are used and the specified image field is empty).'),
      '#weight' => 23,
    );

    $form['share_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Shared URL'),
      '#default_value' => $sharemessage->share_url,
      '#description' => t('Specific URL that will be shared, defaults to the current page.'),
      '#weight' => 25,
    );

    // Settings fieldset.
    $form['override_default_settings'] = array(
      '#type' => 'checkbox',
      '#title' => t('Override default settings'),
      '#default_value' => $sharemessage->override_default_settings,
      '#weight' => 30,
    );

    $form['settings'] = array(
      '#type' => 'fieldset',
      '#tree' => TRUE,
      '#weight' => 35,
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
      '#default_value' => !empty($sharemessage->settings['services']) ? $sharemessage->settings['services'] : $defaults->get('services'),
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

    if ($defaults->get('message_enforcement')) {
      $form['enforce_usage'] = array(
        '#type' => 'checkbox',
        '#title' => t('Enforce the usage of this share message on the page it points to'),
        '#description' => t('If checked, this sharemessage will be used on the page that it is referring to and override the sharemessage there.'),
        '#default_value' => isset($sharemessage->settings['enforce_usage']) ? $sharemessage->settings['enforce_usage'] : 0,
        '#weight' => 40,
      );
    }

    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['sharemessage_token_help'] = array(
        '#title' => t('Replacement patterns'),
        '#type' => 'fieldset',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#description' => t('These tokens can be used in all text fields.'),
        '#weight' => 45,
      );

      $form['sharemessage_token_help']['browser'] = array(
        '#theme' => 'token_tree',
        '#token_types' => array('node', 'sharemessage'),
        '#dialog' => TRUE,
      );
    }

    return $form;
  }

  public function buildEntity(array $form, FormStateInterface $form_state) {
    $sharemessage = parent::buildEntity($form, $form_state);
    if (!$sharemessage->override_default_settings) {
      $sharemessage->settings = array();
    }

    // Move the override field into the settings array.
    if (\Drupal::config('sharemessage.settings')->get('message_enforcement')) {
      $sharemessage->settings['enforce_usage'] = $sharemessage->enforce_usage;
      unset($sharemessage->enforce_usage);
    }
    return $sharemessage;
  }


    /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, FormStateInterface $form_state) {
    $sharemessage = $this->entity;
    $status = $sharemessage->save();

    $url = $sharemessage->url();
    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('ShareMessage %label has been updated.', array('%label' => $sharemessage->label())));
      watchdog('contact', 'ShareMessage %label has been updated.', array('%label' => $sharemessage->label()), WATCHDOG_NOTICE, $sharemessage->link($this->t('Edit'), 'edit-form'));
    }
    else {
      drupal_set_message(t('ShareMessage %label has been added.', array('%label' => $sharemessage->label())));
      watchdog('contact', 'ShareMessage %label has been added.', array('%label' => $sharemessage->label()), WATCHDOG_NOTICE, $sharemessage->link($this->t('Edit'), 'edit-form'));
    }
    $form_state->setRedirect('sharemessage.sharemessage_list');
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::delete().
   */
  public function delete(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('sharemessage.sharemessage_edit', array('sharemessage' => $this->entity->id()));
  }

}
