angular.module('parkme.controllers', [])

.controller('AppCtrl', function($scope, $ionicModal, $timeout) {
  // Form data for the login modal
  $scope.loginData = {};

  // Create the login modal that we will use later
  $ionicModal.fromTemplateUrl('templates/login.html', {
    scope: $scope
  }).then(function(modal) {
    $scope.modal = modal;
  });

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
.controller('HomeCtrl', function($scope, $stateParams) {
})

// list of locations
.controller('LocationsCtrl', function($scope) {
  $scope.locations = [
    { title: 'Robertson St', id: 1 },
    { title: 'Ann St', id: 2 },
    { title: 'Dubstep St', id: 3 },
    { title: 'Indie St', id: 4 },
    { title: 'Rap St', id: 5 },
    { title: 'Cowbell St', id: 6 }
  ];
})

// detail Page
.controller('LocationCtrl', function($scope, $stateParams) {
})

// complete Page
.controller('CompleteCtrl', function($scope, $stateParams) {
})

// about
.controller('AboutCtrl', function($scope, $stateParams) {
})
