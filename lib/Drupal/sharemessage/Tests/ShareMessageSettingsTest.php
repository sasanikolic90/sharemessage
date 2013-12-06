<?php
/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageTest.
 */

namespace Drupal\sharemessage\Tests;

use Drupal\simpletest\WebTestBase;

class ShareMessageSettingsTest extends WebTestBase {

  public static $modules = array('sharemessage', 'sharemessage_test');

  public static function getInfo() {
    return array(
      'name' => 'ShareMessage settings',
      'description' => 'Check if default and sharemessage specific settings work correctly.',
      'group' => 'ShareMessage',
    );
  }

  /**
   * Test case that check if default and sharemessage specific settings work correctly.
   */
  public function testShareMessageSettings() {

    // Create an admin user.
    $permissions = array(
      //'access administration pages',
      //'administer blocks',
      //'administer sharemessages',
      //'view sharemessages',
      'administer display modes'
    );

    $admin_user = $this->drupalCreateUser($permissions);
    //debug($admin_user);
    $this->drupalLogin($admin_user);
/*
    // Add profile ID to the global settings.
    \Drupal::config('sharemessage.settings')->set('sharemessage_addthis_profile_id', 'ra-5006849061326d1cl');

    // Step 1: Setup default settings.
    $this->drupalGet('admin/config/services/sharemessage/settings');
    $default_settings = array(
      'sharemessage_default_services[]' => array(
        'facebook',
        'facebook_like',
      ),
      'sharemessage_default_additional_services' => FALSE,
      'sharemessage_default_icon_style' => 'addthis_16x16_style',
    );
    $this->drupalPost(NULL, $default_settings, t('Save configuration'));

    // Step 2: Create share message with customized settings.
    $this->drupalGet('admin/config/services/sharemessage/add');
    $sharemessage = array(
      'label' => 'ShareMessage Test Label',
      'name' => 'sharemessage_test_label',
      'override_default_settings' => 1,
      'settings[services][]' => array(
        'facebook',
      ),
      'settings[additional_services]' => 1,
      'settings[icon_style]' => 'addthis_32x32_style',
    );
    $this->drupalPost(NULL, $sharemessage, t('Save share message'));
    $this->assertText(t('Message @label saved.', array('@label' => $sharemessage['label'])));

    // Step 3: Verify that settings are overridden
    // (services, additional_services and icon_style).
    $this->drupalGet('sharemessage-test/1');
    $raw_html_services = '<a class="addthis_button_facebook_like"></a>';
    $raw_html_additional_services = '<a class="addthis_button_compact"></a>';
    $raw_html_icon_style = '<div class="addthis_toolbox addthis_default_style ' . $sharemessage['settings[icon_style]'] . '"';

    // Check services (facebook_like button should not be displayed).
    $this->assertNoRaw($raw_html_services, t('Facebook like button that is globally enabled is not displayed on the page, so that the global settings are overridden.'));

    // Additional services should be displayed.
    $this->assertRaw($raw_html_additional_services, t('Additional services button is displayed, so that the global settings are overridden.'));

    // Check icon style.
    $this->assertRaw($raw_html_icon_style, t('Icon style is changed to "' . $sharemessage['settings[icon_style]'] . '" so that the global settings are overridden.'));

    // Step 4: Uncheck "Override default settings" checkbox.
    $this->drupalGet('admin/config/services/sharemessage/manage/' . $sharemessage['name']);
    $edit = array(
      'override_default_settings' => FALSE,
    );
    $this->drupalPost(NULL, $edit, t('Save share message'));
    $this->assertText(t('Message @label saved.', array('@label' => $sharemessage['label'])));

    // Step 5: Check that addThis widget is displayed with default settings.
    $this->drupalGet('sharemessage-test/1');

    // Check services (facebook_like button should be displayed).
    $this->assertRaw($raw_html_services, t('Facebook like button is displayed as it is globally configured.'));

    // Additional services button should not be displayed.
    $this->assertNoRaw($raw_html_additional_services, t('Additional services buttion is not displayed as it is globally configured.'));

    // Check icon style (should be addthis_16x16_style).
    $raw_html_default_icon_style = '<div class="addthis_toolbox addthis_default_style ' . $default_settings['sharemessage_default_icon_style'] . '"';
    $this->assertRaw($raw_html_default_icon_style, t('Default icon style is used.'));
*/
  }
}
