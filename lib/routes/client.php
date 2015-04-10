<?
// MIDDLEWARE
// verify user logged in
$verifyLogin = function() {
  return function() {
    if (!isset($_SESSION['user'])) {
      $app = \Slim\Slim::getInstance();
      $app->redirect(BASE_URL . '/loginform');
    }
  };
};

// verify user logged in for ajax request
$verifyAjax = function() {
  return function() {
    if (!isset($_SESSION['user'])) {
      echo "<a href='".BASE_URL."/loginform'>Please log in for access</a>";
      $app = \Slim\Slim::getInstance();
      $app->stop(403, 'You shall not pass!');
    }
  };
};

// USER CLIENT ROUTES
$app->group('/user', $verifyLogin(), function() use($app) {
  
  // HOME
  $app->get('/', function() use($app) {
    require_once 'lib/controllers/home.php';
    $app->render('home.html', array('data' => $data));
  });
  
  // CONFERENCE
  $app->get('/conference/:id', function($id) use($app) {  
    $conf = R::load('conference', $id);
    $conf = getConferenceAsArray($conf);
    $data = array(
      'conf'        => $conf
    );
    $app->render('conference.html', array('data' => $data));
  });

});

// AJAX CLIENT ROUTES
$app->group('/ajax', $verifyAjax(), function() use($app) {

  // CONFERENCE DETAILS
  $app->post('/conference/:conf_id', function($conf_id) use($app) {
    require 'lib/controllers/conference-control.php';
    $app->render('_conference-control.html', array('data' => $data));
  });
  
});
