<?php

/**
 * List all credentials in master vault
 */
$app->get('/master', $authenticate($app), function() use ($app) {
	$vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);
  $credentials = $v->getAllCredentials();
  // krumo($credentials);
  $app->render('routes/master.html.twig', array(
  	'credentials' => $credentials,
  ));
})->name('master');

/**
 * Add new credential in master vault
 */
$app->get('/master/add', function() use ($app) {
  // krumo($_SESSION);
  $app->render('routes/add.html.twig', array());
})->name('masteradd');  

$app->post('/master/add', function() use ($app) {
  $vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);

  $post_data = $app->request->post();
  $valid_form = true;
  $errors = array();
  if (empty($post_data['username'])) {
    $errors[] = 'Username cannot be empty.';
    $valid_form = false;
  }
  if (empty($post_data['password'])) {
    $errors[] = 'Password cannot be empty.';
    $valid_form = false;
  }
  if (empty($post_data['site'])) {
    $errors[] = 'Site cannot be empty.';
    $valid_form = false;
  }
  if (empty($post_data['url'])) {
    $errors[] = "URL cannot be empty.";
    $valid_form = false;
  }
  if (!$valid_form) {
    $app->flash('error', $errors);
    $app->redirectTo('masteradd');
  }

  if ($v->addCredential($post_data)) {
    $app->flash('success','Credential added successfully.');
  } else {
    $app->flash('error','Error while adding credential.');
  }

  $app->redirectTo("master");
}); 

$app->get('/master/:id', function($id) use ($app) {
	$vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);
  $credential = $v->getCredential($id);
  // krumo($credential);
  $app->render('routes/masterone.html.twig', array(
  	'credential' => $credential,
  ));
})->name('masterone');

$app->get('/master/:id/edit', function($id) use ($app) {
  $vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);
  $credential = $v->getCredential($id);

  $app->render('routes/add.html.twig', array(
    'c' => $credential,
  ));
})->name('masteredit');

$app->post('/master/:id/edit', function($id) use ($app) {
  $vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);

  $post_data = $app->request->post();
  $post_data['id'] = $id;
  if ($v->editCredential($post_data)) {
    $app->flash('success','Credential edited successfully.');
  } else {
    $app->flash('error','Error while editing credential.');
  }
  $app->redirectTo("master");  
});

$app->get('/master/:id/delete', function($id) use ($app) {
  $vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);
  if ($v->deleteCredential($id)) {
    $app->flash('success','Credential deleted successfully.');
  } else {
    $app->flash('error','Error while deleting credential.');
  }
  $app->redirectTo("master");  
})->name('masterdelete');
