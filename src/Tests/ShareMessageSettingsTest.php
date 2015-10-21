<?php
/**
 * @file
 * Definition of Drupal\sharemessage\Tests\ShareMessageSettingsTest.
 */

namespace Drupal\sharemessage\Tests;

/**
 * Check if default and sharemessage specific settings work correctly.
 *
 * @group sharemessage
 */
class ShareMessageSettingsTest extends ShareMessageTestBase {

  /**
   * Test case that check if default and sharemessage specific settings work correctly.
   */
  public function testShareMessageSettings() {

    // Step 1: Setup default settings.
    $this->drupalGet('admin/config/services/sharemessage/settings');
    $default_settings = array(
      'default_services[]' => array(
        'facebook',
        'facebook_like',
      ),
      'default_additional_services' => FALSE,
      'default_icon_style' => 'addthis_16x16_style',
    );
    $this->drupalPostForm(NULL, $default_settings, t('Save configuration'));

    // Step 2: Create share message with customized settings.
    $this->drupalGet('admin/config/services/sharemessage/add');
    $sharemessage = array(
      'label' => 'ShareMessage Test Label',
      'id' => 'sharemessage_test_label',
      'override_default_settings' => 1,
      'settings[services][]' => array(
        'facebook',
      ),
      'settings[additional_services]' => 1,
      'settings[icon_style]' => 'addthis_32x32_style',
    );
    $this->drupalPostForm(NULL, $sharemessage, t('Save'));
    $this->assertText(t('ShareMessage @label has been added.', array('@label' => $sharemessage['label'])));

    // Step 3: Verify that settings are overridden
    // (services, additional_services and icon_style).
    $this->drupalGet('sharemessage-test/sharemessage_test_label');
    $raw_html_services = '<a class="addthis_button_facebook_like"></a>';
    $raw_html_additional_services = '<a class="addthis_button_compact"></a>';
    $raw_html_icon_style = '<div class="addthis_toolbox addthis_default_style ' . $sharemessage['settings[icon_style]'] . '"';

    // Check services (facebook_like button should not be displayed).
    $this->assertNoRaw($raw_html_services, 'Facebook like button that is globally enabled is not displayed on the page, so that the global settings are overridden.');

    // Additional services should be displayed.
    $this->assertRaw($raw_html_additional_services, 'Additional services button is displayed, so that the global settings are overridden.');

    // Check icon style.
    $this->assertRaw($raw_html_icon_style, 'Icon style is changed to "' . $sharemessage['settings[icon_style]'] . '" so that the global settings are overridden.');

    // Step 4: Uncheck "Override default settings" checkbox.
    $this->drupalGet('admin/config/services/sharemessage/manage/' . $sharemessage['id']);
    $edit = array(
      'override_default_settings' => FALSE,
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertText(t('ShareMessage @label has been updated.', array('@label' => $sharemessage['label'])));

    // Step 5: Check that addThis widget is displayed with default settings.
    $this->drupalGet('sharemessage-test/sharemessage_test_label');

    // Check services (facebook_like button should be displayed).
    $this->assertRaw($raw_html_services, 'Facebook like button is displayed as it is globally configured.');

    // Additional services button should not be displayed.
    $this->assertNoRaw($raw_html_additional_services, 'Additional services buttion is not displayed as it is globally configured.');

    // Check icon style (should be addthis_16x16_style).
    $raw_html_default_icon_style = '<div class="addthis_toolbox addthis_default_style ' . $default_settings['default_icon_style'] . '"';
    $this->assertRaw($raw_html_default_icon_style, 'Default icon style is used.');
  }

  /**
   * Test case for the delete and cancel button functionality for ShareMessage.
   */
  function testShareMessageDeleteCancel() {
    // Create a share message.
    $this->drupalGet('admin/config/services/sharemessage/add');
    $sharemessage = [
      'label' => 'ShareMessage Test Label',
      'id' => 'sharemessage_test_label',
      'override_default_settings' => 1,
      'settings[services][]' => [
        'facebook',
      ],
      'settings[additional_services]' => 1,
      'settings[icon_style]' => 'addthis_32x32_style',
    ];
    $this->drupalPostForm(NULL, $sharemessage, t('Save'));

    // Check newly created ShareMessage on list page.
    $this->drupalGet('admin/config/services/sharemessage');
    $this->assertText($sharemessage['label'], 'Newly created sharemessage found.');
    // Check for Edit link.
    $this->assertLink('Edit');
    // Check for the Delete link.
    $this->assertLink('Delete');

    // Click delete link on admin ui.
    $this->clickLink('Delete');
    $this->assertText(t('Are you sure you want to delete @label?', ['@label' => $sharemessage['label']]));

    // Check if cancel button is present or not.
    $this->assertLink('Cancel');

    // Delete the share message.
    $this->drupalPostForm(NULL, [], t('Delete'));
    $this->assertText(t('ShareMessage @label has been deleted.', ['@label' => $sharemessage['label']]));

    // Check if removed from listing page as well.
    $this->drupalGet('admin/config/services/sharemessage');
    $this->assertNoText($sharemessage['label'], 'Not found the deleted message.');
  }
}
