<?
/*
 * Option:
 * - CRUD
 * - add (vote_for)
 * - update/change (vote)
 * - get_by_title
 * - get_articles
 * - get_voters
 */

$base_uri = "/api/option";    // get the model's route

// get voters
$app->get("$base_uri/:id/voters", function($id) use($app, $db) {
  // get the poll id
  $option = $db->from("option")->where(["option_id"=>"$id"])->all();
  $poll_id = $option['poll_id'];
  $votes = $db->from("member_poll")->where(["poll_id"=>"$poll_id"]);
  $members = $db->from("member");
  $data = $members
    ->join($votes, ["member_id"=>"id", "poll_id"=>"$poll_id", "option_id"=>"$id"])
    ->all();
  echo json_encode($data);
});

// get articles
$app->get("$base_uri/:id/articles", function($id) use($app, $db) {
  $data = $db->from("articles")->where(["option_id"=>"$id"]);
  echo json_encode($data);
});

// add vote 
$app->post("$base_uri/:id/vote", function($id) use($app, $db) {
  $params = $app->request()->post();
  $member_id = $params['member_id'];
  // get the poll id
  //$sql = "SELECT * FROM `option` WHERE option_id = :id";
  $option = $db->from("option")->where(["option_id"=>"$id"])->all();
  $poll_id = $option['poll_id'];
  //$sql = "INSERT INTO `member_poll` (member_id, poll_id, option_id)
  //  VALUES (:member_id, :poll_id, :option_id)";
  $result = $db->from("member_poll")
    ->where(["member_id"=>"$member_id","poll_id"=>"$poll_id"])
    ->insert([
        "member_id"=>"$member_id", 
        "poll_id"=>"$poll_id", 
        "option_id"=>"$id"
    ]);
  echo json_encode($result);
});

// update/change vote 
$app->put("$base_uri/:id/vote", function($id) use($app, $db) {
  //$params = $app->request()->put();
  //$member_id = $params['member_id'];
  $member_id = $_SESSION['member_id'];
  // get the poll id
  //$sql = "SELECT * FROM `option` WHERE option_id = :id";
  $option = $db->from("option")->where(["option_id"=>"$id"])->all();
  $poll_id = $option['poll_id'];
  //$sql = "UPDATE `member_poll` SET option_id = :option_id 
  //  WHERE member_id = :member_id AND poll_id = :poll_id";
  $result = $db->from("member_poll")
    ->where(["member_id"=>"$member_id","poll_id"=>"$poll_id"])
    ->update(["option_id"=>"$id"]);
  echo json_encode($result);
});

?>
