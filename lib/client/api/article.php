<?php

$base_uri = "/api/article"; // get the model's route

// like it (and vote)
$app->post("$base_uri/:id/like", function($id) use($app, $client, $model_url) {
  $member_id = $_SESSION['member_id'];
  $data = $client->post("$model_url/$id/like", ['member_id'=>$member_id]);
  echo $data;
});

// get likes
$app->get("$base_uri/:id/members", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/likes");
  echo $data;
});

// get option 
$app->get("$base_uri/:id/option", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/option");
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
  $params = json_encode($app->request()->post());
  // validate params
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
