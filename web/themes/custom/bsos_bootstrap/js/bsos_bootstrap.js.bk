(function ($, Drupal, drupalSettings) {

  'use strict';
  $(document).ready(function() {
    $('.image-grid-images').magnificPopup({
      delegate: '.image-grid-item > a',
      type: 'image',
      tLoading: 'Loading image #%curr%...',
      mainClass: 'mfp-img-mobile',
      gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0,1] // Will preload 0 - before current, and 1 after the current image
      },
      image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: function(item) {
          if ( item.el.siblings('.field--name-field-bsos-caption').html() ) {
            return item.el.siblings('.field--name-field-bsos-caption').html();
          } else {
            return '';
          }
        }
      }
    });
  });

  $('.paragraph__column div').children(':first').addClass('margin-zero-top');

  $('.landing-section, .btn-grid a, .event-grid-item a, .featured-grid-item a, .publication-grid-item').matchHeight();

  // media query event handler
  if (matchMedia) {
    const mq_md = window.matchMedia( '(min-width: 992px)' );
    mq_md.addListener(WidthChange);
    WidthChange(mq_md);
  }

// media query change
  function WidthChange(mq_md) {
    if (mq_md.matches) {
      // window width is at least 992px
      $( '.region-header-secondary' ).append( $('.social-links-block') );
    } else {
      // window width is less than 992px
      $( '.region-footer-menu' ).append( $('.social-links-block') );
    }

  }


  var emptyTabs = $('.bsos-profile-tabs .tab-pane').filter(function() {
    return ! $.trim($(this).text());
  });
  emptyTabs.remove();

  var anchors = $('.bsos-profile-tabs .nav-tabs a');
  anchors.each(function() {
    var id = this.hash;
    var isThere = $(id).length === 1;
    if (!isThere) {
      $(this).closest('li').remove();
    }
  });

  (function($) {
    $.fn.changeElementType = function(newType) {
      var attrs = {};

      $.each(this[0].attributes, function(idx, attr) {
        attrs[attr.nodeName] = attr.nodeValue;
      });

      this.replaceWith(function() {
        return $('<' + newType + '/>', attrs).append($(this).contents());
      });
    }
  })(jQuery);

  $('.sf-menu span.menuparent').addClass('nolink');
  if ( $('.sf-menu span.menuparent').length ) {
    $('.sf-menu span.menuparent').changeElementType('a');
  }

	// Javascript to enable link to tab
	var url = document.location.toString();
	if (url.match('#')) {
	    $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
	} 

	// Change hash for page-reload
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
	    window.location.hash = e.target.hash;
	  });

})(jQuery, Drupal, drupalSettings);
