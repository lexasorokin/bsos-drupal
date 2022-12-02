(function($) {

  'use strict';
  var num_features = $('.feature-carousel .carousel-item').length,
      num_features_class = 'feature-count-' + num_features;
  $('.feature-carousel-view').addClass(num_features_class);

  if ( num_features === 1 ) {
    $('.feature-carousel').owlCarousel({
      margin: 0,
      dots: false,
      items: 1
    });
  }

  if ( num_features === 2 ) {
    $('.feature-carousel').owlCarousel({
      margin: 0,
      dots: false,
      responsive: {
        0: {
          items: 1
        },
        650: {
          items: 2
        }
      }
    });
  }

  if ( num_features > 2 ) {
    $('.feature-carousel').owlCarousel({
      autoplay: true,
      autoplayHoverPause: true,
      margin: 0,
      dots: false,
      nav: true,
      navContainer: '.feature-controls',
      rewind: true,
      responsive: {
        0: {
          items: 1
        },
        600: {
          items: 2
        },
        1000: {
          items: 3
        }
      }
    });

    $('.play-feature-carousel').on('click',function(){
      $('.feature-carousel').trigger('play.owl.autoplay',[1000])
    })
    $('.stop-feature-carousel').on('click',function(){
      $('.feature-carousel').trigger('stop.owl.autoplay')
    })
  }

  $('.feature-carousel .owl-item').matchHeight();

  $('.profile-carousel').owlCarousel({
    /* New stuff */
    autoplay: true,
    autoplayHoverPause: true,
    /* End new stuff */
    margin:24,
    dots: false,
    nav:true,
    navContainer:'.profile-controls',
    rewind:true,
    responsive:{
      0:{
        items:1
      },
      600:{
        items: 2
      }
    }
  });

  $('.play-feature-carousel').on('click', function() {
    $('.feature-carousel').trigger('play.owl.autoplay', [1000]);
  });
  $('.stop-feature-carousel').on('click', function() {
    $('.feature-carousel').trigger('stop.owl.autoplay');
  });

  $('.alerts-carousel').owlCarousel({
    autoplay: true,
    autoplayTimeout: 10000,
    dots: false,
    loop:true,
    items:1,
    rewind:false
  });

  $('.owl-multi').owlCarousel({
    autoplay: true,
    autoplayTimeout: 10000,
    dots:false,
    loop:true,
    items:1,
    nav:true,
    rewind:false
  })
})(jQuery);
