<?php
/*
 * Group Functions:
 * - CRUD
 * - add: a group should have a default rep 'Major Vote'
 * - get_all
 * - get_by_id
 * - get_by_name
 * - get_members
 * - get_polls
 * - get_reps(grp_id)
 */

$base_uri = "/api/group"; // get the model's route

/* GROUP MEMBERS OPERATIONS */
// get group members
$app->get("$base_uri/:id/members", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/members");
  echo $data;
});

/**
 * add member to the group
 * input: member_id
 */
$app->post("$base_uri/:id/join", function($id) use($app, $client, $model_url) {
  // validate params
  $params = $app->request()->post();
  $res = $client->post("$model_url/$id/join", json_encode($params));
  echo json_decode($res);
});

/* 
 * stand member as a rep
 * input: member_id
 */
$app->put("$base_uri/:id/stand", function($id) use($app, $client, $model_url) {
  // validate params
  $params = $app->request()->put();
  //$member_id = $params['member_id'];
  $data = $client->put("$model_url/$id/stand", json_encode($params));
  echo $data;
});

/* GROUP TYPE OPERATIONS */
// get group types
$app->get("$base_uri/types", function() use($app, $client, $model_url) {
  $types = $client->get("$model_url/types");
  echo json_decode($types);
});

/**
 * add group type
 */
$app->post("$base_uri/type", function() use($app, $client, $model_url) {
  $params = $app->request()->post();
  // validate params
  $res = $client->post("$model_url/type", $params);
  echo json_decode($res);
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
