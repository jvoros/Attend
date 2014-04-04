<?
/*********************************
    INITIALIZE COMPONENTS
*********************************/

session_cache_limiter(false);
session_start();
date_default_timezone_set('America/Denver');

// composer bootstrapping
require 'vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:dbase.sqlite');
$Rschema = R::$duplicationManager->getSchema();
R::$duplicationManager->setTables($Rschema);
R::freeze(true);

// app wide utility functions and constants
define('BASE_URL', 'http://localhost/Sites/Attend'); // also defined in app.js 

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



/*********************************
    ROUTES
*********************************/

$app->get('/', function() use($app) {

});


// AUTHORIZATION HANDLING

$app->get('/login', function() use($app) {
    $_SESSION['authredirect'] = $app->request->getReferer();
    // set flag
    $_SESSION['authredirect_flag'] = 1;
    $app->render('login.html');
});

$app->get('/auth/login(/:strategy(/:token))', function($action, $strategy = '', $token = '') use ($app) { 
    // because of Opauth redirects, need to only store referring URL on first visit to the login page, set flag to ensure this is the case
    if (empty($_SESSION['authredirect_flag'])) { 
        // store page prior to login click for redirect
        $_SESSION['authredirect'] = $app->request->getReferer();
        // set flag
        $_SESSION['authredirect_flag'] = 1;
    }
    
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
    
    // custom authresponse handler
    $authresponse = new AuthResponse($re, $Opauth);
    $authresponse->login();
    
    // reset session variables
    unset($_SESSION['opauth']);
    unset($_SESSION['authredirect_flag']);
    
    // redirect to homepage
    $app->response->redirect($_SESSION['authredirect'], 303); 
    
});

$app->get('/auth/logout', function() use($app) {
    unset($_SESSION['loggedin']);
    unset($_SESSION['user']);
    $app->redirect('http://localhost/emcanon/', 303);
});

/*********************************
    RUN
*********************************/

$app->run();

