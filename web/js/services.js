var teampseakAuthServices = angular.module('teamspeakAuthServices', ['ngResource']);

teampseakAuthServices.factory('LatestAuthsService', ['$resource',
    function($resource){
        return $resource('/latest');
    }]);