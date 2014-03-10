<?php
/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageExposeToBlockTestCase.
 */

namespace Drupal\sharemessage\Tests;

use Drupal\simpletest\WebTestBase;

class ShareMessageExposeToBlockTestCase extends ShareMessageTestBase {

  public static function getInfo() {
    return array(
      'name' => 'ShareMessage blocks',
      'description' => 'Check if sharemessage is exposed as block.',
      'group' => 'ShareMessage',
    );
  }

  /**
   * Test case that check if sharemessage is exposed as block.
   */
  public function testShareMessageExposeToBlock() {
// Step 1: Create another sharemessage that will be exposed as block.
    $this->drupalGet('admin/config/services/sharemessage/add');
    $sharemessage = array(
      'label' => 'ShareMessage Test Label',
      'name' => 'sharemessage_test_label',
      'block' => 1,
    );
    $this->drupalPost(NULL, $sharemessage, t('Save share message'));
    $this->assertText(t('Message @label saved.', array('@label' => $sharemessage['label'])));

// Step 2: Go to block section and enable block to be visible
// on content region.
    $this->drupalGet('admin/structure/block');
    $this->assertRaw($sharemessage['label'], t($sharemessage['label'] . ' block exists.'));

    $edit = array(
      'blocks[sharemessage_' . $sharemessage['name'] . '][region]' => 'content',
    );
    $this->drupalPost('admin/structure/block', $edit, t('Save blocks'));

// Step 3: Go to fron page and check if sharemessage is shown.
    $this->drupalGet('<front>');
    $raw_html_string = '<div class="addthis_toolbox addthis_default_style addthis_16x16_style"';
    $this->assertRaw($raw_html_string, t('AddThis buttons are displayed as block on homepage.'));
  }
}