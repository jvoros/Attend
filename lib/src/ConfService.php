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
    $conf->duration  = (strtotime($conf->finish) - strtotime($conf->start)) / 3600;
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
  
}

