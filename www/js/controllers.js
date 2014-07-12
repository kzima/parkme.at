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
        sort: 'nearest'
    };


    // distance/price breakdown (this could by dynamic in the future)
    $scope.breakdown = {
        distance: [100, 250, 500, 1],
        price: [0, 1, 3, 5]
    };

    // filter results by type
    $scope.filterByDistance = function(breakdown){
        return function(parkingLocation) {
           return parkingLocation < breakdown;
        } 
    };

    // filter results by type
    $scope.filterByPrice = function(breakdown){
        return function(parkingLocation) {
           return parkingLocation < breakdown;
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

        locations.query(params).then(function(data) {
            $scope.parkingLocations = locations.get();
        }, function(errors){
            // display error modal
            // !!! to be implemented
        }).finally(function(data){
             // disable busy state
            $scope.busy = false;
            $ionicNavBarDelegate.showBar(true);
        });
    }, 0);
})

// detail Page
.controller('LocationCtrl', function($scope, $stateParams) {

})

// complete Page
.controller('CompleteCtrl', function($scope) {})

// about
.controller('AboutCtrl', function($scope) {})
;