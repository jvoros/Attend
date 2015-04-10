<?
// GOOGLE OAUTH LOGIN ROUTES AND CONTROLLERS
// http://www.ibm.com/developerworks/library/mo-php-todolist-app/
// https://developers.google.com/api-client-library/php/guide/aaa_oauth2_web
// http://phppot.com/php/php-google-oauth-login/

// UTILITY FUNCTION TO HANDLE GOOGLE USER INFO
function processGoogleUser($guser) {
  
  // check domain
  if ($guser['hd'] != 'denverem.org') {
    $app = \Slim\Slim::getInstance();
    $app->flash('error', 'You must use your @denverem.org email address.');
    $app->redirect(BASE_URL . '/logout');
    exit;
  }
  
  // query database
  $user = R::findOne('user', ' email = ? ', [$guser['email']]);
  
  // check if new user or new name
  if (is_null($user)) {
    $user->fname   = $guser['givenName'];
    $user->lname   = $guser['familyName'];
    $user->email   = $guser['email'];
    $user->role    = '2';
  } elseif ($user->fname != $guser['givenName'] || $user->lname != $guser['familyName']) {
    $user->fname   = $guser['givenName'];
    $user->lname   = $guser['familyName'];
  }
  
  // update last visit, store
  $user->last = date("Y-m-d H:i:s");
  $user_id = R::store($user);
  
  // save in user in session
  $_SESSION['user'] = $user->export();
  $_SESSION['user']['role_name'] = $user->role->name;
  
}

// handle redirect from Google with code as URL parameter
if (isset($_GET['code'])) {
  $app->client->authenticate($_GET['code']);
  $app->client->getAccessToken();
  $service = new Google_Service_Oauth2($app->client);
  $user = $service->userinfo->get();
  processGoogleUser($user);
  $app->redirect(BASE_URL . '/user');
} else {
  $authUrl = $app->client->createAuthUrl();
  $app->redirect($authUrl);
}