(function($) {
  var VEGmap = {
    ele: {},
    map: null,
    settings: null,
    initMap: function(settings, $ele) {
      var $this = this;
      this.settings = settings;
      this.cacheDOM();
    },
    cacheDOM: function() {
      this.ele.document = $(document);
      this.ele.body = $('body');
    }
  };
  $.fn.veGmap = function(options) {
    var $ele = $(this);
    var settings = $.extend({
      gmapKey: {}
    }, options);
    if ($ele.length > 0) {
      var index = $ele.data('index'), data = {
        center: {
          lat: 0,
          lng: 0
        },
        zoom: 1
      };
      if ($(document).find('#place-marker-' + index).length > 0) {
        data = ($(document).find('#place-marker-' + index).val() != '') ? JSON.parse($(document).find('#place-marker-' + index).val()) : data;
        var map = new google.maps.Map($ele[0], data);
        $ele.closest(".gmap-place").find("summary").on("click", function() {
          setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(data.center);
          }, 1000);
        });
        data.markerIcon = settings.gmapMarkerIcon;
        data.apiKey = settings.gmapKey;
        var marker = new google.maps.Marker({
          position: data.center,
          map: map,
          title: Drupal.t('Click here to edit place details'),
          draggable: true,
          icon: settings.gmapMarkerIcon
        });
        marker.addListener('dragend', function(e) {
          data.center.lat = e.latLng.lat();
          data.center.lng = e.latLng.lng();
          data.zoom = map.getZoom();
          $(document).find('#place-marker-' + index).val(JSON.stringify(data));
        });
        google.maps.event.addListener(map, 'zoom_changed', function(e) {
          data.zoom = map.getZoom();
          data.apiKey = settings.gmapKey;
          $(document).find('#place-marker-' + index).val(JSON.stringify(data));
        });
      }
    }
  };
})(jQuery);