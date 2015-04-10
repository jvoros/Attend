<?
// ROUTES
$app->get('/login', function() use($app){    
  require_once 'lib/controllers/login.php';
});

$app->get('/loginform', function() use ($app) {
  $app->render('loginform.html');
});

$app->get('/logout', function () use ($app) {
  $app->flashKeep();
  session_unset();
  $app->client->revokeToken();
  $app->redirect(BASE_URL . '/loginform');
});