angular.module('parkme')

/**
 * location model
 * @return {Object}
 */
.factory('Location', function($filter) {
	var Location = function(locationData) {
        this.id = parseInt(this.id);
	    angular.extend(this, locationData);
	    this.title = this.street;
        this.title += (this.suburb) ? ', ' + this.suburb : '';
        this.distanceFrom = this.distance.value + ' ' + this.distance.unit;
        this.descriptionDuration = this.maximumStay.value + ' ';
        this.descriptionDuration += (this.maximumStay.unit === 1) ? (this.maximumStay.unit) : (this.maximumStay.unit) + 's';
        this.price = (this.rate.operational || this.rate.value === 0) ? $filter('currency')(this.rate.value, this.rate.unit) + '/' + this.rate.period : 'free';
        this.descriptionPrice = $filter('currency')(this.rate.value, this.rate.unit);
    };

	return Location;
})

/**
* locations collection
* @param  {Object} $q
* @return {Object}
*/
.service('locations', function($q, $http, Location) {
    var locations = [];
    return {
        /**
         * api call to get locations collection
         * @return {Object} Promise
         */ 
        query: function(params) {
            return $http.post('api/locations', params, {cache: true}).then(function(data) {
                locations = [];
                angular.forEach(data.data.parkingLocations, function(data, key) {
                    var location = new Location(data);
                    locations.push(location);
                });
            });
        },

        /**
         * get all locations
         * @return {Object}
         */
        get: function() {
            return locations;
        },

        /**
         * get one location by id
         * @return {Object}
         */
        getById: function(id) {
            var result = {};
            angular.forEach(locations, function(location, key){
                if (location.id == parseInt(id)) {
                    result = location;
                }
            });
            return result;
        },

        /**
         * get by parking duration
         * @return {Object}
         */
        getByDuration: function(limit) {
            var subset = [];
            angular.forEach(locations, function(location, key){
                if (location.maximumStay.value <= limit) {
                    subset.push(location);
                }
            });
        	return subset;
        }
    }
})

/**
 * Sessions Singleton Service
 * @return {Object}
 */
.service('session', function() {
    var sessionData = {};
    return {
        isLocalStroage: function() {
            return (typeof(Storage) !== "undefined");
        },
        set: function(item, value) {
            if (this.isLocalStroage()) {
                localStorage.setItem(item, angular.toJson(value));
            }
        },
        get: function(item) {
            if (this.isLocalStroage()) {
                return angular.fromJson(localStorage.getItem(item));
            } else {
                return false;
            }
        },
        remove: function(item) {
            if (localStorage.getItem(item)) {
                localStorage.removeItem(item);
            }
        }
    };
})

/**
 * [description]
 * @param  {Object} session
 * @param  {Object} $q
 * @return {Object}
 */
.service('settings', function(session, $q) {
    var currentLocation = {};
    return {

        /**
         * set current location based on device location
         */
        setCurrentLocation: function(location) {

            // use network location
            var deferred = $q.defer();

            if (location) {
                currentLocation = location;
            } else {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // resolve deferred
                        deferred.resolve(position);
                        if (position.coords) {
                            currentLocation = {
                                longitude: position.coords.longitude,
                                latitude: position.coords.latitude
                            };
                        }
                    },
                    function() {
                    // reject
                    deferred.reject('Error getting location');
                });
            }
            return deferred.promise;
        },

        /**
         * get current location
         * @return {Object}
         */
        get: function(){
            return currentLocation;
        },

        /**
         * check if device has been located
         * @return {Object}
         */
        isDeviceLocated: function(){
            return (currentLocation.latitude && currentLocation.longitude);
        }
    };
})

.service('navigation', function(session, settings) {
    var iOSversion = function() {
        if (/iP(hone|od|ad)/.test(navigator.platform)) {
            // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
            var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
            return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
        }
    };
    return {
        go: function() {
            var origin = settings.get('location');
            var destination = session.get('chosenLocation');
            location = {
                lng: 153.023449,
                lat: -27.471011
            };
            
            var directions = origin.lat + ',' + origin.lng + '/' + destination.lat + ',' + detination.lng + '?dirflg=w';

            // If it's an iPhone..
            if ((navigator.platform.indexOf("iPhone") !== -1) || (navigator.platform.indexOf("iPod") !== -1)) {

                var ver = iOSversion() || [0];

                if (ver[0] >= 6) {
                    protocol = 'maps://';
                } else {
                    protocol = 'http://';

                }
                window.location = protocol + 'maps.apple.com/maps/dir/' + directions;
            } else {
                window.open('http://maps.google.com/maps/dir/' + directions);
            }
        }
    };
});