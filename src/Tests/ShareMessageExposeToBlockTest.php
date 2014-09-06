<?php
/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageExposeToBlockTestCase.
 */

namespace Drupal\sharemessage\Tests;

/**
 * Check if sharemessage is exposed as block.
 *
 * @group Sharemessage
 */
class ShareMessageExposeToBlockTest extends ShareMessageTestBase {

  /**
   * Test case that check if sharemessage is exposed as block.
   */
  public function testShareMessageExposeToBlock() {
    // First enable the bartik theme to place the sharemessage block afterwards.
    $theme = 'bartik';
    \Drupal::service('theme_handler')->enable(array($theme));
    \Drupal::config('system.theme')->set('default', $theme)->save();

    // Create another sharemessage.
    $sharemessage = array(
      'label' => 'ShareMessage Test Label',
      'id' => 'sharemessage_test_label',
    );
    $this->drupalPostForm('admin/config/services/sharemessage/add', $sharemessage, t('Save'));
    // Check for confirmation message and listing of the sharemessage entity.
    $this->assertText(t('ShareMessage @label has been added.', array('@label' => $sharemessage['label'])));
    $this->assertText($sharemessage['label']);

    // Add a block that will contain the created sharemessage.
    $block = array(
      'settings[label]' => 'Sharemessage test block',
      'settings[sharemessage]' => $sharemessage['id'],
      'region' => 'content',
    );
    $this->drupalPostForm('admin/structure/block/add/sharemessage_block/' . $theme, $block, t('Save block'));
    // Verify that the block is in the submitted region of the bartik theme.
    $this->drupalGet('admin/structure/block/list/' . $theme);
    $this->assertText($block['settings[label]']);

    // Go to front page and check whether sharemessage is displayed.
    $this->drupalGet('/');
    $raw_html_string = '<div class="addthis_toolbox addthis_default_style addthis_16x16_style"';
    $this->assertRaw($raw_html_string, 'AddThis buttons are displayed as block on homepage.');
  }
}
