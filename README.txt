
PROJECT
-------
http://drupal.org/project/sharemessage


INSTALLATION
------------
1. Download and extract the module to your sites/all/modules/contrib folder.
2. Enable the module on the Drupal Modules page (admin/modules) or using
   $ drush en


HOW TO ADD SHARING WIDGETS
--------------------------
1. Go to admin/config/services/sharemessage/settings and configure your default
   sharing options. Available options are documented in detail in the section
   "SETTINGS" below.
2. Open the "list" tab and click on "Add sharemessage". Enter the values you
   want to be shared, note that you can use available tokens in order to make
   sharemessages display dynamic content.
3. If you don't want to use the default settings you configured on the settings
   page before, you can override those by checking "Override default settings".
4. Check the "Provide a block" setting.
5. Go to admin/structure/block and configure look for a block that has the name
   of your sharemessage. Use the block settings to control where your share
   message is shown. (Mind the used tokens!).

Alternatively you can use an entityreference field on any node/entity with
display format "Rendered entity" to display sharemessages on within nodes and
fieldable entities.


ENFORCE OVERRIDING SHARE MESSAGES
---------------------------------
It is possible to enforce overriding of certain sharemessages if two sharemes-
sages point to the same URL.
This may be useful for example if you have a share message on a product page and
one on the checkout complete, while both point on the product page, you will
want to display another sharemessage in case the user shares the checkout
complete message.

To accomplish this enforcement of the override, you can just enable the option
"Enforce the usage of this share message on the page it points to" on the share-
message that is displayed on the checkout page.

Be careful if your site uses the querystring "?smid=" somewhere in another con-
text, this may lead to unexpected effects/conflicts. In that case, you can
disable this feature with a global setting.


OPTIONS
-------
- AddThis Profile ID:
  Optional. Enter your addthis profile ID in order to be able to track your
  shares on your addthis account.

- Default visible services:
  Determines which service buttons will be displayed on a share widget. You can
  override this per sharemessage.
 
- Show additional services button:
  If checked, a button, which displays a list of additional services in a popup,
  will be displayed.
 
- Show Addthis counter:
  Adds a share counter that counts all shares and displays the amount of shares
  on a certain page.
 
- Default icon style:
  The size of the share buttons in pixel.


EXPORT/IMPORT
-------------
Using entities EntityAPIControllerExportable class, sharemessages are fully
export-/importable. In order to export a sharemessage open the list
(admin/config/services/sharemessage) and use the "export" operation. Importing
can be done by following the "Import share message" link on the same page. Just
paste your exported sharemessage code and import it.


TESTING SHARES ON FACEBOOK
-------------
In order to test your shares on facebook, you can share your nodes or pages the
usual way, by clicking on the like button. This has the huge disadvantage, that
facebook may cache your share requests of the same page. To avoid this annoying
issue, use facebooks debugger tool: http://developers.facebook.com/tools/debug.


CREDITS
-------------
This module was developed and is maintained by MD Systems (by Miro Dietiker,
Berdir, s_leu). The Development has been sponsored by Kampaweb GmbH
http://kampaweb.ch/ and MD Systems http://www.md-systems.ch.
