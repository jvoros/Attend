<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

class UserService
{
  
  public function getNewUser() 
  {
    $user = R::dispense('user');
    return $user;
  }
  
  public function saveUser($user) 
  {
    $user_id = R::store($user);
    return $user_id;
  }
  
  public function findUserByEmail($email) 
  {
    $user = R::findOne('user', ' email = ? ', array($email));
    return $user;
  }
  
  public function findUserListByDateRange($start, $end)
  {
    $userList = R::find('user', ' last >= :start AND last <= :end ORDER BY lname ASC ', array(':start' => $start, ':end' => $end));
    return $userList;
  }
  
}
