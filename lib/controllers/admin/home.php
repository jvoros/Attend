<?
function getTodaysConferences($checkins = NULL) {
  $conference_beans = R::find('conference', ' start LIKE ? ORDER BY start ASC ', array(date("Y-m-d").'%'));
  $conferences = array();
  foreach ($conference_beans as $bean) {
    // only include if current time is earlier than one hour after finish time
    if (strtotime($bean->finish) - time() > -3600) { $conferences[] = getConferenceAsArray($bean, $checkins); }
  }

  return $conferences;
}

// UPCOMING CONFERENCES X3
$future_conferences = R::find('conference', ' start >= ? ORDER BY start ASC ', array(date("Y-m-d")));

$data = array(
  'future_conferences'   => $future_conferences,
  'upcoming_conferences' => array_slice($future_conferences, 0, 3),
);