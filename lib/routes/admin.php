<?
// ADMIN ROUTES
$app->group('/admin', $app->auth->authorizedRole('admin'), function() use($app) {

  $app->get('/', function() use($app) {
    $app->render('admin/home.html', array('data' => $data));
  });
  
  // Config Handling
  $app->get('/config', function() use($app) {
    $configList = $app->configService->getAllConfigs();
    $app->render('admin/config.html', array('configList' => $configList));
  });
  
  $app->post('/config', function() use($app) {
    $response = $app->configService->updateConfigs($app->request->post());
    $configList = $app->configService->getAllConfigs();
    if (empty($response['formErrors'])) {
      $app->flashNow('message', 'Updated configs');
    } else {
      $app->flashNow('formErrors', $response['formErrors']);
      $app->render('admin/config.html', array('configList' => $configList));
    }
    
    $app->render('admin/config.html', array('configList' => $configList));
  });
  
  
  // Location Handling
  $app->get('/locations', function() use($app) {
    $locations = $app->locationService->getAllLocations();
    $faves = array();
    foreach($locations as $loc) {
      if ($loc->favorite == true) { $faves[] = $loc; }
    }
    $app->render('admin/locations.html', array('locations' => $locations, 'faves' => $faves));
  });
  
  $app->get('/location', function() use($app) {
    $app->render('admin/location.html');
  });
  
  $app->post('/location(/:id)', function($id = '') use($app) {
    $response = $app->locationService->processLocation($app->request->post());
    if (empty($response['formErrors'])) {
      $app->flash('message', 'Added/Updated location: ' . $response['location']->name);
      $app->redirect(BASE_URL . '/admin/locations');
    } else {
      $app->flashNow('formErrors', $response['formErrors']);
      $app->render('admin/location.html', array('location' => $response['location']));
    }
  });
  
  $app->get('/location/:id', function($id) use($app) {
    $location = $app->locationService->getLocationByID($id);
    if($location->id == 0) {
      $app->flashNow('error', 'No location found.');
    }
    $app->render('admin/location.html', array('location' => $location));
  });

  
  $app->get('/location/delete/:id', function($id) use($app) {
    $response = $app->locationService->deleteLocationByID($id);
    if($response['status'] == 'error') {
      $app->flash('error', $response['message']);
    } else {
      $app->flash('message', $response['message']);
    }
    $app->redirect(BASE_URL . '/admin/locations');
  });
  
  // Conference Handling
  $app->map('/conferences', function($start = null, $end = null) use($app) {
    $start_date = ($app->request->post('start') ? $app->request->post('start') : $app->configService->getConfig('start_date'));
    $end_date = ($app->request->post('end') ? $app->request->post('end') : $app->configService->getConfig('end_date'));
    $conferences = $app->confService->getConferencesByDateRange($start_date, $end_date);
    $app->render('admin/conferences.html', array('conferences' => $conferences, 'start_date' => $start_date, 'end_date' => $end_date));
  })->via('GET', 'POST');
  
  $app->get('/conference', function() use($app) {
    $locations = $app->locationService->getAllLocations();
    $faves = array();
    foreach($locations as $loc) {
      if ($loc->favorite == true) { $faves[] = $loc; }
    }
    $app->render('admin/conference.html', array('conference' => $conference, 'locations' => $locations, 'faves' => $faves));
  });
  
  $app->get('/conference/:id', function($id) use($app) {
    $conference = $app->confService->getConferenceByID($id);
    if($conference->id == 0) {
      $app->flashNow('error', 'No conference found.');
    }
    $locations = $app->locationService->getAllLocations();
    $faves = array();
    foreach($locations as $loc) {
      if ($loc->favorite == true) { $faves[] = $loc; }
    }
    $app->render('admin/conference.html', array('conference' => $conference, 'locations' => $locations, 'faves' => $faves));
  });
  
  $app->post('/conference(/:id)', function($id = '')  use($app) {
    $response = $app->confService->processConference($app->request->post());
    if (empty($response['formErrors'])) {
      $app->flash('message', 'Added/Updated Conference: ' . $response['conference']['name']);
      $app->redirect(BASE_URL . '/admin/conferences');
    } else {
      $app->flashNow('formErrors', $response['formErrors']);
      $locations = $app->locationService->getAllLocations();
      $faves = array();
      foreach($locations as $loc) {
        if ($loc->favorite == true) { $faves[] = $loc; }
      }
      $app->render('admin/conference.html', array('conference' => $response['conference'], 'locations' => $locations, 'faves' => $faves));
    }
  });
  
  $app->get('/conference/delete/:id', function($id) use($app) {
    $response = $app->confService->deleteConferenceByID($id);
    if($response['status'] == 'error') {
      $app->flash('error', $response['message']);
    } else {
      $app->flash('message', $response['message']);
    }
    $app->redirect(BASE_URL . '/admin/conferences');
  });

  
  // Report Handling
  $app->get('/reports', function() use($app) {
    $default_start = $app->configService->getConfig('start_date');
    $default_end = $app->configService->getConfig('end_date');
    $app->render('admin/reports.html', array('default_start' => $default_start, 'default_end' => $default_end));
  });
  
  $app->get('/reports/users_attendance_date_range', function() use($app) {
    $start = $app->request->params('start');
    $end =  $app->request->params('end');
    
    if (empty($start) || empty($end)) {
      $app->flash('error', 'Start and End dates required');
      $app->redirect(BASE_URL . '/admin/reports');
    }
    
    $userList = $app->userService->findUserListByDateRange($start, $end);
    $userList = $app->reportService->userListAttendanceByDate($userList, $start, $end);
    
    $app->render('admin/reports/users_attendance_date_range.html', array('start' => $start, 'end' => $end, 'userList' => $userList));
    
  });
  
});