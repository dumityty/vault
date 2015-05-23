<?php

$app->get('/master', function() use ($app) {
	$vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);
  $credentials = $v->getAllCredentials();
  krumo($credentials);
  $app->render('routes/master.html.twig', array(
  	'credentials' => $credentials,
  ));
})->name('master');