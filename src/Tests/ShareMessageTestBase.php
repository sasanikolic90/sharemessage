<?php
/**
 * @file
 * Contains \Drupal\sharemessage\ShareMessageTestBase.
 */

namespace Drupal\sharemessage\Tests;

use Drupal\simpletest\WebTestBase;

abstract class ShareMessageTestBase extends WebTestBase {

  public static $modules = array('sharemessage', 'sharemessage_test', 'block');

  public function setUp() {
    parent::setUp();

    // Create an admin user.
    $permissions = array(
      'access administration pages',
      'administer blocks',
      'administer sharemessages',
      'view sharemessages',
      'administer themes'
    );

    $admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($admin_user);

    $this->drupalPlaceBlock('page_title_block');
  }
}
