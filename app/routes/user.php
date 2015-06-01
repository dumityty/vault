<?php

/**
 * User login
 */
$app->get('/user/login', $isLogged($app), function() use ($app) {
	krumo($_SESSION);
  
  // $user['email'] = 'titi@zoocha.com';
  // $user['key'] = '123';
  // $u = new User();
  // $id = $u->add($user);
  // krumo($id);


	$app->render('routes/user/user_login.html.twig', array());
})->name('login');

$app->post('/user/login', $isLogged($app), function() use ($app) {
	krumo($_SESSION);
	
	$post_data = $app->request->post();

	krumo($post_data);
  $user = new User();
  if ($user->login($post_data)) {
  	$_SESSION['user'] = $user;
  	krumo($_SESSION);
		// $app->redirectTo('master');
  }
  else {
  	$app->flash('error', 'Login not successful.');
  	$app->redirectTo('login');
  }
});