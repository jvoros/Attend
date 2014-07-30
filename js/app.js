var baseURL = 'http://localhost/Sites/DHREM/Attend';

var app = angular.module('app', []);

// CONTROLLER
app.controller('mainController', ['$scope', 'dataFactory', function ($scope, dataFactory) {
    
    // $scope variables
    $scope.status;
    $scope.error;
    
    $scope.user;
    $scope.userLocation = 'other';
    $scope.conf;
    scope.checkin;
    
    $scope.map;
    $scope.locPoly;
    $scope.remPoly;
    //$scope.getLocation;

    $scope.session;
    
    // User controls
    function getUser() {
        dataFactory.getUser()
        .success(function (data) { $scope.user = data; })
        .error(function (error) { $scope.status = 'Error loading user'; });
    }
    
    getUser();
    
    // Conference controls
    function getConfByDate(date) {
        dataFactory.getConferenceByDate(date)
        .success(function (data) { $scope.conf = data; })
        .error(function (error) { $scope.status = 'Error loading conference'; });
    };
    
    getConfByDate(getToday());
    
    // Checkin controls
    function getCheckinToday() {
        dataFactory.getCheckinToday()
        .success(function (data) { $scope.checkin = data; })
        .error(function (error) { $scope.status = 'Error loading checkin'; });
    };
    
    getCheckinToday();
    
    $scope.checkIn = function() {
        dataFactory.checkIn()
        .success(function (data) { $scope.checkin = data; })
        .error(function(data) { $scope.status = 'Error loading checkin'; });
    };
    
    // Utility controls
    $scope.getSession = function() {
        dataFactory.getSession()
        .success(function (data) { $scope.session = JSON.stringify(data, null, 4); console.log('fired getSession'); })
        .error(function (data) { $scope.status = 'Error loading session'; });
    };
    
    // GOOGLE MAPS FUNCTIONS
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
    
    // create polys from coords, put on map
    function showPolys(loc, rem) {
        $scope.locPoly = coordsPoly(loc.coords);
        $scope.remPoly = coordsPoly(rem.coords);
        
        $scope.locPoly.setMap($scope.map);
        $scope.remPoly.setMap($scope.map);
    };
    
    // check user location
    function checkLoc() {
        if (google.maps.geometry.poly.containsLocation($scope.myLoc, $scope.locPoly)){
            return 'primary';
        } else if (google.maps.geometry.poly.containsLocation($scope.myLoc, $scope.remPoly)) { 
            return 'remote';
        } else { 
            return 'other';
        }
    };
    
    // init the map, load markers, check location
    function showPosition(p){
        
        // google maps point for users location
        $scope.myLoc = new google.maps.LatLng(p.coords.latitude, p.coords.longitude);
        
        var mapOptions = {
          center: $scope.myLoc,
          zoom: 16
        };
        
        // load map into #map-canvas
        $scope.map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        
        // add location marker
        var marker = new google.maps.Marker({
            position: $scope.myLoc, 
            map: $scope.map
        });
        
        // if conference day load locations then check user location
        // http://jimhoskins.com/2012/12/17/angularjs-and-apply.html
        if ($scope.conf) {
            showPolys($scope.conf.location, $scope.conf.remote);
            $scope.userLocation = checkLoc();
            $scope.$apply();
        }

    } //end showPosition()
    
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
    function getLocation(){
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        }
        else {
            $scope.error = "Geolocation is not supported by this browser.";
        }
    }
 
    getLocation();
    
}]);

// FACTORY
// Factory to handle communication with the backend
// http://weblogs.asp.net/dwahlin/using-an-angularjs-factory-to-interact-with-a-restful-service
app.factory('dataFactory', ['$http', function($http){
    
    var dataFactory = {};
    
    // User methods
    dataFactory.getUser = function() {
        return $http.get(baseURL + '/users/current');
    };
    
    // Conference methods
    dataFactory.getConferenceByDate = function (date) {
        return $http.get(baseURL + '/conferences/date/' + date);
    };
    
    // Checkin methods
    dataFactory.getCheckinToday = function() {
        return $http.get(baseURL + '/checkins/today');
    };
    
    dataFactory.checkIn = function() {
        return $http.post(baseURL + '/checkins');
    };
    
    dataFactory.checkOut = function(id) {
        return $http.put(baseURL + '/checkins');
    };
    
    // Utility methods
    dataFactory.getSession = function() {
        return $http.get(baseURL + '/getsession');
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

