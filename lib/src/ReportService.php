<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

class ReportService
{
  
  private $userService;
  private $confService;
  private $checkinService;
  
  function __construct($userService, $confService, $checkinService)
  {
    $this->userService      = $userService;
    $this->confService      = $confService;
    $this->checkinService   = $checkinService;
  }

  
  public function userListAttendanceByDate($userList, $start, $end)
  {
    foreach($userList as $user) {
      $user->report = $this->userAttendanceByDate($user->id, $start, $end);
    }
    
    return $userList;
  }
  
  public function userAttendanceByDate($user_id, $start, $end)
  {    
    // variables to be returned
    $required_conferences = array();
    $required_hours = 0;
    $required_attended = 0;
    $user_electives = array();
    $user_electives_attended = 0;
    $total_attended = 0;
    $percent_attended = 0;
    
    $checkins = $this->checkinService->getCheckinsForUserByDateRange($user_id, $start, $end);
    $checkins = $this->checkinService->indexCheckinsByConf($checkins);
    $conferences = $this->confService->getConferencesByDateRange($start, $end);
    
    foreach($conferences as $conf) {
      // all required conferences and total time
      if ($conf->elective == false) {
        $required_conferences[] = $conf;
        $required_hours += round((strtotime($conf->finish) - strtotime($conf->start))/3600, 2);
      }
      // all user-attended elective conferences and user's attended time
      if ($conf->elective == true && !empty($checkins[$conf->id]->out_time)) {
        $time_logged = round((strtotime($checkins[$conf->id]['out_time']) - strtotime($checkins[$conf->id]['in_time']))/3600, 2);
        $time_logged = ($time_logged > $conf->duration ? $conf->duration : $time_logged);
        $checkins[$conf->id]['total'] = $time_logged;
        $user_electives_attended += $time_logged;
        $conf->checkin = $checkins[$conf->id];
        $user_electives[] = $conf;
      }  
    }
    
    // match checkins to required conferences and tally user's attended time
    foreach($required_conferences as $conf) {
      $conf->checkin = $checkins[$conf->id];
      if(!empty($checkins[$conf->id]->out_time)) {
        $time_logged = round((strtotime($checkins[$conf->id]['out_time']) - strtotime($checkins[$conf->id]['in_time']))/3600, 2);
        $time_logged = ($time_logged > $conf->duration ? $conf->duration : $time_logged);
        $checkins[$conf->id]['total'] = $time_logged;
        $required_attended += $time_logged;
      }
    }
    
    $total_attended = $required_attended + $user_electives_attended;
    if($required_hours) {
      $percent_attended = round($total_attended/$required_hours, 2) * 100;
    } else {
      $percent_attended = 0;
    }
    
    $report = array(
      'required_conferences'    => $required_conferences,
      'required_hours'          => $required_hours,
      'required_attended'       => $required_attended,
      'user_electives'          => $user_electives,
      'user_electives_attended' => $user_electives_attended,
      'percent_attended'        => $percent_attended,
      'total_attended'          => $total_attended
    );
    
    return $report;    
  }
  
}

