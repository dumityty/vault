<?php

$app->get('/add', $authenticate($app), function() use ($app) {
  $app->render('routes/add.html.twig', array(
    // 'c' => $credential,
  ));
})->name('add');
  
$app->post('/add', function() use ($app) {
  $vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);

  $post_data = $app->request->post();

  if ($v->addCredential($post_data)) {
    $app->flash('success','Credential edited successfully.');

  } else {
    $app->flash('error','Error while editing credential.');
  }
  $app->redirectTo("master");  
});