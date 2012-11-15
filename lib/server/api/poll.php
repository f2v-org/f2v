<?
/*
 * Poll:
 * - CRUD: a poll should:
 *    -- have a default option.
 *    -- be linked to a group.
 * - add (vote)
 * - get_by_title
 * - get_options
 * - get_voters
 */

$base_uri = "/api/poll";    // get the model's route

// get options 
$app->get("$base_uri/:id/options", function($id) use($app, $db) {
  //$sql = "select * from `option` where poll_id = :id";
  $data = $db->from("option")->where(["poll_id"=>"$id"])->all();
  echo(json_encode($data));
});

// get voters
$app->get("$base_uri/:id/voters", function($id) use($app, $db) {
  //$sql = "select * from member m, member_poll v 
  //  where v.member_id = m.id and v.poll_id = :id";
  $members = $db->from("member");
  $votes = $db->from("member_poll")->where(["poll_id"=>"$id"]);
  $data = $members->join($votes, ["member_id"=>"id"])->all();
  echo json_encode($data);
});

?>
