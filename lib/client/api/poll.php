<?php
/*
 * Poll:
 * - CRUD: a poll should:
 *    -- have a default option.
 *    -- be linked to a group.
 */

$base_uri = "/api/poll"; // get the model's route

// get options
$app->get("$base_uri/:id/options", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/options");
  echo $data;
});

// get voters 
$app->get("$base_uri/:id/voters", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/voters");
  echo $data;
});


/* CRUD OPERATIONS */

// GET ALL 
$app->get("$base_uri", function() use($app, $client, $model_url) {
  $data = $client->get("$model_url");
  echo $data;
});

// GET ONE 
$app->get("$base_uri/:id", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id");
  echo $data;
});

// ADD 
$app->post("$base_uri", function() use($app, $client, $model_url) {
  $params = $app->request()->post();
  // validate params
  // default option.
  $option_id = $params['option_id']; 
  // group
  $group_id = $params['group_id']; 
  $data = $client->post("$model_url", $params);
  echo $data;
});

// UPDATE 
$app->put("$base_uri/:id", function($id) use($app, $client, $model_url) {
  $params = $app->request()->put();
  // validate params
  $res = $client->put("$model_url/$id", $params);
  echo $res;
});

// DELETE 
$app->delete("$base_uri/:id", function($id) use($app, $client, $model_url) {
  $params = json_encode($app->request()->delete());
  // validate params
  $data = $client->delete("$model_url/$id", $params);
  echo $data;
});

?>
