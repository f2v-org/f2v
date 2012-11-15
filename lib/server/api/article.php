<?
/*
 * Article:
 * - CRUD
 * - add (vote)
 * - like (vote)
 * - get_likes
 * - get_by_id
 * - get_by_title
 */

// get members who like it
$app->get("$base_uri/:id/members", function($id) use($app, $db) {
  $data = $db->from("member_article")->where(["article_id"=>"$id"])->all();
  echo json_encode($data);
});

// get the option for this article
$app->get("$base_uri/:id/option", function($id) use($app, $db) {
  $article = $db->from("article")->where(["id"=>"$id"])->all();
  $option_id = $article['option_id'];
  $result = $db->from("option")->where(["id"=>"$option_id"])->all();
  echo json_encode($result);
});

// like. Note: liking an article automatically creates a vote for 
// the option the article links to. 
$app->post("$base_uri/:id/like", function($id) use($app, $db) {
  $params = $app->request()->post();
  $member_id = $params['member_id'];
  $ma = $db->from("member_article");
  $like = $ma->where(["member_id"=>"$member_id", "article_id"=>"$id"])->all();
  if ( ! $like )  // create like
    $result = $ma->insert(["member_id"=>"$member_id", "article_id"=>"$id"]);
  // create/update vote
  // get the option id, and poll id
  $article = $db->from("article")->where(["id"=>"$id"])->all();
  $option_id = $article['option_id'];
  $option = $db->from("option")->where(["id"=>"$option_id"])->all();
  $poll_id = $option['poll_id'];
  // create/update vote
  $mp = $db->from("member_poll")
    ->where(["member_id"=>"$member_id","poll_id"=>"$poll_id"]);
  $vote = $mp->all();
  if ($vote) {
    $result = $mp->update(["option_id"=>"$option_id"]);
  } else {
    $result = $mp->insert([
      "member_id"=>"$member_id", 
      "poll_id"=>"$poll_id", 
      "option_id"=>"$option_id"
    ]);
  }
  echo json_encode($result);
});

?>
