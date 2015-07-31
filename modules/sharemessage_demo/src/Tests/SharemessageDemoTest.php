<?php
/**
 * @file
 * Contains \Drupal\sharemessage_demo\Tests\SharemessageDemoTest.
 */

namespace Drupal\sharemessage_demo\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the demo module for sharemessage.
 *
 * @group sharemessage
 */
class SharemessageDemoTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  public static $modules = array(
    'sharemessage_demo',
    );

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Install bartik theme.
    \Drupal::service('theme_handler')->install(array('bartik'));
    $theme_settings = $this->config('system.theme');
    $theme_settings->set('default', 'bartik')->save();
  }

  /**
   * Asserts translation jobs can be created.
   */
  protected function testInstalled() {
    $admin_user = $this->drupalCreateUser([
      'access content overview',
      'administer content types',
      'administer blocks',
      'view sharemessages',
    ]);

    $this->drupalLogin($admin_user);
    $this->drupalGet('admin/structure/block');
    $this->assertText(t('Share message'));
    $this->clickLink(t('Configure'), 0);

    $this->drupalGet('admin/structure/types');
    $this->assertText(t('Shareable content'));

    // Search for the sharemessage block on the demo node.
    $this->drupalGet('admin/content');
    $this->clickLink(t('Sharemessage demo'));
    $this->assertText(t('Welcome to the Sharemessage demo module!'));
    $this->assertText(t('Share message'));

    // Asserts that the buttons are displayed.
    $this->assertRaw('addthis_button_preferred_1');
    $this->assertRaw('addthis_button_preferred_2');
    $this->assertRaw('addthis_button_preferred_3');
    $this->assertRaw('addthis_button_preferred_4');
    $this->assertRaw('addthis_button_preferred_5');
    $this->assertRaw('addthis_button_compact');
  }

}
