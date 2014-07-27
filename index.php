<?
/*********************************
    INITIALIZE COMPONENTS
*********************************/

// php housekeeping
session_cache_limiter(false);
session_start();
date_default_timezone_set('America/Denver');

// composer bootstrapping
require 'vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:dbase.sqlite');
R::freeze(true);

// app wide utility functions and constants
define('BASE_URL', 'http://localhost/Sites/DHREM/Attend'); // also defined in app.js 

// initialize Slim, use Twig to render views
$app = new \Slim\Slim(array(
    'templates.path' => 'templates',
));

// route middleware for authorization redirect
$auth = new AuthProtect($app);

// UTILITY FUNCTIONS

function getCheckinStatus($user, $conf) {
    $checkinToday = R::findOne('checkin', 
                               ' user_id = :user AND conference_id = :conf ', 
                               array(':user' => $user, ':conf' => $conf));
    
    if (empty($checkinToday)) {
        $checkin['status']  = 'none';
        $checkin['id']      = FALSE;
    } elseif (empty($checkinToday->out)){
        $checkin['status']  = 'in';
        $checkin['id']      = $checkinToday->id;
        $checkin['in']      = $checkinToday->in;
    } else {
        $checkin['status']  = 'out';
        $checkin['id']      = $checkinToday->id;
        $checkin['in']      = $checkinToday->in;
        $checkin['out']     = $checkinToday->out;
    }
    
    return $checkin;
}



/*********************************
    ROUTES
*********************************/

// HOME PAGE
$app->get('/', $auth->protect(), function() use($app) {
    
    // get conference dtails
    //$conf = getConferenceDetails();
    $_SESSION['conf'] = $conf;
    
    // if conference day, get checkin status
    if ($conf != 'none') {
        $_SESSION['user']['checkin'] = getCheckinStatus($_SESSION['user']['id'], $conf['id']);
    }
    
   // render
    $app->render('main.html');
    
});

// CONFERENCE ROUTES
// GET conference BY date (Y-m-d)
$app->get('/conferences/date/:date', $auth->protect(), function($date) use($app) {
    
    $conf = R::findOne('conference', ' day = ? ', array(date($date)));
    if($conf) { 
        R::preload($conf, array('location'=>'location', 'remote'=>'location'));
        $data = $conf->export();
    } else {
        $data = null;
    }
        
    echo json_encode($data, JSON_PRETTY_PRINT);    
    
});

// SET LOCATION
$app->post('/loc/:loc', function($loc) use($app) {
    // log location in session
    $_SESSION['user']['location'] = $loc;
    echo json_encode($loc);
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



// AUTHORIZATION HANDLING

$app->get('/login', function() use($app) {
    $app->render('login.html');
});

$app->get('/logout', function() use($app) {
    $_SESSION = array();
    $app->redirect(BASE_URL, 303);
});

// Opauth handling
$app->get('/auth/login/google(/:token)', function($token = '') use ($app) {     
    // Opauth library for external provider authentication
    require 'opauth.conf.php';
    $opauth = new Opauth($config);
});

$app->post('/auth/response', function() use ($app) {
    // get Opauth response
    $re = unserialize(base64_decode($_POST['opauth']));
    
    // instantiate Opauth
    require 'opauth.conf.php';
    $Opauth = new Opauth($config, false);
    
    // custom oauthresponse handler to return local user_id
    $oauthresponse = new OauthResponse($re, $Opauth);
    $oar = $oauthresponse->getUser();
    
    /// reset session variables
    unset($_SESSION['opauth']);
    
    // error handling
    if (isset($oar['error'])) {
        $app->flash('error', $oar['error']);
        $_SESSION['loggedin'] = FALSE;
        $app->response->redirect(BASE_URL . "/login", 303);
    } else {
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['user'] = $oar['user'];
        $app->response->redirect(BASE_URL, 303);
    }
});


/*********************************
    RUN
*********************************/

$app->run();

