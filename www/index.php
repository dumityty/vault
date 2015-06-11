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

/**
 * Add username and settings variable to view
 */
$app->hook('slim.before.dispatch', function () use ($app) {
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;  
    $app->view()->setData('user', $user);
    
    // check if on a vault route
    $route = $app->router()->getCurrentRoute();
    $params = $route->getParams();
    // $vault = $route->getParam('vault');

    if (isset($params['vault'])) {
      $app->view()->setData('vault', $params['vault']);
    }
});

// the default root endpoint
$app->get('/', function() use ($app) {
  $app->redirectTo('vault', array('vault' => 'master'));

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

