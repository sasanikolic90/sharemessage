<?php
/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageTestBase.
 */
/*
namespace Drupal\sharemessage\Tests;

use Drupal\simpletest\WebTestBase;

class ShareMessageTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   *
  public static $modules = array('sharemessage', 'sharemessage_test');

  public static function getInfo() {
    return array(
      'name' => 'Uninstall action test',
      'description' => 'Tests that uninstalling actions does not remove other module\'s actions.',
      'group' => 'Action',
    );
  }

  public function setUp() {

    // Create an admin user.
    $permissions = array(
      'access administration pages',
      'administer blocks',
      'administer sharemessages',
      'view sharemessages',
    );

    $admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($admin_user);

    // Add profile ID to the global settings.
    \Drupal::config('sharemessage.settings')->set('sharemessage_addthis_profile_id', 'ra-5006849061326d1cl');
  }
}*/