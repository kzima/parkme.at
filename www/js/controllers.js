angular.module('parkme.controllers', [])

.controller('AppCtrl', function($scope, $ionicModal, $timeout, $state, settings) {

    // initalize global settings
    settings.init();

    /**
     * handle redirect across app
     * @param  {Object} state
     * @param  {Object} params
     */
    $scope.goTo = function(state, params){
        $state.go(state, params, { reload: true });
    };

})

// home page/landing page
.controller('HomeCtrl', function($scope, session, settings) {

    // set the location for current device
    settings.setCurrentLocation();

    // Vechicle type (car|motorcycle)
    $scope.params = {
        vechicleType: "car", 
        currentLocation: {
            longitude: 0,
            latitude: 0
        }
    };

    // update vehicle data in the session 
    $scope.$watch('params.vechicleType', function(){
        session.set('vechicleType', $scope.params.vechicleType);
    }, true);

    // update vehicle data in the session 
    $scope.$watch('params.currentLocation', function(){
        session.set('currentLocation', $scope.params.currentLocation);
    }, true);

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
        distance: [{min: 0, max:100}, {min: 100, max:250}, {min: 250, max:500}, {min: 500, max:1000}],
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
    $timeout(function(){
        $ionicNavBarDelegate.showBar(false);
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
            $scope.busy = false;
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
.controller('LocationCtrl', function($scope, $stateParams, locations) {
    $scope.parking = locations.getById($stateParams.id);
})

// complete Page
.controller('CompleteCtrl', function($scope) {})

// about
.controller('AboutCtrl', function($scope) {})
;