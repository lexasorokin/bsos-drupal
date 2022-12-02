(function(Drupal, $) {
  'use strict';
  var gmapLoaded = 0;
  $.fn.renderGmap = function(options){
    var map = new google.maps.Map($(this)[0], {
      zoom: options.zoom,
      center: options.center,
      scrollwheel: false
    });
    var bounds = new google.maps.LatLngBounds();
    $(options.markers).each(function(i, data){
      var marker = new google.maps.Marker({
        position: data.marker,
        map: map,
        icon: options.markerIcon
      });
      bounds.extend(data.marker);
      var $textarea = '';
      if(data.content){
        $textarea = data.content;
      }
      var infowindow = new google.maps.InfoWindow({
        content: $textarea
      });
      marker.addListener('click', function() {
        infowindow.open(map, marker);
      });
    });
    map.fitBounds(bounds);
    var listener = google.maps.event.addListener(map, "idle", function() { 
      map.setZoom(options.zoom);
      google.maps.event.removeListener(listener); 
    });
  };
  Drupal.behaviors.visualEditorFront = {
    attach: function(context, settings) {
      if(typeof google === 'object' && typeof google.maps === 'object'){
        gmapLoaded = 1;
      }
      if(gmapLoaded == 0 && settings.ve_map && settings.ve_map.apiKey){
        $.getScript('//maps.googleapis.com/maps/api/js?key=' + settings.ve_map.apiKey, function(data, textStatus, jqxhr) {
          for(var i in settings.ve_map.canvas){
            var $canvasEle = $('#'+i);
            var canvas = settings.ve_map.canvas[i];
            if($canvasEle){
              $canvasEle.renderGmap({
                zoom: parseInt(canvas.zoom),
                center: canvas.center,
                markers: canvas.markers,
                markerIcon: settings.ve_map.markerIcon
              });
            }
          };
        });
      }
      gmapLoaded++;
      $('.ve-animate').each(function(){
        var $this = $(this);
        $this.waypoint(function() {
            $this.addClass($this.data('effect')+' animated');
        }, { offset: '80%' });
      });
    }  
  };
})(Drupal, jQuery);