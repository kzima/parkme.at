angular.module('parkme', ['ionic', 'parkme.controllers'])

.run(function($ionicPlatform) {
    $ionicPlatform.ready(function() {
        // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
        // for form inputs)
        if (window.cordova && window.cordova.plugins.Keyboard) {
            cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
        }
        if (window.StatusBar) {
            // org.apache.cordova.statusbar required
            StatusBar.styleDefault();
        }
    });
})

.config(function($stateProvider, $urlRouterProvider) {
    $stateProvider

    /**
     * home page
     */
    .state('home', {
        url: "/home",
        templateUrl: "templates/home.html",
        controller: 'HomeCtrl'
    })

    /**
     * A list of locations
     */
    .state('locations', {
        url: "/locations",
        templateUrl: "templates/locations.html",
        controller: 'LocationsCtrl'
    })

    /**
     * Detail location page
     */
    .state('location', {
        url: "/locations/:id",
        templateUrl: "templates/location.html",
        controller: 'LocationCtrl'
    })

    /**
     * complete page (successful/unsuccessful)
     */
    .state('complete', {
        url: "/complete",
        templateUrl: "templates/complete.html",
        controller: 'CompleteCtrl'
    })

    /**
     * About app info
     */
    .state('about', {
        url: "/about",
        templateUrl: "templates/about.html",
        controller: 'AboutCtrl'
    });

    // if none of the above states are matched, use this as the fallback
    $urlRouterProvider.otherwise('/home');
});
