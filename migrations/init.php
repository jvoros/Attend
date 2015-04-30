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
$loc->lat       = "39.7423359";
$loc->lng       = "-104.8415582";
$loc->radius    = 500;
$loc->favorite  = 1;
$loc->comments  = "The big hospital.";
$loc_id = R::store($loc);

$loc2           = R::dispense('location');
$loc2->name     = "Rita Bass, at Denver Health";
$loc2->address  = "660 Bannock Street, Denver CO 80204";
$loc2->lat      = "39.7268934"; 
$loc2->lng      = "-104.9899788";
$loc2_id = R::store($loc2);

$loc3           = R::dispense('location');
$loc3->name     = "Voros House";
$loc3->address  = "677 South Grape Street, Denver CO 80246";
$loc3->lat      = "39.704216";
$loc3->lng      = "-104.924045";
$loc3_id = R::store($loc3);

$loc5           = R::dispense('location');
$loc5->name     = "Denver Health, Main";
$loc5->address  = "777 Bannock St, Denver CO 80204";
$loc5->lat      = "39.72811919999999";
$loc5->lng       = "-104.9910209";
$loc5_id = R::store($loc5);

// CONFERENCES
$conf               = R::dispense('conference');
$conf->start        = "2015-01-04 07:30:00";
$conf->finish       = "2015-01-04 12:30:00";
$conf->primary_loc  = $loc;
$conf->sharedLocationList = array($loc3);
$conf->name         = "Morbity and Mortality";
$conf->elective     = FALSE;
$conf->comments     = "This will be a good one.";
$conf_id = R::store($conf);

$conf2               = R::dispense('conference');
$conf2->start        = "2015-02-04 07:30:00";
$conf2->finish       = "2015-02-04 12:30:00";
$conf2->primary_loc  = $loc2;
$conf2->sharedLocationList = array($loc3);
$conf2->name        = "Morbity and Mortality";
$conf2->elective    = FALSE;
$conf2_id = R::store($conf2);

$conf3              = R::dispense('conference');
$conf3->start       = "2015-03-06 18:30:00";
$conf3->finish      = "2015-03-06 20:30:00";
$conf3->primary_loc = $loc;
$conf3->sharedLocationList = array($loc3);
$conf3->name        = "Asynchronous Module";
$conf3->elective    = TRUE;
$conf3_id = R::store($conf3);

$conf4              = R::dispense('conference');
$conf4->start       = date("Y-m-d H:i:s");
$conf4->finish      = date("Y-m-d H:i:s", time()+7200);
$conf4->primary_loc = $loc3;
$conf4->sharedLocationList = array($loc, $loc2);
$conf4->name        = "Test Conference";
$conf4->elective    = FALSE;
$conf4_id = R::store($conf4);

$conf5              = R::dispense('conference');
$conf5->start       = date("Y-m-d H:i:s", time()+28800);
$conf5->finish      = date("Y-m-d H:i:s", time()+36000);
$conf5->primary_loc = $loc3;
$conf5->sharedLocationList = array($loc2, $loc);
$conf5->name        = "Test Elective Later";
$conf5->elective    = TRUE;
$conf5_id = R::store($conf5);

$conf6              = R::dispense('conference');
$conf6->start       = date("Y-m-d H:i:s");
$conf6->finish      = date("Y-m-d H:i:s", time()+7200);
$conf6->primary_loc = $loc5;
$conf6->sharedLocationList = array($loc2, $loc);
$conf6->name        = "Test Elective Elsewhere";
$conf6->elective    = TRUE;
$conf6_id = R::store($conf6);

echo "Created conferences <br />";

// CHECK INS
$check              = R::dispense('checkin');
$check->conference  = $conf;
$check->user        = $user;
$check->in_time     = '2015-01-04 07:32:12';
$check->out_time    = '2015-01-04 11:47:12';
$check->total       = round((strtotime($check->out) - strtotime($check->in)) / 3600, 2);
$check_id = R::store($check);

$check2              = R::dispense('checkin');
$check2->conference  = $conf;
$check2->user        = $admin;
$check2->in_time     = '2015-01-04 07:30:12';
$check2->out_time    = '2015-01-04 10:15:12';
$check2->total       = round((strtotime($check2->out) - strtotime($check2->in)) / 3600, 2);
$check2_id = R::store($check2);

$check3              = R::dispense('checkin');
$check3->conference  = $conf3;
$check3->user        = $user;
$check3->in_time     = '2015-03-06 07:32:12';
$check3->out_time    = '2015-03-06 9:52:47';
$check3->total       = round((strtotime($check3->out) - strtotime($check3->in)) / 3600, 2);
$check3_id = R::store($check3);

echo "Created checkin with user and conference <br />";