<?
/*********************************
    HOUSEKEEPING
*********************************/

session_start();

// php housekeeping
date_default_timezone_set('America/Denver');

// composer bootstrapping
require 'vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:dbase.sqlite');
R::freeze(true);

// app wide utility functions and constants
// also defined in app.js 
define('BASE_URL', 'http://localhost/Sites/DHREM/Attend'); 


/*********************************
    INITIALIZE SLIM & COMPONENTS
*********************************/

$app = new \Slim\Slim(array(
    'templates.path' => 'templates',
));

// prepare Twig view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true,
    'debug' => true
);

// give Twig templates access to global variables, dump() function, Slim View Extras
$twig = $app->view->getEnvironment();
$twig->addGlobal('base_url', BASE_URL);
$twig->addGlobal('session', $_SESSION);

$app->view->getEnvironment()->addExtension(new \Twig_Extension_Debug());
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension(), new \Twig_Extension_Debug());

// Google PHP Library
$client = new Google_Client();
$client->setApplicationName('Attend');
$client->setClientId('459801839286-j103ceme00kacpbrk19nihn7r3l2icme.apps.googleusercontent.com');
$client->setClientSecret('C95vXMePXkVXKgtuQr8lWCqk');
$client->setRedirectUri(BASE_URL . '/login');
$client->setScopes(array(
  'https://www.googleapis.com/auth/userinfo.email',
  'https://www.googleapis.com/auth/userinfo.profile',
));
$app->client = $client;

/*********************************
    UTILITY FUNCTIONS
*********************************/

function getCheckinStatus($conf) {
    $checkinToday = R::findOne('checkin', 
                               ' user_id = :user AND conference_id = :conf ', 
                               array(':user' => $_SESSION['user']['id'], ':conf' => $conf));
    
    if($checkinToday) { return $checkinToday->export(); }
}

// HANDLE GOOGLE USER INFO
function processGoogleUser($guser) {
  
  //$_SESSION['guser'] = $guser;
  
  // check domain
  if ($guser['hd'] != 'denverem.org') {
    $app = \Slim\Slim::getInstance();
    $app->flash('error', 'You must use your @denverem.org email address.');
    $app->redirect(BASE_URL . '/logout');
    exit;
  }
  
  // query database
  $user = R::findOne('user', ' email = ? ', [$guser['email']]);
  
  // check if new user or new name
  if (is_null($user)) {
    $user->fname   = $guser['givenName'];
    $user->lname   = $guser['familyName'];
    $user->email   = $guser['email'];
    $user->role    = '2';
  } elseif ($user->fname != $guser['givenName'] || $user->lname != $guser['familyName']) {
    $user->fname   = $guser['givenName'];
    $user->lname   = $guser['familyName'];
  }
  
  // update last visit, store
  $user->last = date("Y-m-d H:i:s");
  $user_id = R::store($user);
  
  // save in session
  $_SESSION['user'] = $user->export();
  $_SESSION['user']['role_name'] = $user->role->name;
  
}

// ROUTE AUTHORIZATION
// by role
$authorizedRole = function($role_name = 'user') {
  return function() use ($role_name) {
    if ($_SESSION['user']['role_name'] != $role_name) {
    $app = \Slim\Slim::getInstance();
    $app->flash('error', 'You do not have access to that resource.');
    $app->redirect(BASE_URL);
    }
  };
};

// verify user logged in
$verifyLogin = function() {
  return function() {
    if (!isset($_SESSION['user'])) {
      $app = \Slim\Slim::getInstance();
      $app->redirect(BASE_URL . '/loginform');
    }
  };
};

/*********************************
    ROUTES
*********************************/

// GOOGLE OAUTH LOGIN
// http://www.ibm.com/developerworks/library/mo-php-todolist-app/
// https://developers.google.com/api-client-library/php/guide/aaa_oauth2_web
// http://phppot.com/php/php-google-oauth-login/

$app->get('/login', function() use($app){    
  // handle redirect from Google with code as URL parameter
  if (isset($_GET['code'])) {
    $app->client->authenticate($_GET['code']);
    $app->client->getAccessToken();
    $service = new Google_Service_Oauth2($app->client);
    $user = $service->userinfo->get();
    processGoogleUser($user); // utility function
    $app->redirect(BASE_URL);
  } else {
    $authUrl = $app->client->createAuthUrl();
    $app->redirect($authUrl);
  }
});

