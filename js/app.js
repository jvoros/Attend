var baseURL = 'http://localhost/Sites/DHREM/Attend';

var app = angular.module('app', []);

// Main Controller
app.controller('mainController', ['$scope', 'dataFactory', function ($scope, dataFactory) {
    
    $scope.status = '';
    $scope.error = '';
    $scope.user = '';
    $scope.userLocation = null;
    $scope.conf = {};
    
    function getUser() {
        dataFactory.getUser()
            .success(function (data) { $scope.user = data; })
            .error(function (error) { $scope.status = 'Error loading user'; });
    }
    
    getUser();
    
    function getConfByDate(date) {
        dataFactory.getConferenceByDate(date)
            .success(function (data) { $scope.conf = data; })
            .error(function (error) { $scope.status = 'Error loading conference'; });
    };
    
    getConfByDate(getToday());
    
    // Google Maps Business
    // http://www.victorshi.com/blog/post/Use-Geolocation-API-with-Angularjs
    // http://jsfiddle.net/svigna/pc7Uu/
    
    // utility function to turn string of four coordinates (comma separated) into google maps polygon
    function coordsPoly(x_string) {
        var r = x_string.split(",");
        var coords = [
            new google.maps.LatLng(r[0], r[1]),
            new google.maps.LatLng(r[2], r[3]),
            new google.maps.LatLng(r[4], r[5]),
            new google.maps.LatLng(r[6], r[7])
        ]; 
        var poly = new google.maps.Polygon({
            paths: coords
        });
        return poly;
    };
    
    function showPosition(p){
        
        // google maps point for users location
        var myLoc = new google.maps.LatLng(p.coords.latitude, p.coords.longitude);
        
        var mapOptions = {
          center: myLoc,
          zoom: 16
        };
        
        // load map into #map-canvas
        var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        
        var marker = new google.maps.Marker({
            position: myLoc, 
            map: map,
        });
        
        // load locations
        var locationPoly = coordsPoly($scope.conf.location.coords);
        var remotePoly = coordsPoly($scope.conf.remote.coords);
        
        locationPoly.setMap(map);
        remotePoly.setMap(map);
        
        // check user location
        // http://jimhoskins.com/2012/12/17/angularjs-and-apply.html
        // $scope.$apply is key here
        if (google.maps.geometry.poly.containsLocation(myLoc, locationPoly)){
            $scope.userLocation = 'primary';
            $scope.$apply();
        } else if (google.maps.geometry.poly.containsLocation(myLoc, remotePoly)) { 
            $scope.userLocation = 'remote';
            $scope.$apply();
        } else { 
            $scope.userLocation = null;
            $scope.$apply();
        }

    }
    
    // error handling
    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                $scope.error = "User denied the request for Geolocation."
                break;
            case error.POSITION_UNAVAILABLE:
                $scope.error = "Location information is unavailable."
                break;
            case error.TIMEOUT:
                $scope.error = "The request to get user location timed out."
                break;
            case error.UNKNOWN_ERROR:
                $scope.error = "An unknown error occurred."
                break;
        }
        $scope.$apply();
    }
    
    // check for geolocation and initialize
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        }
        else {
            $scope.error = "Geolocation is not supported by this browser.";
        }
    }
 
    getLocation();
    
}]);

// Factory to handle communication with the backend
// http://weblogs.asp.net/dwahlin/using-an-angularjs-factory-to-interact-with-a-restful-service
app.factory('dataFactory', ['$http', function($http){
    
    var dataFactory = {};
    
    dataFactory.getUser = function() {
        return $http.get(baseURL + '/users/current');
    };
    
    
    dataFactory.getConferenceByDate = function (date) {
        return $http.get(baseURL + '/conferences/date/' + date);
    };
    
    dataFactory.getCheckinToday = function() {
        return $http.get(baseURL + '/checkins/today');
    };
    return dataFactory;
    
}]);

// Utility functions
// date Y-m-d
function getToday() {
    // GET CURRENT DATE
    var date = new Date();

    // GET YYYY, MM AND DD FROM THE DATE OBJECT
    var yyyy = date.getFullYear().toString();
    var mm = (date.getMonth()+1).toString();
    var dd  = date.getDate().toString();

    // CONVERT mm AND dd INTO chars
    var mmChars = mm.split('');
    var ddChars = dd.split('');

    // CONCAT THE STRINGS IN YYYY-MM-DD FORMAT
    var datestring = yyyy + '-' + (mmChars[1]?mm:"0"+mmChars[0]) + '-' + (ddChars[1]?dd:"0"+ddChars[0]);
    
    return datestring;

}

