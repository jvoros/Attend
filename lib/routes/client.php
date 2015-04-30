<?
// USER CLIENT ROUTES

// HOME
$app->get('/', $app->auth->verifyLogin(), function() use($app) {
  $todaysConferences = $app->confService->getConferencesByDate(date("Y-m-d"));
  $report = $app->reportService->userAttendanceByDate($app->auth->getUserID(), '2015-01-01', '2016-01-01');
  $data = array(
    'todays_conferences' => $todaysConferences,
    'report' => $report
  );
  $app->render('home.html', array('data' => $data));
});


// CONFERENCE
$app->get('/conference/:id', $app->auth->verifyLogin(), function($id) use($app) {    
  $conf = $app->confService->getConferenceByID($id);
  $data = array(
    'conf' => $conf
  );
  $app->render('conference.html', array('data' => $data));
});


// CHECKIN
$app->post('/checkin/:conf_id/:user_id', $app->auth->verifyAjax(), function($conf_id, $user_id) use($app) {

  $checkin = $app->checkinService->getCheckinForConfUser($conf_id, $user_id);

  $data = array('checkin' => $checkin);

  // Confirm time and location
  $tandl = $app->checkinService->checkTimeAndLocation($conf_id, $app->request->params('at_location'));
  if ($tandl == 'false') {
    $app->render('checkin-wrongtl.html', array('data' => $data));
    $app->stop();
  }

  // Handle client-side controller reqeuests
  switch($app->request->params('action')) {
    case "checkin":
      $response = $app->checkinService->processCheckin($conf_id, $user_id);
      break;
    case "checkout":
      $response = $app->checkinService->processCheckout($conf_id, $user_id);
      break;
    case "cancel-checkin":
      $response = $app->checkinService->cancelCheckin($conf_id, $user_id);
      break;
    case "cancel-checkout":
      $response = $app->checkinService->cancelCheckout($conf_id, $user_id);
      break;
  }

  // Update template variables
  $error = $response['error'];
  $checkin = $app->checkinService->getCheckinForConfUser($conf_id, $user_id);
  $data['checkin'] = $checkin;
  $data['error'] = $error;

  // Decide which template to render
  if (!isset($checkin->in_time)) {
    $app->render('checkin-in.html', array('data' => $data));
    $app->stop();
  }

  if(!isset($checkin->out_time)) {
    $app->render('checkin-out.html', array('data' => $data));
    $app->stop();
  }

  if(isset($checkin->out_time)) {
    $app->render('checkin-done.html', array('data' => $data));
    $app->stop();
  }

});
