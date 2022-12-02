(function($) {

  'use strict';

  const mq = window.matchMedia( '(min-width: 992px)' );
  const headerpos = $('.header-branding-navigation').offset().top;

  if (mq.matches) {
    $(window).scroll(function() {
      var header = $('.header-branding-navigation'),
          f_header = $('.fullscreen-header'),
          windowpos = $(window).scrollTop();

      if (windowpos >= (headerpos + 134)) {
        header.addClass('header-fixed');
        f_header.css('justify-content', 'flex-end');
      } else {
        header.removeClass('header-fixed');
        f_header.css('justify-content', 'space-between');
      }
    });
  }

})(jQuery);
