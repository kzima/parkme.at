// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'parkme' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'parkme.controllers' is found in controllers.js
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

        .state('app', {
        url: "/app",
        abstract: true,
        templateUrl: "templates/menu.html",
        controller: 'AppCtrl'
    })

    /**
     * home page
     */
    .state('app.home', {
        url: "/home",
        views: {
            'menuContent': {
                templateUrl: "templates/home.html",
                controller: 'HomeCtrl'
            }
        }
    })

    /**
     * A list of locations
     */
    .state('app.locations', {
        url: "/locations",
        views: {
            'menuContent': {
                templateUrl: "templates/locations.html",
                controller: 'LocationsCtrl'
            }
        }
    })

    /**
     * Detail location page
     */
    .state('app.location', {
        url: "/locations/:id",
        views: {
            'menuContent': {
                templateUrl: "templates/location.html",
                controller: 'LocationCtrl'
            }
        }
    })

    /**
     * complete page (successful/unsuccessful)
     */
    .state('app.complete', {
        url: "/complete",
        views: {
            'menuContent': {
                templateUrl: "templates/complete.html",
                controller: 'CompleteCtrl'
            }
        }
    })

    /**
     * About app info
     */
    .state('app.about', {
        url: "/about",
        views: {
            'menuContent': {
                templateUrl: "templates/about.html",
                controller: 'AboutCtrl'
            }
        }
    });

    // if none of the above states are matched, use this as the fallback
    $urlRouterProvider.otherwise('/app/home');
});
