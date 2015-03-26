<?
// script to load initial data into database

date_default_timezone_set('America/Denver');
require '../vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:../dbase.sqlite');

// emtpy old database
R::nuke();
echo "Nuked old dbase<br />";

// ROLES
$role_admin       = R::dispense('role');
$role_admin->name = "admin";
$role_admin_id = R::store($role_admin);

$role_user        = R::dispense('role');
$role_user->name  = "user";
$role_user_id = R::store($role_user);

// USERS
$user           = R::dispense('user');
$user->fname    = "Jeremy";
$user->lname    = "Vorossssss";
$user->email    = "jeremy.voros@denverem.org";
$user->created  = "2014-04-16 17:30:30";
$user->last     = date("Y-m-d H:i:s");
$user->role     = $role_user;
$user_id = R::store($user);

$admin           = R::dispense('user');
$admin->fname    = "Admin";
$admin->lname    = "Admin";
$admin->email    = "admin@denverem.org";
$admin->created  = "2014-04-16 17:30:30";
$admin->last     = date("Y-m-d H:i:s");
$admin->role     = $role_admin;
$admin_id = R::store($admin);

echo "Created user <br />";

// LOCATIONS
$loc            = R::dispense('location');
$loc->name      = "University of Colorado";
$loc->coords    = "39.740805,-104.843302,39.743659,-104.843366,39.743643,-104.838989,39.740986,-104.839246";
$loc_id = R::store($loc);

$loc2           = R::dispense('location');
$loc2->name     = "Rita Bass, at Denver Health";
$loc2->coords   = "39.725582,-104.990845,39.724221,-104.990866,39.724188,-104.989321,39.725599,-104.989235";
$loc2_id = R::store($loc2);

$loc3           = R::dispense('location');
$loc3->name     = "Voros House";
$loc3->coords   = "39.703910,-104.923371,39.703910,-104.924605,39.704488,-104.924573,39.704471,-104.923296";
$loc3_id = R::store($loc3);

$loc4           = R::dispense('location');
$loc4->name     = "Children's Hospital";
$loc4->coords   = "39.743428,-104.837379,39.741316,-104.837337,39.741201,-104.833174,39.743527,-104.833152";
$loc4_id = R::store($loc4);

$loc5           = R::dispense('location');
$loc5->name     = "Denver Health, Main";
$loc5->coords   = "39.727323,-104.990330,39.727241,-104.992862,39.729056,-104.992948,39.728908,-104.990201";
$loc5_id = R::store($loc5);

// CONFERENCES
$conf               = R::dispense('conference');
$conf->day          = "2014-06-14";
$conf->location     = $loc;
$conf->remote       = $loc3;
$conf->duration     = 4;
$conf->name         = "Morbity and Mortality";
$conf_id = R::store($conf);

$conf2              = R::dispense('conference');
$conf2->day         = date('Y-m-d');
$conf2->location    = $loc2;
$conf2->remote      = $loc3;
$conf2->duration    = 4;
$conf2->name         = "Morbity and Mortality";
$conf2_id = R::store($conf2);

echo "Created conferences <br />";

// CHECK INS
$check              = R::dispense('checkin');
$check->conference  = $conf;
$check->user        = $user;
$check->in          = date('H:i:s', strtotime('2014-06-14 07:32:12'));
$check->out         = date('H:i:s', strtotime('2014-06-14 11:32:12'));
$check->total       = $check->out - $check->in;
$check_id = R::store($check);

echo "Created checkin with user and conference <br />";