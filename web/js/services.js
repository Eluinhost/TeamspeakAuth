var teampseakAuthServices = angular.module('teamspeakAuthServices', ['ngResource']);

teampseakAuthServices.factory('LatestAuthsService', ['$resource',
    function($resource){
        var URL = NgRouting.generateResourceUrl('api_v1_authentications_all');
        return $resource( URL, {_format: 'json'});
    }]);

teampseakAuthServices.factory('RequestTeamspeakCodeService', ['$resource',
    function($resource) {
        var URL = NgRouting.generateResourceUrl('api_v1_teamspeak_code_request');
        return $resource( URL, {_format: 'json'}, {'update': { method:'PUT'}});
    }
]);