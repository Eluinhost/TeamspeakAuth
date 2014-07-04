angular.module('teamspeakAuthApp', ['mm.foundation', 'ui.router', 'ngResource', 'cgBusy'])

/*************************************
 *  Configure the application routes *
 *************************************/
    .config(function($stateProvider, $urlRouterProvider) {

        $stateProvider

            .state('index', {
                url: '/',
                templateUrl: 'partials/index'
            })

            .state('verify', {
                url: '/verify',
                templateUrl: 'partials/verify',
                controller: 'VerifyCtrl'
            });

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

    .controller('VerifyCtrl', ['$scope', function($scope) {
        $scope.teamspeakDetails = null;
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

    .factory('VerifyAccountService', ['$resource', function($resource) {
        var URL = NgRouting.generateResourceUrl('api_v1_authentications_new');
        return $resource( URL, {_format: 'json'});
    }])

/*****************************************
 *  Configure the application directives *
 *****************************************/

    .directive('tsUuidFetcher', ['RequestTeamspeakCodeService', function(RequestTeamspeakCodeService) {
        return {
            restrict: 'AE', //allow attribute or element
            scope: {
                teamspeakDetails: '='
            },
            templateUrl: 'partials/teamspeakUUIDFetcher',
            link: function($scope, $element, attr) { //called after DOM ready

                //setup errors
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

                $scope.resetAccount = function() {
                    $scope.teamspeakDetails = null;
                };

                //what to do when code is requested
                $scope.requestCode = function() {
                    //clear errors any old data
                    $scope.resetAccount();
                    $scope.clearErrors();

                    //make sure we actually have a nickname
                    if($scope.request_nick == null || $scope.request_nick.length == 0) {
                        $scope.addError('Must provide a Teamspeak nickname');
                        return;
                    }

                    //set the promise for the busy graphic
                    $scope.promise = RequestTeamspeakCodeService.update(
                        {},
                        {username: $scope.request_nick},
                        function(data) {
                            $scope.teamspeakDetails = data;  //update with new values
                        },
                        function(error) {
                            //check for errors
                            if(typeof error.data != 'undefined' && error.data.length > 0 ) {
                                angular.forEach(error.data, function(element){
                                    if(typeof element.message != 'undefined') {
                                        $scope.addError(element.message);
                                    }
                                });
                            }
                            //if none in message set a default one
                            if($scope.errors.length == 0) {
                                $scope.addError('Unknown Error Occurred');
                            }
                        }
                    );
                }
            }
        };
    }])

    .directive('accountVerification', ['VerifyAccountService', function(VerifyAccountService) {
        return {
            restrict: 'AE',
            scope: {
                teamspeakDetails: '='
            },
            templateUrl: 'partials/accountVerification',
            link: function($scope, $element, attr) {
                //setup errors
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

                $scope.resetAccount = function() {
                    $scope.teamspeakDetails = null;
                };

                $scope.verifyCodes = function() {
                    if($scope.teamspeakCode == null || $scope.teamspeakCode.length == 0) {
                        $scope.addError('Must supply the provided Teamspeak code');
                        return;
                    }

                    if($scope.minecraftCode == null || $scope.minecraftCode.length == 0) {
                        $scope.addError('Must supply the provided Minecraft code');
                        return;
                    }

                    if($scope.minecraftName == null || $scope.minecraftName.length == 0) {
                        $scope.addError('Must supply your Minecraft username');
                        return;
                    }

                    $scope.promise = VerifyAccountService.save(
                        {},
                        {
                            ts_uuid: $scope.teamspeakDetails.uuid,
                            ts_code: $scope.teamspeakCode,
                            mc_uuid: $scope.minecraftName,
                            mc_code: $scope.minecraftCode
                        },
                        function(data) {
                            //empty response on success
                        },
                        function(error) {
                            //check for errors
                            if(typeof error.data != 'undefined' && error.data.length > 0 ) {
                                angular.forEach(error.data, function(element){
                                    if(typeof element.message != 'undefined') {
                                        $scope.addError(element.message);
                                    }
                                });
                            }
                            //if none in message set a default one
                            if($scope.errors.length == 0) {
                                $scope.addError('Unknown Error Occurred');
                            }
                        }
                    );
                }
            }
        }
    }])

    //directive with keybind="expression()" key=13
    .directive('keybind', function() {
        return function(scope, element, attrs) {
            element.bind("keydown keypress", function(event) {
                if(event.which === Number(attrs.key)) {
                    scope.$apply(function(){
                        scope.$eval(attrs.keybind);
                    });

                    event.preventDefault();
                }
            });
        };
    });


