<?php
/**
 * CRUD Operations
 */

$base_uri = "/api/$model";      // get the model's route
$dataset = $db->from("$model"); // get the dataset

// List
$app->get("$base_uri", function() use ($app, $dataset) {
  $data = $dataset->all();
  echo json_encode($data);
});

// Find
$app->get("$base_uri/:id", function($id) use ($app, $dataset) {
  $data = $dataset->where(["id"=>"$id"])->all();
  echo json_encode($data);
});

// Add
$app->post("$base_uri", function() use ($app, $dataset) {
  //$params = json_decode($app->request()->post());
  $params = $app->request()->post();
  $result = $dataset->insert($params);
  echo json_encode($result);
});

// Update
$app->put("$base_uri/:id", function($id) use ($app, $dataset) {
  $params = $app->request()->put();
  $result = $dataset->where(["id"=>"$id"])->update($params);
  echo json_encode($result);
});

// Delete
$app->delete("$base_uri/:id", function($id) use ($app, $dataset) {
  $result = $dataset->where(["id"=>"$id"])->delete();
  echo json_encode($result);
});

?>
