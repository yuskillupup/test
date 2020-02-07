<?php
session_start();
require('dbconnect.php');

//ログインをしているかチェック
if(isset($_SESSION['id'])){
  $id = $_GET['id'];

  //投稿を検査する　
  $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
  $messages->execute(array($id));
  $message = $messages->fetch();

  if($message['member_id'] == $_SESSION['id']){
    //削除する
    $del = $db->prepare('DELETE FROM posts WHERE id=?');
    $del->execute(array($id));
  }
}

header('Location: index.php');
exit();

?>
<html lang="ja">
<head>
  <!doctype html>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="css/style.css">

<title>ひとこと掲示板</title>
</head>
<body>
<main>



</main>
</body>
</html>
