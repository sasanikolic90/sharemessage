<?php
/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageWorkflowTest.
 */

namespace Drupal\sharemessage\Tests;

/**
 * Main sharemessage workflow through the admin UI.
 *
 * @group Sharemessage
 */
class ShareMessageWorkflowTest extends ShareMessageTestBase {

  /**
   * Main sharemessage workflow through the admin UI.
   */
  public function testShareMessageWorkflow() {

    // Step 1: Create a share message in the UI.
    $this->drupalGet('admin/config/services/sharemessage/add');
    $edit = array(
      'label' => 'ShareMessage Test Label',
      'id' => 'sharemessage_test_label',
      'title' => 'ShareMessage Test Title',
      'message_long' => 'ShareMessage Test Long Description',
      'message_short' => 'ShareMessage Test Short Description',
      'image_url' => 'http://www.example.com/drupal.jpg',
      'share_url' => 'http://www.example.com',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertText(t('ShareMessage @label has been added.', array('@label' => $edit['label'])), t('ShareMessage is successfully saved.'));

    // Step 2: Display share message and verify AddThis markup
    // and meta header elements.
    $this->drupalGet('sharemessage-test/sharemessage_test_label');
    $raw_html_string = '<div class="addthis_toolbox addthis_default_style addthis_16x16_style">';
    $this->assertRaw($raw_html_string, t('AddThis buttons are displayed.'));

    $meta_title = '<meta property="og:title" content="' . $edit['title'] . '" />';
    $this->assertRaw($meta_title, t('OG:title exists and has appropriate content.'));

    $meta_description = '<meta property="og:description" content="' . $edit['message_long'] . '" />';
    $this->assertRaw($meta_description, t('OG:description exists and has appropriate content.'));

    $meta_image = '<meta property="og:image" content="' . $edit['image_url'] . '" />';
    $this->assertRaw($meta_image, t('OG:image exists and has appropriate content.'));

    $meta_url = '<meta property="og:url" content="' . $edit['share_url'] . '" />';
    $this->assertRaw($meta_url, t('OG:url exists and has appropriate content.'));

    $this->drupalGet('admin/config/services/sharemessage/add');
    // Check if the enforce checkbox is there.
    $this->assertFieldByName('enforce_usage', NULL, 'The enforce checkbox was found.');

    $edit_2 = array(
      'label' => 'ShareMessage 2 Test Label',
      'id' => 'sharemessage_test_label2',
      'title' => 'ShareMessage 2 Test Title',
      'message_long' => 'ShareMessage 2 Test Long Description',
      'message_short' => 'ShareMessage 2 Test Short Description',
      'image_url' => $edit['image_url'],
      'share_url' => $edit['share_url'],
      'enforce_usage' => 1,
    );
    $this->drupalPostForm(NULL, $edit_2, t('Save'));

    $sharemessage = entity_load('sharemessage', 'sharemessage_test_label2');
    // Check if the option was saved as expected.
    $this->assertEqual(!empty($sharemessage->settings['enforce_usage']), TRUE, 'Enforce setting was saved on the entity.');
    $this->drupalGet('sharemessage-test/sharemessage_test_label', array('query' => array('smid' => 'sharemessage_test_label2')));

    // Check if the og:description tag gets rendered correctly.
    $meta_description = '<meta property="og:description" content="' . $edit_2['message_long'] . '" />';
    $this->assertRaw($meta_description, t('OG:description was overridden properly.'));
    // Check if the og:url tag gets rendered correctly.
    $url = url($edit['share_url'], array('query' => array('smid' => 'sharemessage_test_label2')));
    $meta_url = '<meta property="og:url" content="' . $url . '" />';
    $this->assertRaw($meta_url, t('OG:url has correct query string.'));
    $meta_url = '<meta property="og:url" content="' . $edit['share_url'] . '" />';
    $this->assertNoRaw($meta_url, t('Suppressing og:url meta tag for overridden sharemessage.'));

    // Check if the overridden sharemessage is rendered correctly.
    $this->assertRaw('addthis:description="' . $edit['message_long'] . '"', t('Overridden sharemessage has OG data as attributes.'));

    // Disable enforcement of overrides in the global settings.
    \Drupal::config('sharemessage.settings')->set('message_enforcement', FALSE)->save();
    $this->drupalGet('sharemessage-test/sharemessage_test_label', array('query' => array('smid' => 'sharemessage_test_label2')));

    // Check if the og:description tag gets rendered correctly.
    $meta_description = '<meta property="og:description" content="' . $edit['message_long'] . '" />';
    $this->assertRaw($meta_description, t('OG:description was not overridden.'));
    // Check if the og:url tag gets rendered correctly.
    $meta_url = '<meta property="og:url" content="' . $edit['share_url'] . '" />';
    $this->assertRaw($meta_url, t('OG:url does not contain query string.'));
  }
}
