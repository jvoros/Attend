<?
$postVars = $app->request->post();
$formErrors = array();

if ($postVars["name"] == '') {
  $formErrors[] = "Name is required.";
}

if ($postVars['address'] == '') {
  $formErrors[] = "Address is required.";
}

$nameTest = R::findOne('location', ' name = ? ', array($postVars["name"]));
if (isset($nameTest)) {
  $formErrors[] = "That name is already in use.";
}

$addressTest = R::findOne('location', ' address = ? ', array($postVars["address"]));
if (isset($addressTest)){
  $formErrors[] = "That address is already in use.";
}

if (empty($formErrors)) {
  $location = R::dispense('location');
  $location->name = $postVars["name"];
  $location->address = $postVars["address"];
  $location->lat = $postVars["lat"];
  $location->lng = $postVars["lng"];
  $location->radius = $postVars["radius"];
  $location->favorite = $postVars["favorite"];
  $loc_id = R::store($location);
}

if (isset($loc_id)) {
  $app->flash('message', 'Added location: '.$location->name);
  $app->redirect(BASE_URL . '/admin/locations');
} else {
  $app->flash('formErrors', $formErrors);
  $app->render('admin/location.html');
}

