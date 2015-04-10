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
$loc->address   = "12605 East 16th Ave, Aurora CO 80045";
$loc->coords    = "39.740805,-104.843302,39.743659,-104.843366,39.743643,-104.838989,39.740986,-104.839246";
$loc_id = R::store($loc);

$loc2           = R::dispense('location');
$loc2->name     = "Rita Bass, at Denver Health";
$loc2->address  = "660 Bannock Street, Denver CO 80204";
$loc2->coords   = "39.725582,-104.990845,39.724221,-104.990866,39.724188,-104.989321,39.725599,-104.989235";
$loc2_id = R::store($loc2);

$loc3           = R::dispense('location');
$loc3->name     = "Voros House";
$loc3->address  = "677 South Grape Street, Denver CO 80246";
$loc3->coords   = "39.703910,-104.923371,39.703910,-104.924605,39.704488,-104.924573,39.704471,-104.923296";
$loc3_id = R::store($loc3);

$loc5           = R::dispense('location');
$loc5->name     = "Denver Health, Main";
$loc5->address  = "777 Bannock St, Denver CO 80204";
$loc5->coords   = "39.727323,-104.990330,39.727241,-104.992862,39.729056,-104.992948,39.728908,-104.990201";
$loc5_id = R::store($loc5);

// CONFERENCES
$conf               = R::dispense('conference');
$conf->start        = "2015-01-04 07:30:00";
$conf->finish       = "2015-01-04 12:30:00";
$conf->primary_loc  = $loc;
$conf->sharedLocationList = [$loc3];
$conf->name         = "Morbity and Mortality";
$conf->elective     = FALSE;
$conf_id = R::store($conf);

$conf2               = R::dispense('conference');
$conf2->start        = "2015-02-04 07:30:00";
$conf2->finish       = "2015-02-04 12:30:00";
$conf2->primary_loc  = $loc2;
$conf2->sharedLocationList = [$loc3];
$conf2->name        = "Morbity and Mortality";
$conf2->elective    = FALSE;
$conf2_id = R::store($conf2);

$conf3              = R::dispense('conference');
$conf3->start       = "2015-03-06 18:30:00";
$conf3->finish      = "2015-03-06 20:30:00";
$conf3->primary_loc = $loc;
$conf3->sharedLocationList = [$loc3];
$conf3->name        = "Asynchronous Module";
$conf3->elective    = TRUE;
$conf3_id = R::store($conf3);

$conf4              = R::dispense('conference');
$conf4->start       = date("Y-m-d H:i:s");
$conf4->finish      = date("Y-m-d H:i:s", time()+7200);
$conf4->primary_loc = $loc3;
$conf4->sharedLocationList = [$loc, $loc2];
$conf4->name        = "Test Conference";
$conf4->elective    = FALSE;
$conf4_id = R::store($conf4);

$conf5              = R::dispense('conference');
$conf5->start       = date("Y-m-d H:i:s", time()+28800);
$conf5->finish      = date("Y-m-d H:i:s", time()+36000);
$conf5->primary_loc = $loc5;
$conf5->sharedLocationList = [$loc2, $loc3];
$conf5->name        = "Test Elective";
$conf5->elective    = TRUE;
$conf5_id = R::store($conf5);

echo "Created conferences <br />";

// CHECK INS
$check              = R::dispense('checkin');
$check->conference  = $conf;
$check->user        = $user;
$check->in          = '2015-01-04 07:32:12';
$check->out         = '2015-01-04 11:47:12';
$check->total       = round((strtotime($check->out) - strtotime($check->in)) / 3600, 2);
$check_id = R::store($check);

$check2              = R::dispense('checkin');
$check2->conference  = $conf;
$check2->user        = $admin;
$check2->in          = '2015-01-04 07:30:12';
$check2->out         = '2015-01-04 10:15:12';
$check2->total       = round((strtotime($check2->out) - strtotime($check2->in)) / 3600, 2);
$check2_id = R::store($check2);

$check3              = R::dispense('checkin');
$check3->conference  = $conf3;
$check3->user        = $user;
$check3->in          = '2015-03-06 07:32:12';
$check3->out         = '2015-03-06 9:52:47';
$check3->total       = round((strtotime($check3->out) - strtotime($check3->in)) / 3600, 2);
$check3_id = R::store($check3);

echo "Created checkin with user and conference <br />";