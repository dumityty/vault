<?php
/**
 * This is an 'includes' file.
 * You can include any files you will need for your adaptor here.
 * As a start, there are two examples of code to 'include' your adaptor routes and middleware.
 * You can extend this file to include classes, other files, etc.
 */
// MIDDLEWARES
$middleware = glob(dirname(__FILE__) . '/middleware/*.php');
foreach ($middleware as $key => $file) {
  include $file;
}
// ROUTES
$routes = glob(dirname(__FILE__) . '/routes/*.php');
foreach ($routes as $key => $file) {
  include $file;
}

// OTHER
include dirname(__FILE__) . "/config.php";
include dirname(__FILE__) . "/lib/helper.php";
include dirname(__FILE__) . "/lib/Vault.php";
