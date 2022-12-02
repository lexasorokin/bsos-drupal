(function($) {

  'use strict';

  if ( !Modernizr.objectfit ) {
    $('.fullscreen-image').each(function () {
      var $container = $(this),
        imgUrl = $container.find('img').prop('src');
      if (imgUrl) {
        $container
          .css('backgroundImage', 'url(' + imgUrl + ')')
          .addClass('compat-object-fit');
      }
    });
  }
})(jQuery);