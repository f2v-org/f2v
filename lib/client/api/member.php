<?php
/*
 * Member:
 * - register: default member 'World' 
 * - login(email, password)
 * - logout
 * - get_by_email
 */

$base_uri = "/api/member"; // get the model's route

/* 
 * login 
 * input: email, password
 */
$app->post("$base_uri/login", function() use($app, $client, $model_url) {
  $params = json_encode($app->request()->post());
  // validate params
  $data = $client->post("$model_url", $params);
  if ($data) {
    $_SESSION['member_id'] = json_decode($data); 
  } else {
    return FALSE;
  }
});

// logout
$app->post("$base_uri/logout", function() use($app, $client, $model_url) {
  if ( isset($_SESSION['member_id']) ) {
    unset($_SESSION['member_id']);
    return TRUE;
  } else {
    return FALSE;
  }
});

// get member groups 
$app->get("$base_uri/:id/groups", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/groups");
  echo $data;
});

// get member reps 
$app->get("$base_uri/:id/reps", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/reps");
  echo $data;
});

// get member polls 
$app->get("$base_uri/:id/polls", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/polls");
  echo $data;
});

// get member votes 
$app->get("$base_uri/:id/votes", function($id) use($app, $client, $model_url) {
  $data = $client->get("$model_url/$id/votes");
  echo $data;
});

/* 
 * elect the member as a rep
 * input: member_id
 */
$app->get("$base_uri/:id/elect", function($id) use($app, $client, $model_url) {
  $params = $app->request()->put();
  //$member_id = $params['member_id'];
  $member_id = $_SESSION['member_id'];
  array_push($params, ['member_id'=>"$member_id"]);
  $data = $client->put("$model_url/$id/elect", json_encode($params));
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
