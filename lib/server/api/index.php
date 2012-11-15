<?php
/**
 * f2v Server 
 * Provides a RESTful api to perform database operations
 */

/** 
 * FIXME: 
 * SQL injection, XSS, CSRF
 * authentication, authorization
 */

$root_dir = realpath('../../../'); 
require "$root_dir/config/server.php";
require "$root_dir/lib/db.php";
// Load required packages e.g. Slim framework
require "$root_dir/vendor/autoload.php";

// configure Slim
$app = new \Slim\Slim(array(
  'mode' => "$GLOBALS[slim_mode]"
));
$app->contentType("application/json"); 
$app->add(new \Slim\Middleware\ContentTypes());

$db = new DB($GLOBALS['db_params']);

$path = $app->request()->getPath(); 
$res = preg_match('#^/api/([[:word:]]+)/?#i', $path, $match);
if ($res) {
  $model = $match['1'];
  $app->hook("slim.before.router", function() use($app, $db, $model){
    require 'routes.php';
    switch ($model) {
      case 'group':
        require 'group.php';
        break;
      case 'member':
        require 'member.php';
        break;
      case 'poll':
        require 'poll.php';
        break;
      case 'option':
        require 'option.php';
        break;
      case 'article':
        require 'article.php';
        break;
      default:
        break;
    }
  });
}

$app->run();

?>
