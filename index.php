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

// give Twig templates access to session variables, dump() function, Slim View Extras
$app->view->getEnvironment()->addGlobal('session', $_SESSION);
$app->view->getEnvironment()->addGlobal('base_url', BASE_URL);
$app->view->getEnvironment()->addExtension(new \Twig_Extension_Debug());
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension(), new \Twig_Extension_Debug());

// route middleware for authorization redirect
$auth = new AuthProtect($app);

/*********************************
    ROUTES
*********************************/

/*

GET /           home page, checkin/out buttons appear on conference day, generate attendance report button
GET /checkin    process checkin for conference
GET /checkout   process checkout for conference
GET /report     attendance report

// ADMIN ROUTES

*/


$app->get('/', $auth->protect(), function() use($app) {
    // check if today is a conference day, set session variable
    $confer = R::findOne('conference', ' day = ? ', array(date("Y-m-d")));
    if (empty($confer)) {
        $conf = 'none';
    } else {
        // get conference details
        $conf = array();
        $conf['id']         = $confer->id;
        $conf['day']        = $confer->day;
        $conf['location']   = $confer->location->name;
        $conf['coords']     = $confer->location->coords;
        $conf['remote']     = $confer->fetchAs('location')->remote->name;
        $conf['r_coords']   = $confer->fetchAs('location')->remote->coords;
        
        // get checkin details
        $checkin = R::findOne('checkin', ' user_id = :user AND conference_id = :conf ', array(':user' => $_SESSION['user']['id'], ':conf' => $conf['id']));
        $_SESSION['checkin'] = $checkin->export();
    }
    
    
    
    $app->render('home.html', array('conf' => $conf));
    // if conference day, display checkin/out buttons
    
    // if not conference day, display notification
    
    // display attendance report button
});


/*
$app->get('/checkin', function() use($app) {
    // protect route to require login
    
    // process checkin
    
    // display confirmation message, checkout button, logout button
});


$app->get('/checkout', function() use($app) {
    // protect route to require login
    
    // process checkout
    
    // display confirmation message, attendance report button, logout button
}):


*/

// AUTHORIZATION HANDLING
// login, logout
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
    if (isset($user['error'])) {
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

