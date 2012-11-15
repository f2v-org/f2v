<?php

// get the model's route
$base_uri = "/api/member";

/* 
 * login
 * input: email, password
 * return: member_id on success
 */
$app->post("$base_uri/login", function() use($app, $db) {
  $params = json_decode($app->request()->put());
  // check params
  $email = $params['email'];
  //$sql = "select * from member where email = $email";
  $dataset = $db->from("member")->where(['email'=>"$email"]);
  $member = $dataset->all();
  if ($params['password'] == $member['password']) {
    $dataset->update(["last_login"=>date("Y-m-d H:i:s")]);
    return json_encode($member['id']);
  } else {
    return FALSE;
  }
});

// elect a member as a rep
$app->put("$base_uri/:id/elect", function($id) use($app, $db) {
  $params = $app->request()->put();
  $group_id = $params['group_id'];
  $member_id = $params['member_id'];
  //$rep_id = $params['rep_id'];
  // verify that member exists
  $result = $db->from("group_member")->where(["group_id"=>"$group_id", "member_id"=>"$member_id"])
    ->update(["rep_id"=>"$id"]);
  echo json_encode($result);
});

// get groups 
$app->get("$base_uri/:id/groups", function($id) use($app, $db) {
  //$sql = "select * from `group` g, `group_member` gm 
  //  where gm.member_id = :id and gm.group_id = g.id";
  $groups = $db->from("group");
  $gm = $db->from("group_member")->where(["member_id"=>"$id"]);
  $data = $groups->join($gm, ["group_id"=>"id"])->all();
  echo json_encode($data);
});

// get votes 
$app->get("$base_uri/:id/votes", function($id) use($app, $db) {
  //$sql = "select * from `poll` p, `member_poll` v 
  //  where v.member_id = :id and v.poll_id = p.id";
  $polls = $db->from("poll");
  $votes = $db->from("member_poll")->where(["member_id"=>"$id"]);
  $data = $polls->join($votes, ["poll_id"=>"id"])->all();
  echo json_encode($data);
});

// get member reps 
$app->get("$base_uri/:id/reps", function($id) use($app, $db) {
  //$sql = "select * from member m, group_member gm 
  //  where gm.member_id = :id and gm.rep_id = m.id";
  $members = $db->from("member");
  $gm = $db->from("group_member")->where(["member_id"=>"$id"]);
  $data = $members->join($gm, ["rep_id"=>"id"])->all();
  echo json_encode($data);
});

// get member polls 
$app->get("$base_uri/:id/polls", function($id) use($app, $db) {
  //$sql = "select * from poll where member_id = :id";
  $data = $db->from("poll")->where(['member_id'=>"$id"])->all();
  echo json_encode($data);
});

?>
