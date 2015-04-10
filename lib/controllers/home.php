<?
// UTILITY FUNCTIONS
// get necessary conference data for today's conferences that have not yet finished within the hour
// if $checkins (indexed by conference) are provided, will also get user's checkins for these conferences
function getTodaysConferences($checkins = NULL) {
  $conference_beans = R::find('conference', ' start LIKE ? ORDER BY start ASC ', [date("Y-m-d").'%']);
  $conferences = [];
  foreach ($conference_beans as $bean) {
    // only include if current time is earlier than one hour after finish time
    if (strtotime($bean->finish) - time() > -3600) { $conferences[] = getConferenceAsArray($bean, $checkins); }
  }

  return $conferences;
}

// CONTROLLER LOGIC
// Get user and then index checkins by conference for easy querying
$user = R::load('user', $_SESSION['user']['id']);
$checkins_by_conf = getCheckinsByConf($user->ownCheckinList);

// initialize variables
$all_conferences = [];
$attended_electives = [];
$total_conference_hours = '';
$logged_user_hours = '';

// Get a list of all conferences with corresponding user's attendance at each
// Total conference hours and user's attendance hours
// Don't include today's conferences
$conf_beans = R::findAll('conference', ' elective = 0 ORDER BY start DESC ' );
foreach ($conf_beans as $bean) {
  if(date('Ymd', strtotime($bean->start)) < date('Ymd')) {
    $conf = getConferenceAsArray($bean, $checkins_by_conf);
    $all_conferences[] = $conf;
    $total_conference_hours += $conf['duration'];
    if (isset($conf['checkin'])) {
      $logged_user_hours += $conf['checkin']['total'];
    }
  }
}

// Get a list of all electives user has attended and add those hours to user's total hours
$elective_beans = R::findAll('conference', ' elective = 1 ORDER BY start DESC ' );
foreach ($elective_beans as $bean) {
  $conf = getConferenceAsArray($bean, $checkins_by_conf);
  if (isset($conf['checkin'])) {
    $attended_electives[] = $conf;
    $logged_user_hours += $conf['checkin']['total'];
  }
}

// Return the data to the template
$data = array(
  'all_conferences'         => $all_conferences,
  'attended_electives'      => $attended_electives,
  'total_conference_hours'  => $total_conference_hours,
  'logged_user_hours'       => $logged_user_hours,
  'percent_attended'        => round($logged_user_hours/$total_conference_hours, 2) * 100,
  'todays_conferences'      => getTodaysConferences($checkins_by_conf),  
);
