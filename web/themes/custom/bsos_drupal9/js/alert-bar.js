(function($, Drupal) {

  'use strict';

  // Owl Slider for OACS Alerts section
  Drupal.behaviors.alert_bar = {
    attach: function(context, settings) {
      $("#alert-slider").owlCarousel({
        items: 1,
        nav: false,
        dots: false,
        autoplay: true,
        loop: true,
      });
    }
  };

})(jQuery, Drupal);
