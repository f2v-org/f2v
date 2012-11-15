<?php
/*
 * Option:
 * - CRUD
 * - add (vote_for)
 * - update/change (vote)
 * - get_by_title
 * - get_articles
 * - get_voters
 */

$base_uri = "/api/option"; // get the model's route

// get articles
$app->get("$base_uri/:id/articles", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/articles");
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
  $member_id = $_SESSION['member_id'];
  array_push($params, ['created_by'=>$member_id]);
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
  $params = $app->request()->delete();
  // validate params
  $data = $client->delete("$model_url/$id", $params);
  echo $data;
});

?>
