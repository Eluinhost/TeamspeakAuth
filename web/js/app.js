angular.module('teamspeakAuthApp', ['mm.foundation', 'ui.router', 'ngResource'])

/*************************************
 *  Configure the application routes *
 *************************************/
    .config(function($stateProvider, $urlRouterProvider) {

        $stateProvider

            .state('index', {
                url: '/',
                templateUrl: 'partials/index'
            })

            .state('teamspeak_request', {
                url: '/teamspeak_request',
                templateUrl: 'partials/teamspeakrequest'
            })

        // catch all route
        // send users to the form page
        $urlRouterProvider.otherwise('/');
    })

/************************
 *  Add our controllers *
 ************************/
    .controller('LatestAuthsCtrl', ['$scope', 'LatestAuthsService', function ($scope, LatestAuthsService) {
        $scope.latest = LatestAuthsService.query();
    }])

/***************************************
 *  Configure the application services *
 ***************************************/
    .factory('LatestAuthsService', ['$resource', function($resource){
        var URL = NgRouting.generateResourceUrl('api_v1_authentications_all');
        return $resource( URL, {_format: 'json'});
    }])

    .factory('RequestTeamspeakCodeService', ['$resource', function($resource) {
        var URL = NgRouting.generateResourceUrl('api_v1_teamspeak_code_request');
        return $resource( URL, {_format: 'json'}, {'update': { method:'PUT'}});
    }])

/*****************************************
 *  Configure the application directives *
 *****************************************/

    .directive('tsUuidFetcher', function() {
        return {
            restrict: 'AE',
            templateUrl: 'partials/teamspeakUUIDFetcher',
            controller: ['$scope', 'RequestTeamspeakCodeService', function($scope, RequestTeamspeakCodeService) {
                $scope.requestCode = function() {
                    console.log($scope);
                    $scope.user = RequestTeamspeakCodeService.update({username: $scope.request_nick});
                }
            }]
        };
    });


