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

// Auth handling
$googleClientParams = array(
  'client_id'       => '459801839286-j103ceme00kacpbrk19nihn7r3l2icme.apps.googleusercontent.com',
  'client_secret'   => 'C95vXMePXkVXKgtuQr8lWCqk',
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

$app->get('/test', function() use($app) {
  $confs = $app->confService->getConferencesByDateRange('2015-04-01', '2016-01-01');
  foreach ($confs as $conf) {
    $start = date('Y-m-d', strtotime($conf->start));
    echo "<p><b>". $conf->name ."</b> on ". $start ."</p>";
  }
  
  $checkins = $app->checkinService->getCheckinsForUserByDateRange(1, '2015-01-01', '2016-01-01');
  echo "<b>Checkins</b>";
  foreach($checkins as $checkin) {
    $in = date('Y-m-d', strtotime($checkin->in_time));
    echo "<p>User_id: ". $checkin->user_id .". In: ". $in ."</p>";
  }
  
  $report = $app->reportService->userAttendanceByDate(1, '2015-01-01', '2016-01-01');
  echo "<b>Report</b><br>";
  echo "Required hours: ".$report['required_hours']."<br>";
  echo "Checkins: <br><pre>";
  echo print_r($report['checkins'], true);
  echo "</pre>";
  echo "Electives: <br><pre>";
  echo print_r($report['user_electives'], true);
  echo "</pre>";
});
/*********************************
    RUN
*********************************/

$app->run();

