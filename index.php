
<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
  //ログインしている
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
}else{
  //ログインしていない
  header('Location: login.php');
  exit();
}

//投稿内容を記録する
if(!empty($_POST)){
  if($_POST['message'] != ''){
    $message = $db->prepare('INSERT INTO posts SET message=?, member_id=?,reply_post_id=?,created=NOW()');
    $message->execute(array(
      $_POST['message'],
      $member['id'],
      $_POST['reply_post_id']
    ));

    header('Location: index.php');
    exit();
  }
}

//投稿を取得する
/*
$page = $_GET['page'];
if($page == ''){
  $page = 1;
}
*/
if(isset($_GET['page']) && is_numeric($_GET['page'])){
  $page = $_GET['page'];
}else{
  $page = 1;
}

//maxファンクション　指定されたパラメタのうち大きい方を返す
//マイナスが設定された場合の対応
$page = max($page, 1);

//最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
//mixファンクション　指定されたパラメタのうち小さい方を返す
$page = min($page, $maxPage);

$start = ($page - 1) * 5;

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m INNER JOIN posts p ON m.id = p.member_id ORDER BY p.created DESC LIMIT ?, 5');

$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();
//$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id ORDER BY p.created DESC');

//返信の場合
if(isset($_GET['res'])){
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
  $response->execute(array($_GET['res']));

  $table = $response->fetch();
  $message = '@' . $table['name'] . ' ' . $table['message'];
}

//htmlspecialcharsを関数化
function h($value){
  return htmlspecialchars($value, ENT_QUOTES);
}

//本文内のURLにリンクを設定します
function makeLink($value){
  return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)",'<a href="\1\2">\1\2</a>', $value);
}

?>

<html lang="ja">
<h  ead>
<!doctype html>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="css/style.css">

<title>メッセージ投稿</title>
</head>
<body>
<header>
  <h2>ひとこと掲示板</h2>
</header>

<main>




  <form action="" method="post">
    <div style="text-align: right">
      <a href="logout.php">ログアウト</a>
    </div>
    <dl>
      <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
      <dd>
        <textarea name="message" cols="50" rows="5"><?php
        //if(isset($message)) {
        //  echo $message;
        //}
        print(isset($message) ? h($message) : "");
        //以下の記述だとエラーメッセージが表示される
         //htmlspecialchars($message,ENT_QUOTES);
         ?></textarea>
        <!-- 返信先のIDを記録しておく-->
        <input type="hidden" name="reply_post_id" value="<?php echo h($_GET['res']) ;?> "/>
      </dd>
    </dl>
    <div>
      <input type="submit" value="投稿する" />
    </div>
  </form>

<?php
foreach ($posts as $post):
?>
  <div class="msg">
    <img src="member_picture/<?php echo h($post['picture']) ;?>" width="48" height="48" alt="<?php echo h($post['name']) ;?>" />
  <p><?php echo makeLink(h($post['message']));?><span class="namse">(<?php echo h($post['name']) ;?>)</span>
  [<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]
  </p>
  <p class="day">
    <a href="view.php?id=<?php echo h($post['id']) ;?> ">
      <?php echo h($post['created']) ;?>
    </a>
    <span>　</span>
    <?php if($post['reply_post_id'] > 0): ?>
    <a href="view.php?id=<?php echo h($post['reply_post_id']) ;?>">
      返信元のメッセージ
    </a>
    <?php endif ; ?>

    <?php
    if($_SESSION['id'] == $post['member_id']):
    ?>
      [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color:#F33;">削除</a>]
    <?php
    endif;
    ?>

  </p>
  </div>
<?php endforeach; ?>

<ul class="paging">
<?php if($page > 1) :?>
  <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
<?php else :?>
  <li>前のページへ</li>
<?php endif ;?>
<?php if($page < $maxPage) :?>
  <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
<?php else :?>
  <li>次のページへ</li>
<?php endif ;?>
</ul>

</main>
</body>
</html>
