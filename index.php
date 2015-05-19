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
define('BASE_URL', 'http://localhost/Sites/DHREM/Attend'); 
//define('BASE_URL', 'http://www.denverem.org/attend'); 


/*********************************
    INITIALIZE SLIM & COMPONENTS
*********************************/

$app = new \Slim\Slim(array(
    'templates.path' => 'templates',
    'debug' => true
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

// Model services
$app->userService     = new \JV\UserService();
$app->confService     = new \JV\ConfService();
$app->checkinService  = new \JV\CheckinService($app->userService, $app->confService);
$app->reportService   = new \JV\ReportService($app->userService, $app->confService, $app->checkinService);
$app->locationService = new \JV\LocationService();
$app->configService   = new \JV\ConfigService();

// Config params
$app->configs = include('config.php');

// Auth handling
$googleClientParams = array(
  'client_id'       => $app->configs['google_client_id'],
  'client_secret'   => $app->configs['google_client_secret'],
  'redirect_uri'    => BASE_URL . '/login',
);

$app->auth = new \JV\Auth($app, $app->userService, $googleClientParams);


/*********************************
    ROUTES
*********************************/

// Routes act as views, managing data from Model services
// include all route files
$routeFiles = (array) glob('lib/routes/*.php');
foreach($routeFiles as $routeFile) {
    require_once $routeFile;
}

// TEST ROUTES

/*********************************
    RUN
*********************************/

$app->run();

