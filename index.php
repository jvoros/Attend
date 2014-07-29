<?
/*********************************
    INITIALIZE COMPONENTS
*********************************/

// php housekeeping
session_cache_limiter(false);
session_start();
date_default_timezone_set('America/Denver');

// timeout session
if (empty($_SESSION['session_start'])) {
    $_SESSION['session_start'] = time();
} elseif (time() - $_SESSION['session_start'] > 60 * 240) {
    $_SESSION = array();
    $_SESSION['session_start'] = time();
}

// composer bootstrapping
require 'vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:dbase.sqlite');
R::freeze(true);

// initialize Slim, use Twig to render views
$app = new \Slim\Slim(array(
    'templates.path' => 'templates',
));

// prepare Twig view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    // 'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true,
    'debug' => true
);

// app wide constants
define('BASE_URL', 'http://localhost/Sites/DHREM/Attend'); // also defined in app.js 

// give Twig templates access to session variables, dump() function, Slim View Extras
$app->view->getEnvironment()->addGlobal('_', $_SESSION);
$app->view->getEnvironment()->addGlobal('base_url', BASE_URL);
$app->view->getEnvironment()->addExtension(new \Twig_Extension_Debug());
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension(), new \Twig_Extension_Debug());

// route middleware for authorization redirect
$auth = new AuthProtect($app);


/*********************************
    ROUTES
*********************************/

// HOME PAGE
$app->get('/', $auth->protect(), function() use($app) {
    
    // get conference dtails
    $confer = R::findOne('conference', ' day = ? ', array(date("Y-m-d")));
    if (isset($confer)) { 
        R::preload($confer, array('location' => 'location', 'remote' => 'location'));
        $conf = $confer->export();
    } else {
        $conf = 'none';
    }

    $_SESSION['conf'] = $conf;
    
    // if conference day, get checkin status
    if ($conf != 'none') {
        $checkinToday = R::findOne('checkin', ' user_id = :user AND conference_id = :conf ', 
                                   array(':user' => $_SESSION['user']['id'], ':conf' => $conf['id']));
                
        $_SESSION['checkin'] = empty($checkinToday) ? null : $checkinToday->export();
    }
    
    // render
    $app->render('home.html');
    
});

// SET LOCATION
$app->post('/loc/:loc', function($loc) use($app) {
    // log location in session
    $_SESSION['user']['location'] = $loc;
    echo $loc;
});


// CHECKIN
$app->get('/checkin', function() use($app) {

    if ($_SESSION['checkin'] == null) {
        $check                = R::dispense('checkin');
        $check->user_id       = $_SESSION['user']['id'];
        $check->conference_id = $_SESSION['conf']['id'];
        $check->in            = date("Y-m-d H:i:s");
        $check_id = R::store($check);
        $_SESSION['checkin'] = $check->export();
        
    } else {
        $app->flash('error', "You already checked in for today");
    }
    
    $app->redirect(BASE_URL, 303);
    
});

$app->get('/checkout', function() use($app) {
    
    if (isset($_SESSION['checkin']['in']) && empty($_SESSION['checkin']['out'])) {
        
        $check              = R::load('checkin', $_SESSION['checkin']['id']);
        $check->out         = date("Y-m-d H:i:s");
        $check->total       = round((strtotime($check->out) - strtotime($check->in))/3600, 2);
        $check_id = R::store($check);
        $_SESSION['checkin'] = $check->export();
        
    } elseif (isset($_SESSION['checkin']['out'])) {
        $app->flash('error', "You already checked out");
    } elseif (empty($_SESSION['checkin']['in'])) {
        $app->flash('error', "You must check in first");
    }
    
    $app->redirect(BASE_URL, 303);
    
});


// AUTHORIZATION HANDLING

$app->get('/login', function() use($app) {
    $app->render('login.html');
});

$app->get('/logout', function() use($app) {
    $_SESSION = array();
    $app->redirect('login', 303);
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

