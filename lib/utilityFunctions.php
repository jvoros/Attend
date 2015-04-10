<?
// ROUTE AUTHORIZATION
// by role
$authorizedRole = function($role_name = 'user') {
  return function() use ($role_name) {
    if ($_SESSION['user']['role_name'] != $role_name) {
    $app = \Slim\Slim::getInstance();
    $app->flash('error', 'You do not have access to that resource.');
    $app->redirect(BASE_URL);
    }
  };
};

// USERS
function getCheckinsByConf($checkins) {
  foreach ($checkins as $checkin) {
    $checkins_by_conf[$checkin->conference_id] = $checkin;
  }
  return $checkins_by_conf;
}

// CONFERENCES
// get necessary conference data as array
// if $checkins (indexed by conference) are provided, also get user's checkin for that conference
function getConferenceAsArray($conf, $checkins = NULL) {
  $conf->duration  = (strtotime($conf->finish) - strtotime($conf->start)) / 3600;
  $conf->location = $conf->fetchAs('location')->primary_loc;
  $conf->remotes = $conf->sharedLocationList;
  if(isset($checkins[$conf->id])) {
    $conf->checkin = $checkins[$conf->id];
  }
  $conference = $conf->export();
  return $conference;
}
