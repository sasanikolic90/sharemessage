/**
 * @file
 * Ensures that ShareMessage is visible in dialogs and other ajax elements.
 */

(function ($, Drupal) {

  "use strict";

  /**
   * Helper function for initialization of share message elements.
   *
   * @param {jQuery} jQuery object that is holding share message elements.
   */
  var initElements = function ($elements) {
    $elements.each(function() {
      addthis.toolbox(this);
    });
  };

  /**
   * Attaches the ShareMessage behaviour inside dialogs.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.sharemessage = {
    attach: function (context) {
      var $sharemessageElements = $(context).find('.addthis_toolbox');

      if ($sharemessageElements.length === 0) {
        return;
      }

      // This is used for special cases when the scripts are added using AJAX,
      // for example if ShareMessage is rendered in a dialog. In that case
      // the addthis library is loaded after ShareMessage.
      if (addthis === undefined) {
        var interval = setInterval(function() {
          if (addthis !== undefined) {
            clearInterval(interval);
            initElements($sharemessageElements);
          }
        }, 50);
      }
      else {
        initElements($sharemessageElements);
      }
    }
  };

})(jQuery, Drupal);
