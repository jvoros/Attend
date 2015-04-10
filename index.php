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
$twig->addExtension(new \Twig_Extension_Debug());

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

require 'lib/utilityFunctions.php';

/*********************************
    ROUTES
*********************************/

$routeFiles = (array) glob('lib/routes/*.php');
foreach($routeFiles as $routeFile) {
    require_once $routeFile;
}

// TEST ROUTES

$app->get('/getsession', function() use ($app) {
  header("Content-Type: application/json");
  echo json_encode($_SESSION);
});

/*********************************
    RUN
*********************************/

$app->run();

