<?
// Get user and checkin for this conference
$user = R::load('user', $_SESSION['user']['id']);
$conf = R::load('conference', $conf_id);
$checkin = reset($user->withCondition(' conference_id = ? ', [$conf_id])->ownCheckinList); // gets first and only item out of ownList array

// Confirm time and location
if((strtotime($conf->start) - time()) < 3600 && (time() - strtotime($conf->finish)) < 3600) {
  $timeframe = 'true';
} else {
  $timeframe = 'false';
}

// Handle check-in
if($_POST['checkin'] == 'in') {
  if (isset($checkin->in)) { 
    $error = "You have already checked in for this conference."; 
  } elseif ($timeframe == 'false') {
    $error = "You must be within one hour of the start or finish time for checkins";
  } elseif ($_SESSION['user']['at_loc'] == 'false') {
    $error = "You must be at the conference location or a remote location for checkins";
  } else {
    $new_check              = R::dispense('checkin');
    $new_check->user        = $user;
    $new_check->conference  = $conf; 
    $new_check->in          = date('Y-m-d H:i:s');
    $ncid = R::store($new_check);
    $checkin = $new_check; 
  }
}

// Handle cancel check-in
if($_POST['cancel'] == 'in') {
  if(!isset($checkin->in)) {
    $error = "You have not checked in yet.";
  } else {
    R::trash($checkin);
    $checkin = '';
  }
}

// Handle check-out
if($_POST['checkin'] == 'out') {
  if (isset($checkin->out)) {
    $error = "You have already checked out for this conference.";
  } elseif (!isset($checkin->in)) {
    $error = "You must check in before you check out.";
  } elseif ($timeframe == 'false') {
    $error = "You must be within one hour of the start or finish time for checkins";
  } elseif ($_SESSION['user']['at_loc'] == 'false') {
    $error = "You must be at the conference location or a remote location for checkins";
  } else {
    $checkin->out = date('Y-m-d H:i:s');
    $checkin_id = R::store($checkin);
  }
}

// Handle cancel check-out
if($_POST['cancel'] == 'out') {
  if(!isset($checkin->out)) {
    $error = "You have not checked out yet.";
  } else {
    $checkin->out = '';
    $cancel_out_id = R::store($checkin);
  }
}

$data = array(
  'conf'      => $conf_array,
  'checkin'   => $checkin,
  'timeframe' => $timeframe,
  'at_loc'    => $_POST['at_location']
);