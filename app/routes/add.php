<?php

$app->get('/add', function() use ($app) {
	$vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);
  $credential = array(
  	'site' => 'google',
  	'username' => 'root',
  	'password' => 'root',
  	'url' => 'google.com',
  );
  $v->addCredential($credential);
})->name('add');