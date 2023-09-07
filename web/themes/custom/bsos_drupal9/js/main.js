// Intersection observer used for inview elements
const callback = function (entries) {
  entries.forEach((entry) => {
    // console.log(entry);

    if (entry.isIntersecting) {
      entry.target.classList.add("inview");
    } else {
      entry.target.classList.remove("inview");
    }
  });
};

const observer = new IntersectionObserver(callback);

const targets = document.querySelectorAll(".inview-element");

targets.forEach(function (target) {
  observer.observe(target);
});

jQuery(document).ready(function ($) {
  // Sticky header
  $(window).scroll(function () {
    if ($(this).scrollTop() > 190) {
      $(".site-header").addClass("site-header--sticky");
      $("body").addClass("sticky-header");
    } else {
      $(".site-header").removeClass("site-header--sticky");
      $("body").removeClass("sticky-header");
    }
  });

  // FitVids
  $(".main-content").fitVids();
  // $(".video-hero").fitVids();
  $("[data-vbg]").youtube_background({
    "play-button": false,
    mobile: true,
  });

  $("#menu-toggle, .menu-close").on("click", function (e) {
    e.preventDefault();
    $("body").toggleClass("nav-open");
    $("#menu-toggle").toggleClass("open");
    // Toggle the aria-hidden attribute on the .offcanvas-menu element
    $(".offcanvas-menu").attr(
      "aria-hidden",
      $(".offcanvas-menu").attr("aria-hidden") === "true" ? "false" : "true"
    );
  });

  let backLink =
    '<li class="go-back"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg> Back</a></li>';

  $(".offcanvas-menu li.menuparent > ul").each(function (index) {
    $(this).prepend(backLink);
  });

  // Add a button after each li that contains a ul
  $(".offcanvas-menu li.menuparent").each(function (index) {
    $(this).append(
      '<button class="sub-menu-toggle" aria-expanded="false" aria-label="Open sub menu"><span class="screen-reader-text">Open sub menu</span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></button>'
    );
  });

  var selectedOffcanvasLevel = 0;
  // When .sub-menu-toggle is clicked, increment selectedOffcanvasLevel and add translate3d(-" + 100 * this.selectedOffcanvasLevel + "%, 0, 0)" to the .mobile-nav
  $(".sub-menu-toggle").on("click", function (e) {
    e.preventDefault();
    selectedOffcanvasLevel++;
    $("#mobile-menu").css(
      "transform",
      "translate3d(-" + 100 * selectedOffcanvasLevel + "%, 0, 0)"
    );
    // Add a class of visible to the sibling ul
    $(this).siblings("ul").addClass("visible");
  });

  $(".go-back a").on("click", function (e) {
    e.preventDefault();
    selectedOffcanvasLevel--;
    $("#mobile-menu").css(
      "transform",
      "translate3d(-" + 100 * selectedOffcanvasLevel + "%, 0, 0)"
    );
    // Remove the visible class from the parent ul
    $(this).parent("ul").removeClass("visible");
  });

  // Desktop Menu
  // When a link is hovered, add a class of dropdown-visible to the body, then remove the class when the mouse leaves the menu
  $("li.menuparent").hover(
    function () {
      $("body").addClass("dropdown-visible");
    },
    function () {
      $("body").removeClass("dropdown-visible");
    }
  );

  // When the search icon is clicked, toggle the .search-overlay-open class on the body
  $(".search-trigger-link").on("click", function (e) {
    e.preventDefault();
    $("body").toggleClass("search-overlay-open");
    // Toggle the aria-hidden attribute on the .search-overlay element
    $(".search-overlay").attr(
      "aria-hidden",
      $(".search-overlay").attr("aria-hidden") === "true" ? "false" : "true"
    );
  });
  // Close the search overlay when the .close-button button is clicked
  $(".close-button").on("click", function (e) {
    e.preventDefault();
    $("body").removeClass("search-overlay-open");
    // Toggle the aria-hidden attribute on the .search-overlay element
    $(".search-overlay").attr(
      "aria-hidden",
      $(".search-overlay").attr("aria-hidden") === "true" ? "false" : "true"
    );
  });

  // $(".hero").parallaxScroll({friction:.2,direction:"vertical"})

  // $('.open-me').on('click', function(e) {
  //   e.preventDefault();
  //   $(this).toggleClass('open');
  //   // Toggle the parent element open class
  //   $(this).parent('.toggle-card').toggleClass('open');
  // });

  // Hero Image Parallax
  /** change value here to adjust parallax level */
  var parallax = -0.4;

  var $bg_images = $(".parallax");
  var offset_tops = [];
  $bg_images.each(function (i, el) {
    offset_tops.push($(el).offset().top);
  });

  $(window).scroll(function () {
    var dy = $(this).scrollTop();
    $bg_images.each(function (i, el) {
      var ot = offset_tops[i];
      $(el).css("background-position", "50% " + (dy - ot) * parallax + "px");
    });
  });

  // Get the header (To-do, temporary disabled from github html)
  //var header = document.getElementById("masthead");

  // Get the offset position of the navbar
  //var sticky = header.offsetTop;

  // Add the sticky class to the header when you reach its scroll position. Remove "sticky" when you leave the scroll position
  /*function myFunction() {
    if (window.pageYOffset > sticky) {
      header.classList.add("sticky");
    } else {
      header.classList.remove("sticky");
    }
  }*/

  // Owl Slider for homepage profiles
  $("#profiles-slider").owlCarousel({
    items: 3,
    nav: false,
    dots: true,
    // autoplay: true,
    margin: 30,
    loop: true,
    stagePadding: 30,
    // slideTransition: "linear",
    autoplaySpeed: 7000,

    center: true,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 2,
      },
      768: {
        items: 3,
      },
      1024: {
        items: 4,
      },
      1240: {
        items: 5,
      },
    },
  });

  // Two Cards Slider for the Under Hero Section
  $("#two-card-slider").owlCarousel({
    items: 2,
    nav: false,
    dots: true,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 2,
      },
    },
  });

  // Hero Slider
  $("#hero-carousel").owlCarousel({
    items: 1,
    nav: false,
    dots: true,
    autoplay: true,
    loop: true,
  });

  // Tabs (based on Bootstrap)
  // $(".tab-content:first").addClass("acitve show");
  $("ul.nav-tabs li:first a").addClass("active");
  $("ul.nav-tabs li a").on("click", function (e) {
    e.preventDefault();
    $("ul.nav-tabs li a").each(function () {
      $(this).removeClass("active");
    });
    $(this).addClass("active");
    $(".tab-pane").each(function () {
      $(this).removeClass("active show");
    });
    var activeTab = $(this).attr("href");
    $(activeTab).addClass("active show");
  });

  // Accordion (based on Bootstrap)
  $("a[data-toggle='collapse']").on("click", function (e) {
    e.preventDefault();
    $(this).toggleClass("active");
    $(this).parent().parent().toggleClass("active");
    // Change aria-expanded to true or false
    $(this).attr(
      "aria-expanded",
      $(this).attr("aria-expanded") === "false" ? "true" : "false"
    );
    // Get the URL of the link
    var url = $(this).attr("href");
    // Set the class of active to the div with the same ID as the URL
    $(url).toggleClass("in");
    // Change aria-hidden to true or false
    $(url).attr(
      "aria-hidden",
      $(url).attr("aria-hidden") === "true" ? "false" : "true"
    );
  });

  // Magnific Popup for the YouTube video in the hero
  $(".youtube-popup").magnificPopup({
    // disableOn: 700,
    type: "iframe",
    mainClass: "mfp-fade",
    removalDelay: 160,
    preloader: false,
    fixedContentPos: false,
  });
});
