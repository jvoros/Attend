<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

class ConfigService
{

  public function getConfig($key) 
  {
    $config = R::findOne('config', ' name = ? ', array($key));
    return $config->value;
  }
  
  public function getAllConfigs()
  {
    $configList = R::findAll('config');
    return $configList;
  }
  
  public function updateConfigs($updates)
  {
    $formErrors = array();
    foreach($updates as $key => $value) {
      $config = R::findOne('config', ' name = ? ', array($key));
      if(!$config) {
        $formErrors[] = "Error finding config " . $key;
      } else {
        $config->value = $value;
        $config_id = R::store($config);
      }
    }
    $response['formErrors'] = $formErrors;
    return  $response;
  }

}