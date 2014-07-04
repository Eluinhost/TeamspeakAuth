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

    .directive('tsUuidFetcher', ['RequestTeamspeakCodeService', function(RequestTeamspeakCodeService) {
        return {
            restrict: 'AE', //allow attribute or element
            scope: {}, //isolate the scope
            templateUrl: 'partials/teamspeakUUIDFetcher',
            link: function($scope, $element, attr) { //called after DOM ready
                $scope.teamspeak_details = null; //setup default values
                $scope.teamspeak_stage = "start";
                $scope.errors = [];

                $scope.addError = function(msg) {
                    $scope.errors.push({msg: msg, type: 'alert round'});
                };

                $scope.removeError = function(index) {
                    $scope.errors.splice(index, 1);
                };

                $scope.clearErrors = function() {
                    $scope.errors = [];
                };

                $scope.requestCode = function() {   //make new function in the scope
                    $scope.teamspeak_details = null;    //clear existing values
                    $scope.teamspeak_stage = "loading";

                    $scope.clearErrors();

                    RequestTeamspeakCodeService.update(
                        {},
                        {username: $scope.request_nick},
                        function(data) {
                            $scope.teamspeak_details = data;  //update with new values
                            $scope.teamspeak_stage = "complete";
                        },
                        function(error) {
                            if(typeof error.data != 'undefined' && error.data.length > 0 ) {
                                angular.forEach(error.data, function(element){
                                    if(typeof element.message != 'undefined') {
                                        $scope.addError(element.message);
                                    }
                                });
                            }
                            if($scope.errors.length == 0) {
                                $scope.addError('Unknown Error Occurred');
                            }
                            $scope.teamspeak_stage = "start";
                        }
                    );
                }
            }
        };
    }]);


