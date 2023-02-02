/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_sass = {
    attach: function(context, settings) {

      // Custom code here

    }
  };

  // Status Messages
  Drupal.behaviors.status_messages = {
    attach: function(context, settings) {
      const removeButtonsHighlighted = document.querySelectorAll(".highlighted .btn-close");
      removeButtonsHighlighted.forEach((btn) => {
        btn.addEventListener('click', function(){
          btn.parentElement.remove();
        });
      })
    }
  };

})(jQuery, Drupal);
