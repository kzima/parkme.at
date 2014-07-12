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

    // Form data for the login modal
    // $scope.loginData = {};

    // Create the login modal that we will use later
    /*  $ionicModal.fromTemplateUrl('templates/login.html', {
        scope: $scope
      }).then(function(modal) {
        $scope.modal = modal;
      });*/

    // Triggered in the login modal to close it
    $scope.closeLogin = function() {
            $scope.modal.hide();
        },

        // Open the login modal
        $scope.login = function() {
            $scope.modal.show();
        };

    // Perform the login action when the user submits the login form
    $scope.doLogin = function() {
        console.log('Doing login', $scope.loginData);

        // Simulate a login delay. Remove this and replace with your login
        // code if using a login system
        $timeout(function() {
            $scope.closeLogin();
        }, 1000);
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
    $scope.locations = [{
        title: 'Robertson St',
        id: 1
    }, {
        title: 'Ann St',
        id: 2
    }, {
        title: 'Dubstep St',
        id: 3
    }, {
        title: 'Indie St',
        id: 4
    }, {
        title: 'Rap St',
        id: 5
    }, {
        title: 'Cowbell St',
        id: 6
    }];

    // set params for the locations request api
    var params = {
        vehicleType: session.get('vechicleType'),
        longitude: session.get('currentLocation').longitude,
        latitude: session.get('currentLocation').latitude
    };

    // hide/show loading state
    $scope.busy = true;
    $ionicNavBarDelegate.showBar(false);

    $timeout(function(){
        $ionicNavBarDelegate.showBar(false);
    });

    /**
     * query locations and redirect to listing page
     */
    $timeout(function(){

        locations.query(params).then(function(data) {
            $scope.locations = locations.get();
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