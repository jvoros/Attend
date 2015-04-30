<?
namespace JV;

/*
/ import Redbean Facade
/ R must be setup and running
/ TO DO: figure out out to inject RedBean instance
*/    
use R as R;

use Google_Client as Google_Client;
use Google_Service_Oauth2 as Google_Service_Oauth2;

class Auth
{
  protected $app;
  protected $client;
  protected $userService;
  
  function __construct($app, $userService, $params) {
    $this->app          = $app;
    $this->userService  = $userService;
    $this->client       = $this->initGoogleClient($params);
  }
  
  // Route Middleware
  public function verifyLogin() 
  {
    return function() {
      if (!isset($_SESSION['user'])) {
      $this->app->redirect(BASE_URL . '/loginform');
      }
    };
  }
  
  public function verifyAjax() 
  {
    return function() {
      if (!isset($_SESSION['user'])) {
        echo "<a href='".BASE_URL."/loginform'>Please log in for access</a>";
        $this->app->stop(403, 'You shall not pass!');
      }
    };
  }
  
  public function authorizedRole($role_name = 'user') 
  {
    return function() use ($role_name) {
      if ($_SESSION['user']['role_name'] != $role_name) {
        $this->app->flash('error', 'You do not have access to that resource.');
        $this->app->redirect(BASE_URL);
        }
    };
  }
  
  // Google PHP Library
  // http://www.ibm.com/developerworks/library/mo-php-todolist-app/
  // https://developers.google.com/api-client-library/php/guide/aaa_oauth2_web
  // http://phppot.com/php/php-google-oauth-login/
  
  private function initGoogleClient($params) 
  {
    $client = new Google_Client();
    $client->setApplicationName('Attend');
    $client->setClientId($params['client_id']);
    $client->setClientSecret($params['client_secret']);
    $client->setRedirectUri($params['redirect_uri']);
    $client->setScopes(array(
      'https://www.googleapis.com/auth/userinfo.email',
      'https://www.googleapis.com/auth/userinfo.profile',
    ));
    return $client;
  }
  
  public function createAuthUrl() 
  {
    return $this->client->createAuthUrl();
  }
  
  private function authenticateCodeReturnGoogleUser($code) 
  {
    $this->client->authenticate($code);
    $this->client->getAccessToken();
    $service = new Google_Service_Oauth2($this->client);
    $user = $service->userinfo->get();
    return $user;
  }
  
  // Handle login
  private function processGoogleUser($guser) 
  {
  
    // check domain
    if ($guser['hd'] != 'denverem.org') {
      $this->app->flash('error', 'You must use your @denverem.org email address.');
      $this->app->redirect(BASE_URL . '/logout');
      $this->app->stop();
    }

    // query database
    $user = $this->userService->findUserByEmail($guser['email']);

    // check if new user or new name
    if (is_null($user)) {
      $user->fname   = $guser['givenName'];
      $user->lname   = $guser['familyName'];
      $user->email   = $guser['email'];
      $user->role_id = '2';
    } elseif ($user->fname != $guser['givenName'] || $user->lname != $guser['familyName']) {
      $user->fname   = $guser['givenName'];
      $user->lname   = $guser['familyName'];
    }

    // update last visit, store
    $user->last = date("Y-m-d H:i:s");
    $user_id = $this->userService->saveUser($user);

    // save in user in Service and session
    $this->user = $user;
    $_SESSION['user'] = $user->export();
    $_SESSION['user']['role_name'] = $user->role->name;

  }
  
  public function loginUserByCode($code) 
  {
    $user = $this->authenticateCodeReturnGoogleUser($code);
    $this->processGoogleUser($user);
  }
  
  // User Functions
  public function getUser()
  {
    $user = R::load('user', $_SESSION['user']['id']);
    return $user;
  }
  
  public function getUserID()
  {
    $user_id = $_SESSION['user']['id'];
    return $user_id;
  }
  
  // Logout
  public function logout() 
  {
    session_unset();
    $this->client->revokeToken();
  }
  
}