$app->get('/loginform', function() use ($app) {
  $app->render('loginform.html');
});

$app->get('/logout', function () use ($app) {
  $app->flashKeep();
  session_unset();
  $app->client->revokeToken();
  $app->redirect(BASE_URL . '/loginform');
});

// HOME PAGE
$app->get('/', $verifyLogin(), function() use($app) {
  require 'lib/homeController.php';
  $app->render('home.html', array('data' => $data));
});

// CLIENT ROUTES

// TEST ROUTES
$app->get('/getsession', function() use ($app) {
  header("Content-Type: application/json");
  echo json_encode($_SESSION);
});

$app->get('/testconf', function() use($app) {
  
});

// USER ROUTES
$app->get('/users/current', function() use($app) {
    
    echo json_encode($_SESSION['user'], JSON_PRETTY_PRINT);
    
});

// CONFERENCE ROUTES
// GET conference BY date (Y-m-d)
$app->get('/conferences/date/:date', function($date) use($app) {
    
    $conf = R::findOne('conference', ' day = ? ', array(date($date)));
    if($conf) { 
        R::preload($conf, array('location'=>'location', 'remote'=>'location'));
        $data = $conf->export();
    } else {
        $data = null;
    }
    $_SESSION['conf'] = $data;
    echo json_encode($data, JSON_PRETTY_PRINT);    
    
});

// CHECKIN ROUTES
$app->get('/checkins/today', function() use($app) {
    $checkinToday = R::findOne('checkin', ' user_id = :user AND conference_id = :conf ', 
                               array(':user' => $_SESSION['user']['id'], ':conf' => $_SESSION['conf']['id']));
    
    if($checkinToday) { 
        $_SESSION['checkin'] = $checkin;
        echo json_encode($checkinToday->export()); 
    }        
});

$app->post('/checkin', function() use($app) {
    $checkin = R::dispense('checkin');
    $checkin->conference_id = $_SESSION['conf']['id'];
    $checkin->user_id = $_SESSION['user']['id'];
    $checkin->in = date("H:i:s");
    $checkin_id = R::store($checkin);
    $checkin = $checkin->export();
    
    $_SESSION['checkin'] = $checkin;
    echo json_encode($checkin);
});

$app->post('/checkout', function() use($app) {
    $checkin = R::load('checkin', $_SESSION['checkin']['id']);
    if($checkin) {
        $checkin->out = date("H:i:s");
        $checkin_id = R::store($checkin);
         $checkin = $checkin->export();
    
        $_SESSION['checkin'] = $checkin;
        echo json_encode($checkin);
    }
});

// handle time on server for consistency
$app->put('/checkout/:id', function($id) use($app) {
    $checkin = R::load('checkin', $id);
    $checkin->out = date("H:i:s");
    $checkin_id = R::store($checkin);
    $checkin = $checkin->export();
    
    $_SESSION['checkin'] = $checkin;    
    echo json_encode($checkin);
});

// GET SESSION
$app->get('/getsession', function() use($app) {
    echo json_encode($_SESSION, JSON_PRETTY_PRINT);
});

$app->get('/ajax-logout', function() use($app) {
    $_SESSION = array();
});

// CHECKIN
$app->post('/checkin', function() use($app) {

    // confirm no prior checkin, create new checkin
    if(empty($_SESSION['checkin']['id'])) {
        $check                = R::dispense('checkin');
        $check->user_id       = $_SESSION['user']['id'];
        $check->conference_id = $_SESSION['conf']['id'];
        $check->in            = date("Y-m-d H:i:s");
        $check_id = R::store($check);
        
        $data['status'] = 'success';
        $data['msg'] = $check_id;
    
    } else {
        $data['status'] = 'error';
        $data['msg'] = 'You have already checked in for today';
    }
    
    echo json_encode($data);   
    
});

// CHECKOUT
$app->post('/checkout', function() use($app) {
    
    // confirm prior checkin, add checkout time
    if(isset($_SESSION['checkin']['id'])) {
        $check = R::find('checkin', $_SESSION['checkin']['id']);
        $check->out = date("Y-m-d H:i:s");
        $check_id = R::store($check);
        
        $data['status'] = 'success';
        $data['msg'] = 'Total Hours: ';
    
    } else {
        $data['status'] = 'error';
        $data['msg'] = "You haven't checked in today or you have already checked out.";
    }
    
    echo json_encode($data);
       
});


/*********************************
    RUN
*********************************/

$app->run();

