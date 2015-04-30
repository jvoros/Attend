<?
// ADMIN ROUTES
$app->group('/admin', $app->auth->authorizedRole('admin'), function() use($app) {

  $app->get('/', function() use($app) {
    require_once 'lib/controllers/admin/home.php';
    $app->render('admin/home.html', array('data' => $data));
  });
  
  $app->get('/locations', function() use($app) {
    $locations = R::findAll('location', ' ORDER BY name ASC ');
    $faves = array();
    foreach($locations as $loc) {
      if ($loc->favorite == "on") { $faves[] = $loc; }
    }
    $app->render('admin/locations.html', array('locations' => $locations, 'faves' => $faves));
  });
  
  $app->get('/location', function() use($app) {
    $app->render('admin/location.html');
  });
  
  $app->post('/location', function() use($app) {
    require 'lib/controllers/admin/location-new.php';
  });
  
  $app->get('/location/:id', function($id) use($app) {
    $location = R::load('location', $id);
    if($location->id == 0) {
      $app->flash('error', 'No location found.');
    }
    $app->render('admin/location.html', array('location' => $location));
  });
  
  $app->put('/location/:id', function($id) use($app) {
    require 'lib/controllers/admin/location-update.php';
  });
  
  $app->get('/location/delete/:id', function($id) use($app) {
    $location = R::load('location', $id);
    if($location->id == 0) {
      $app->flash('error', 'No location found.');
    } else {
      R::trash($location);
      $app->flash('message', 'Deleted location: '.$location->name);
    }
    $app->redirect(BASE_URL . '/admin/locations');
  });

});