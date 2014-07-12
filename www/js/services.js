angular.module('parkme')

/**
 * location model
 * @return {Object}
 */
.factory('Location', function() {
	var Location = function(locationData) {
	    angular.extend(this, locationData);
	    this.id = 1;
	}

	Location.prototype.alert = function() {
	    console.log('alert', this.id);
	}

	return Location;
})

/**
* locations collection
* @param  {[type]} $q
* @return {Object}
*/
.service('locations', function($q, $http, Location) {
    var locations = [];
    return {
        query: function(params) {
            return $http.post('api/locations', params, {cache: true}).then(function(data) {
                angular.forEach(data, function(data, key) {
                    var location = new Location(data);
                    locations.push(location);
                });
            });
        },
        get: function() {
        	return locations;
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
    return {
        // set current location
        setCurrentLocation: function() {
            // clean any local session if any for user loation
            session.set('currentLocation',{longitude: 0, latitude: 0});

            // use network location
            var deferred = $q.defer();
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // resolve deferred
                    deferred.resolve(position);
                    if (position.coords) {
                    	session.set('currentLocation', {
                    		longitude: position.coords.longitude,
                    		latitude: position.coords.latitude
                    	});
                    }
                },
                function() {
                    deferred.reject('Error getting location');
            });
            return deferred.promise;
        },
        init: function() {
            if (!session.get('currentLocation')){
                session.set('currentLocation', {});
            }
        }
    };
})

.service('navigation', function(session, Location) {
    var iOSversion = function() {
        if (/iP(hone|od|ad)/.test(navigator.platform)) {
            // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
            var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
            return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
        }
    };
    return {
        go: function() {
            var location = session.get('location');
            location = {
                lng: 153.023449,
                lat: -27.471011
            };
            var destination = Location.getPlace().location;
            var directions = location.lat + ',' + destination.lng + '/' + destination.lat + ',' + location.lng + '?dirflg=w';

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