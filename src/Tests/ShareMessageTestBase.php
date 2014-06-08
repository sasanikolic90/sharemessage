<?php
/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageTestBase.
 */

namespace Drupal\sharemessage\Tests;

use Drupal\simpletest\WebTestBase;

class ShareMessageTestBase extends WebTestBase {

  public static $modules = array('sharemessage', 'sharemessage_test', 'block');

  public function setUp() {
    parent::setUp();

    // Create an admin user.
    $permissions = array(
      'access administration pages',
      'administer blocks',
      'administer sharemessages',
      'view sharemessages',
    );

    $admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($admin_user);
  }
}