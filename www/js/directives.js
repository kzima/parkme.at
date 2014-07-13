angular.module('parkme')

/**
 * google places directive
 */
.directive('googlePlaces', function(settings){
  return {
    restrict:'E',
    replace:true,
    // transclude:true,
    template: '<input id="google_places_ac" name="google_places_ac" type="text" class="form-control">',
    link: function(scope, element, attrs){
      // angular.element(element).val(session.get("currentAddress"));
      var autocomplete = new google.maps.places.Autocomplete(element[0], {});
      google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        var location = {latitude: place.geometry.location.lat(), longitude: place.geometry.location.lng()};
        settings.setCurrentLocation(location);
      });
    }
  };
})