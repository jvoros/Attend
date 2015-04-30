<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

class CheckinService
{
  
  private $userService;
  private $confService;
  
  function __construct($userService, $confService)
  {
    $this->userService = $userService;
    $this->confService = $confService;
  }
  
  public function checkTimeAndLocation($conf_id, $loc)
  {
    $conf = $this->confService->getConferenceByID($conf_id);
    if ((strtotime($conf->start) - time()) > 3600 || (time() - strtotime($conf->finish)) > 3600) {
      $response = 'false';
    };
    if ($loc == 'false') {
      $response = 'false';
    };
    return $response;
  }
  
  public function getCheckinForConfUser($conf_id, $user_id)
  {
    $checkin = R::findOne('checkin', ' conference_id = :conf_id AND user_id = :user_id ', array(':conf_id' => $conf_id, ':user_id' => $user_id));
    return $checkin;
  }
  
  public function getCheckinsForUserByDateRange($user_id, $start, $end) {
    $checkins = R::find('checkin', ' user_id = :user_id AND in_time >= :start AND in_time <= :end ', 
                        array(':user_id' => $user_id, ':start' => $start, ':end' => $end));
    return $checkins;
  }
  
  public function indexCheckinsByConf($checkins)
  {
    foreach($checkins as $checkin) {
       $checkins_by_conf[$checkin->conference_id] = $checkin;
    }
    return $checkins_by_conf;
  }
  
  // Checkin processing
  public function processCheckin($conf_id, $user_id)
  {
    $response = array();
    $checkin = $this->getCheckinForConfUser($conf_id, $user_id);
    if(isset($checkin->in_time)) {
      $response['error'] = "You have already checked in for this conference.";
    } else {
      $checkin = R::dispense('checkin');
      $checkin->conference_id = $conf_id;
      $checkin->user_id = $user_id;
      $checkin->in_time = date('Y-m-d H:i:s');
      $checkin_id = R::store($checkin);
      if($checkin_id) {
        $response['checkin'] = $checkin;
      } else {
        $response['error'] = "Unable to save checkin.";
      }
    }
    return $response;
  }
  
  public function processCheckout($conf_id, $user_id) 
  {
    $response = array();
    $checkin = $this->getCheckinForConfUser($conf_id, $user_id);
    if(isset($checkin->out_time)) {
      $response['error'] = "You have already checked out for this conference.";
    } elseif (!isset($checkin->in_time)) {
      $resopnse['error'] = "You must check in before you check out.";
    } else {
      $checkin->out_time = date('Y-m-d H:i:s');
      $checkin_id = R::store($checkin);
      if($checkin_id) {
        $response['checkin'] = $checkin;
      } else {
        $response['error'] = "Unable to save checkin.";
      }
    }
    return $response;
  }
  
  public function cancelCheckin($conf_id, $user_id) {
    $response = array();
    $checkin = $this->getCheckinForConfUser($conf_id, $user_id);
    R::trash($checkin);
    $response['checkin'] = null;
    return $response;
  }
  
  public function cancelCheckout($conf_id, $user_id) {
    $response = array();
    $checkin = $this->getCheckinForConfUser($conf_id, $user_id);
    $checkin->out_time = null;
    $checkin_id = R::store($checkin);
    if($checkin_id) {
      $response['checkin'] = $checkin;
    } else {
      $response['error'] = "Unable to save checkin.";
    }
    return $response;
  }  
  
}


