<?php
/***
 * Client
 * Provides the public interface (api) to the database operations thro the 
 * server's api.
 */

// FIXME: 
// * SQL injection, XSS, CSRF
// * authentication, authorization

$root_dir = realpath('../../../'); 
// Load required packages e.g. Slim framework
require "$root_dir/vendor/autoload.php";
require "$root_dir/lib/rest-client/rest_client.php";
require "$root_dir/config/client.php";

// configure Slim
$app = new \Slim\Slim(array(
  'mode' => "$GLOBALS[slim_mode]"
));
$app->add(new \Slim\Middleware\ContentTypes());
$app->contentType('application/json');

$client = new RestClient(); // the http client
$base_url = "$GLOBALS[server_url]"; // the server's url

$path = $app->request()->getPath();
$res = preg_match('#^/api/([[:word:]]+)/?#i', $path, $match);
if ($res) {
  $model = $match['1'];
  $model_url = "$base_url/$model";
  $app->hook("slim.before.router", function() use($app, $client, $model, $model_url) {
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
