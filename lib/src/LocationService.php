<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

class LocationService
{
  
  public function getAllLocations()
  {
    $locations = R::findAll('location', ' ORDER BY name ASC ');
    return $locations;
  }
  
  public function getLocationByID($id)
  {
    $location = R::load('location', $id);
    return $location;
  }
  
  public function deleteLocationByID($id)
  {
    $location = R::load('location', $id);
    if($location->id == 0) {
      $response = array(
        'status' => 'error',
        'message' => 'That location not found.'
      );
    } else {
      R::trash($location);
      $response = array(
        'status' => 'success',
        'message' => 'Deleted: '.$location->name
      );
    }
    return $response;
  }
  
  public function processLocation($location)
  {
    
    $formErrors = array();

    if ($location["name"] == '') {
      $formErrors[] = "Name is required.";
    }

    if ($location["address"] == '') {
      $formErrors[] = "Address is required.";
    }

    // if location id set, should be one returned bean, if new location should be zero returned beans
    $testCompare = (isset($location["id"]) ? 1 : 0);
    
    $nameTest = R::find('location', ' name = ? ', array($location["name"]));
    if (count($nameTest) > $testCompare) {
      $formErrors[] = "That name is already in use.";
    }

    $addressTest = R::find('location', ' address = ? ', array($location["address"]));
    if (count($addressTest) > $testCompare) {
      $formErrors[] = "That address is already in use.";
    }
    
    $loc = (isset($location["id"]) ? R::load('location', $location["id"]) : R::dispense('location'));
    
    if (empty($formErrors)) {
      $loc->name = $location["name"];
      $loc->address = $location["address"];
      $loc->lat = $location["lat"];
      $loc->lng = $location["lng"];
      $loc->radius = ($location["radius"] > 0 ? $location["radius"] : 150);
      $loc->favorite = (isset($location["favorite"]) ? true : false);
      $loc_id = R::store($loc);
    }
    
    return array('formErrors' => $formErrors, 'location' => $loc);
  
  }
  
}

