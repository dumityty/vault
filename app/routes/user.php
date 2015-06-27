<?php

/**
 * User login
 */
$app->get('/user/login', $isLogged($app), function() use ($app) {
	$app->render('routes/user/user_login.html.twig', array());
})->name('login');

$app->post('/user/login', $isLogged($app), function() use ($app) {
	krumo($_SESSION);
	
	$post_data = $app->request->post();

	krumo($post_data);
  $user = new User();
  if ($user->login($post_data)) {
  	$_SESSION['user'] = array(
      'id' => $user->id,
      'keys' => array(
        // then when you try to access a different vault you will go to a form
        // which you then fill to decrypt the specific key for that vault
        // by default get master or personal vault key? - for testing now just get master
        'master' => $user->getKey(),
      ),
    );
		if (isset($_SESSION['login_redirect'])) {
      $login_redirect = $_SESSION['login_redirect'];
      unset($_SESSION['login_redirect']);
      $app->redirect($login_redirect);
    }
    else {
      $app->redirect('/');      
    }
  }
  else {
  	$app->flash('error', 'Login not successful.');
  	$app->redirectTo('login');
  }
});

$app->get('/user/logout', $authenticate($app), function () use ($app) {
  unset($_SESSION['user']);
  $app->view()->setData('user', null);
  $app->redirect('/');
})->name('logout');