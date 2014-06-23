var teamspeakAuthControllers = angular.module('teamspeakAuthControllers', []);

teamspeakAuthControllers.controller('LatestAuthsCtrl', ['$scope', 'LatestAuthsService', function ($scope, LatestAuthsService) {
    $scope.latest = LatestAuthsService.query();
}]);