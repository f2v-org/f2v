<?php
/**
 * Group Functions:
 * - CRUD
 * - add: a group should have a default rep 'Major Vote'
 * - get_by_id
 * - get_by_name
 */

$base_uri = "/api/group"; // get the model's route

/* group_type functions */
// get group types 
$app->get("$base_uri/types", function($id) use($app, $db) {
  $data = $db->from("group_type")->all();
  echo json_encode($data);
});
// add group type 
$app->post("$base_uri/types", function($id) use($app, $db) {
  $params = $app->request()->post();
  // validate params
  $data = $db->from("group_type")->insert($params);
  echo json_encode($data);
});
// update group type 
// delete group type 
/* /group_type functions */

/** 
 * add a member to the group
 * input: member_id
 * returns: TRUE/FALSE
 */
$app->post("$base_uri/:id/join", function($id) use($app, $db) {
  $params = $app->request()->post();
  $member_id = $params['member_id'];
  // verify that member exists
  $result = $db->from("group_member")->insert(["group_id"=>"$id", "member_id"=>"$member_id"]);
  echo json_encode($result);
});

// stand member as a rep
$app->put("$base_uri/:id/stand", function($id) use($app, $db) {
  $params = $app->request()->put();
  $member_id = $params['member_id'];
  // verify that member exists
  //$sql = "UPDATE `group_member` SET is_rep = :is_rep
  //  WHERE group_id = :group_id AND member_id = :id";
  $result = $db->from("group_member")->where(["group_id"=>"$id", "member_id"=>"$member_id"])
    ->update(["is_rep"=>TRUE]);
  echo json_encode($result);
});

// get group members
$app->get("$base_uri/:id/members", function($id) use($app, $db) {
  //$sql = "select * from member m, group_member gm 
  //  where gm.group_id = :id and gm.member_id = m.id";
  $members = $db->from("member");
  $gm = $db->from("group_member")->where(["group_id"=>"$id"]);
  $data = $members->join($gm, ["member_id"=>"id"])->all();
  echo json_encode($data);
});

// get group reps 
$app->get("$base_uri/:id/reps", function($id) use($app, $db) {
  //$sql = "select * from member m, group_member gm 
  //  where gm.group_id = :id and gm.member_id = m.id and gm.is_rep = TRUE";
  $members = $db->from("member");
  $gm = $db->from("group_member")->where(["group_id"=>"$id", "is_rep"=>TRUE]);
  $data = $members->join($gm, ["member_id"=>"id"])->all();
  echo json_encode($data);
});

// get group polls 
$app->get("$base_uri/:id/polls", function($id) use($app, $db) {
  $data = $db->from("poll")->where(["group_id"=>"$id"])->all();
  echo json_encode($data);
});


?>
