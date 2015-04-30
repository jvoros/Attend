<?
$postVars = $app->request->post();

$location = R::load('location', $id);
if ($location->id == 0) {
  echo ajaxRespond('error', 'Location not found');
  $app->stop();
}

if ($postVars["name"] == '') {
  echo ajaxRespond('error', 'Location name is required');
  $app->stop();
}

if ($postVars['address'] == '') {
  echo ajaxRespond('error', 'Location address is required');
  $app->stop();
}

$location->name = $postVars["name"];
$location->address = $postVars["address"];
$location->lat = $postVars["lat"];
$location->lng = $postVars["lng"];
$location->radius = $postVars["radius"];
$location->favorite = $postVars["favorite"];
$loc_id = R::store($location);

if (isset($loc_id)) {
  echo ajaxRespond('success', 'Location updated');
} else {
  echo ajaxRespond('error', 'Unable to save updates');
}

