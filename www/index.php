<?php
session_cache_limiter(false);
session_start();

require '../vendor/autoload.php';

// Include the app configuration file.
// require_once dirname(dirname(__FILE__)) . '/app/config.php';
// Include the DBHandler class.
// require_once dirname(dirname(__FILE__)) . '/app/lib/DbHandler.php';
// Include the Helper functions file.
// require_once dirname(dirname(__FILE__)) . '/app/lib/helper.php';
// require_once dirname(dirname(__FILE__)) . '/app/lib/Vault.php';

$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('slim-skeleton');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

// Include the main app file which includes routes, middleware, etc
include dirname(dirname(__FILE__)) . '/app/includes.php';

// the default root endpoint
$app->get('/', function() use ($app) {

  $vault = "mastervault";
  $key = "masterkey";
  $v = new Vault($vault, $key);

  // $c = $v->getCredential('1');
  // if (!isset($c)) {
  //   $app->flashNow('error','Credential could not be found.');    
  // }

  // $new_c = array(
  //   'site' => 'google',
  //   'username' => 'root',
  //   'password' => 'root',
  //   'url' => 'google.com',
  // );
  // $v->addCredential($new_c);

  // $all = $v->getAllCredentials();
  // krumo($all);

  // $encrypted_txt = $v->encrypt('123', 'secret');
  // $decrypted_txt = $v->decrypt($encrypted_txt, 'secret');

  // echo $encrypted_txt;
  // echo "<br>";
  // echo $decrypted_txt;

  $app->render('routes/index.html.twig', array(
  ));
})->name('home');

// example of endpoint that return json
$app->get('/json-example', function() use ($app) {
  try {
    $data = array('hello' => 'world');
  }
  catch(Exception $e) {
    $data = array(
      'error' => array(
        'message' => $e->getMessage(),
      ),
    );
  }

  setResponse(200, $data);
});

$app->run();

