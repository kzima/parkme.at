angular.module('parkme.controllers', [])

.controller('AppCtrl', function($scope, $ionicModal, $timeout, $state, settings) {

    /**
     * handle redirect across app
     * @param  {Object} state
     * @param  {Object} params
     */
    $scope.goTo = function(state, params){
        $state.go(state, params, { reload: true });
    };

    settings.init();

})

// home page/landing page
.controller('HomeCtrl', function($scope, $timeout, $ionicNavBarDelegate, session, settings) {

    // clear session
    session.remove('currentLocation');
    session.remove('currentAddress');

    // set the location for current device
    $scope.placeholder = "Type an address";
    settings.setCurrentLocation().then(function(){
        // input placeholder
        if (settings.isDeviceLocated()){
            $scope.placeholder = "Use my current location";
        }
    });

    // Vechicle type (car|motorcycle)
    $scope.params = {
        vechicleType: "car", 
        currentLocation: {}
    };

    // wait until page loads
    $timeout(function(){
        // remove header
        $ionicNavBarDelegate.showBar(false);
    });

    /**
     * redirect to next page
     * save location in localstorage
     */
    $scope.nextActionFn = function(){

        // update vehicle data in the session 
        session.set('vechicleType', $scope.params.vechicleType);

        // update currentLocation
        session.set('currentLocation', settings.get());

        // redirect to next page
        $scope.goTo('locations');
    }

})

// list of locations
.controller('LocationsCtrl', function($scope, $timeout, $ionicNavBarDelegate, session, locations) {
    
    // filter
    $scope.filter = {
        sort: 'nearest',
        duration: 12,
        symbol: '$',
        distance: 'm'
    };

    // distance/price breakdown (this could by dynamic in the future)
    $scope.breakdown = {
        distance: [{min: 0, max:100}, {min: 100, max:250}, {min: 250, max:500}, {min: 500, max:1000}, {min: 1000, max:2000}],
        price: [{min: 0, max:1}, {min: 1, max:2}, {min: 2, max:3}, {min: 3, max:5}]
    };

    // filter results by type
    $scope.filterByDistance = function(distance){
        return function(parking) {
           return (parking.distance.value >= distance.min && parking.distance.value < distance.max);
        } 
    };

    // filter results by type
    $scope.filterByPrice = function(price){
        return function(parking) {
           return (parking.rate.value >= price.min && parking.rate.value < price.max);
        } 
    };

    // set params for the locations request api
    var params = {
        vehicleType: session.get('vechicleType'),
        longitude: session.get('currentLocation').longitude,
        latitude: session.get('currentLocation').latitude
    };

    // hide/show loading state
    $scope.busy = true;

    // wait until page loads
    $timeout(function(){
        // remove header
        $ionicNavBarDelegate.showBar(false);
        // input placeholder
        $scope.placeholder = "Use my current location";
    });

    /**
     * query locations and redirect to listing page
     */
    $timeout(function(){
        locations.query(params).then(function() {
            $scope.parkingLocations = locations.getByDuration($scope.filter.duration);
        }, function(errors){
            // display error modal
            // !!! to be implemented
        }).finally(function(){
             // disable busy state
            //$scope.busy = false;
            $ionicNavBarDelegate.showBar(true);
        });
    }, 0);

    /** 
     * watch duration and filter results
     */
    $scope.$watch('filter.duration', function(newVal, oldVal){
        if (oldVal !== newVal) {
            $scope.parkingLocations = locations.getByDuration($scope.filter.duration);
        }
    });
})

// detail Page
.controller('LocationCtrl', function($scope, $stateParams, $timeout, $http, locations, session, settings) {
    // get parking object
    $scope.parking = locations.getById($stateParams.id);

    // clear chosen Location
    session.set('chosenLocation', {});

    /**
     * select Pariking
     * set ne timestamp and chosen location
     * send request ot server to save user selection
     */
    $scope.selectParking = function(){
        // set parkedAt timestamp
        session.set('parkedAt', moment());
        var chosenLocation = {
            longitude: $scope.parking.location.longitude,
            latitude: $scope.parking.location.latitude
        };
        var params = {
            vechicleType: session.get("vechicleType"), 
            longitude: chosenLocation.longitude,
            latitude: chosenLocation.latitude,
        };
        session.set('chosenLocation', chosenLocation);
        $http.post(settings.getApiUrl() + 'locations/'+$stateParams.id+'/parked', params).then(function(){
            // redirect to complete page
            $scope.goTo('complete', {id: $stateParams.id});
        });
        
    }
})

// complete Page
.controller('CompleteCtrl', function($scope, $timeout, $stateParams, $http, session, settings, navigation) {
    // set the session timestamp on when the user has last time parked
    var parkedAt = moment(session.get('parkedAt'));
    var epxpiryPeriod = 60; //mins
    $scope.isDeviceLocated = false; // hide tame to my car btn initialy

    // check if we can locate the device
    settings.setCurrentLocation().then(function(){
        if (settings.isDeviceLocated()) {
            $scope.isDeviceLocated = true;
        }
    });
    
    /**
     * on "Park me again" check the time and either call service unparked or redirect to 
     * locations list page 
     * moment() is now
     * parkedAt is time when selected the car park
     */
    $scope.parkMeAgain = function(){
        if (moment() < parkedAt.add(epxpiryPeriod, 'm')) {
            var params = {
                vechicleType: session.get("vechicleType"), 
                longitude: session.get("chosenLocation").longitude,
                latitude: session.get("chosenLocation").latitude,
            };
            // this means user didn't find cark park space ans is looking fo new one
            $http.post(settings.getApiUrl() + 'locations/'+$stateParams.id+'/unparked', params);
        } 
        // this means that user parked the car
        $scope.goTo('locations'); 
    };

    /**
     * take me to my car
     */
    $scope.takeMeToMyCar = function(){
        navigation.go(true); // true means reverse
    };

    /**
     * redirect user to the satnav :)
     */
    //navigation.go(false); // false means from current location to the selected parking

})

// about
.controller('AboutCtrl', function($scope) {})
;