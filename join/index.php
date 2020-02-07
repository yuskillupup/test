<?php
require('../dbconnect.php');

session_start();

//★検証start
/*
$error['name'] = 'blank';
$errorData = $error['name'];
$name = 'name';
*/
//★検証end
//★エラー解消：Notice: Undefined variable: error
//対策：issetで配列が空であるかどうか確認
//注意点：事前にbkを取得した後、$errorで検索し、影響範囲を確認した



//★検証start
/*
//yes
if(isset($error['name'])){
  echo $error['name'];
  echo 'yes';
}else{
  echo "no";
}

//yes
if(isset($errorData)){
  echo $errorData;
  echo 'yes';
}else{
  echo "no";
}

//yes
if(isset($error[$name])){
  echo $error[$name];
  echo 'yes';
}else{
  echo "no";
}

//no 配列の引数にプロパティを代入
function errorCheck($key){
  if(isset($error[$key])){
    echo $error[$key];
  }else{
    echo 'no';
  }
}
errorCheck('name');
*/



//検証end

//（不採用対策）：errorを定義する
//不採用の理由：以下の処理だと送信条件のemptyの分岐がYESを通過しなくなる
//$error['name'] = '';
//$error['email'] = '';
//$error['password'] = '';
//$error['image'] = '';


//★エラー解消：<br /><b>Notice</b>:  Undefined index: name in <b>C:\xampp\htdocs\startphp\section5\bk\20200201\post\join\index.php</b> on line <b>94</b><br />
//対策：$_POST['name']、$_POST['email']などの存在チェックをする＋繰り返しの処理なので関数化
function errorEscapePost($post){
  isset($_POST[$post]) ? print(htmlspecialchars($_POST[$post], ENT_QUOTES)) : print('') ;
}
//GETも同様
function errorEscapeGet($get){
  isset($_GET[$get]) ? htmlspecialchars($_GET[$get], ENT_QUOTES) : print('') ;
}



if(!empty($_POST)){
  //エラー項目の確認
  if($_POST['name'] == ''){
    $error['name'] = 'blank';
  }
  if($_POST['email'] == ''){
    $error['email'] = 'blank';
  }
  if(strlen($_POST['password']) < 4){
    $error['password'] = 'length';
  }
  if($_POST['password'] == ''){
    $error['password'] = 'blank';
  }
  $fileName = $_FILES['image']['name'];
  if(!empty($fileName)){
    $ext = substr($fileName, -3);
    if($ext != 'jpg' && $ext != 'gif'){
      $error['image'] = 'type';
    }
  }

  //重複アカウントチェック
  if(empty($error)){
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
    $member->execute(array($_POST['email']));
    $record = $member->fetch();
    if($record['cnt'] > 0){
      $error['email'] = 'duplicate';
    }
  }

  if(empty($error)){
    //画像をアップロードする
    //アップロードファイルネーム編集
    $image = date('YmdHis') . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['image'] = $image;
    header('Location: check.php');
    exit();
  }
}


//書き直し
if(errorEscapeGet('action')){
  if($_GET['action'] == 'rewrite'){
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
  }
}

?>

<html lang="ja">
<head>
  <!doctype html>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="css/style.css">

<title>会員登録</title>
</head>
<body>
<header>
<h1>会員登録</h1>
</header>

<main>

<p>次のフォームに必要事項をご記入ください。</p>
<!--自分自身を呼び出すときはaction属性を空にする
ファイルの送信フォームがある場合はmultipart/form-data属性を必ず指定する
-->
<form action="" method="post" enctype="multipart/form-data">
  <dl>
    <dt>ニックネーム <span class="required">必須</span></dt>
    <dd>
      <input type="text" name="name" size="35" maxlength="255" value="<?php errorEscapePost('name')?>" />
    </dd>
    <!-- phpをif文1行で書くなら{}は不要、または3項演算子 -->
    <?php// if($error['name'] == "blank") echo $error['name'] ?>
    <?php if((isset($error['name']) == true ? $error['name'] : '') == "blank"): ?>
    <p class="error">* ニックネームを入力してください</p>
    <?php endif; ?>

    <dt>メールアドレス<span class="required">必須</span></dt>
    <dd>
      <input type="text" name="email" size="35" maxlength="255" value="<?php errorEscapePost('email') ;?>" />
    </dd>
    <?php if((isset($error['email']) == true ? $error['email'] : '')  == 'blank'): ?>
    <p class="error">* メールアドレスを入力してください</p>
    <?php endif ;?>
    <?php if((isset($error['email']) == true ? $error['email'] : '') == "duplicate"): ?>
    <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
    <?php endif ;?>


    <dt>パスワード<span class="required">必須</span></dt>
    <dd>
      <input type="password" name="password" size="10" maxlength="20" value="<?php errorEscapePost('password') ;?>" />
    </dd>
    <?php if((isset($error['password']) == true ? $error['password'] : '') == "blank") :?>
    <p class="error">* パスワードを入力してください</p>
    <?php endif ;?>
    <?php if((isset($error['password']) == true ? $error['password'] : '') == "length") :?>
    <p class="error">* パスワードは4文字以上で入力してください</p>
    <?php endif ;?>

    <dt>写真など</dt>
    <dd>
      <input type="file" name="image" size="35" />
      <?php if((isset($error['image']) == true ? $error['image'] : '') == 'type') :?>
      <p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
      <?php endif; ?>
      <?php if(!empty($error)): ?>
      <p class="error">*恐れ入りますが、画像を改めて指定してください</p>
      <?php endif; ?>
    </dd>

  </dl>
  <div><input type="submit" value="入力内容を確認する" /></div>
</form>



</main>
</body>
</html>
