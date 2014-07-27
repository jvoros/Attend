var baseURL = 'http://localhost/Sites/DHREM/Attend';

var app = angular.module('app', []);

// Main Controller
app.controller('mainController', function($scope){
    
    
    
    // Google Maps Business
    $scope.showPosition = function(p){
        
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
    
    }
    
    // check for geolocation and initialize
    $scope.getLocation = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition($scope.showPosition, $scope.showError);
        }
        else {
            $scope.error = "Geolocation is not supported by this browser.";
        }
    }
 
    $scope.getLocation();
    
});

