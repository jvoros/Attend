<?
// LOGIN HANDLING ROUTES

$app->get('/login', function() use($app){   
    // handle redirect from Google with code as URL parameter
  if (isset($_GET['code'])) {
    $app->auth->loginUserByCode($_GET['code']);
    $app->redirect(BASE_URL);
  } else {
    $authUrl = $app->auth->createAuthUrl();
    $app->redirect($authUrl);
  }
});

$app->get('/loginform', function() use ($app) {
  $app->render('loginform.html');
});

$app->get('/logout', function () use ($app) {
  $app->flashKeep();
  $app->auth->logout();
  $app->redirect(BASE_URL . '/loginform');
});