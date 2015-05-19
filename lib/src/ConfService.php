<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

class ConfService
{
  
  // add location information to conference object
  private function getAdditionalConferenceDetails($conf)
  {
    $conf->duration  = round((strtotime($conf->finish) - strtotime($conf->start)) / 3600, 2);
    $conf->location = $conf->fetchAs('location')->primary_loc;
    $conf->remotes = $conf->sharedLocationList;
    return $conf;
  }
  
  public function getConferenceByID($id)
  {
    $conf = R::load('conference', $id);
    $conf = $this->getAdditionalConferenceDetails($conf);
    return $conf;
  }
  
  public function deleteConferenceByID($id)
  {
    $conf = R::load('conference', $id);
    if($conf->id == 0) {
      $response = array(
        'status' => 'error',
        'message' => 'That conference not found.'
      );
    } else {
      R::trash($conf);
      $response = array(
        'status' => 'success',
        'message' => 'Deleted: '.$conf  ->name
      );
    }
    return $response;
    
  }
  
  // Y-m-d format
  public function getConferencesByDate($date)
  {
    $conferences =  R::find('conference', ' start LIKE ? ORDER BY start ASC ', array($date.'%'));
    foreach ($conferences as $conf) {
      $conf = $this->getAdditionalConferenceDetails($conf);
    }
    return $conferences;
  }
  
  // Y-m-d format
  public function getConferencesByDateRange($start, $end)
  {
    $conferences = R::find('conference', ' start >= :start AND start <= :end ORDER BY start ASC ', array(':start' => $start, ':end' => $end));
    foreach ($conferences as $conf) {
      $conf = $this->getAdditionalConferenceDetails($conf);
      $conf = $conf->export();
    }
    return $conferences;
  }
  
  public function getUpcomingConferences()
  {
    $datetime = new \DateTime('tomorrow');
    $conferences = R::find('conference', ' start > :start ORDER BY start ASC LIMIT 3', array(':start' => $datetime->format('Y-m-d')));
    foreach ($conferences as $conf) {
      $conf = $this->getAdditionalConferenceDetails($conf);
      $conf = $conf->export();
    }
    return $conferences;
  }
  
  public function processConference($conf)
  {
    
    $formErrors = array();

    if ($conf["name"] == '') {
      $formErrors[] = "Name is required.";
    }

    if ($conf['date'] == '') {
      $formErrors[] = "Date is required.";
    }
    
    if ($conf['start_time'] == '') {
      $formErrors[] = "Start time is required.";
    }
    
    if ($conf['end_time'] == '') {
      $formErrors[] = "End time is required.";
    }
    
    if ($conf['location_primary'] == '' || $conf['location_primary'] == '---') {
      $formErrors[] = "Location is required.";  
    }
    
    $conference = (isset($conf["id"]) ? R::load('conference', $conf["id"]) : R::dispense('conference'));
    
    if (empty($formErrors)) {
      $conference->name     = $conf['name'];
      $conference->start    = $conf['date'] . ' ' . $conf['start_time'];
      $conference->finish   = $conf['date'] . ' ' . $conf['end_time'];
      $conference->name     = $conf['name'];
      $conference->elective = (isset($conf['elective']) ? true : false);
      $conference->comments = $conf['comments'];
      $conference->primary_loc_id = $conf['location_primary'];
      //$conference->sharedLocationList[] = R::load('location', $conf['location_remote']);
      $conference_id = R::store($conference);
    }
    
    return array('formErrors' => $formErrors, 'conference' => $conf);
  
  }
  
}

