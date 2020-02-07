<?php
/* ここに、PHPのプログラムを記述します */
try{
  //行いたい処理
  //$dbにインスタンスを生成

  $server   = "mysql111.phy.lolipop.lan";              // 実際の接続値に置き換える
  $user     = "LAA0988684";                           // 実際の接続値に置き換える
  $pass     = "1234";                                  // 実際の接続値に置き換える
  $database = "LAA0988684-mydb5";                      // 実際の接続値に置き換える

  //-------------------
  //DBに接続
  //-------------------
  /*
  $conn = mysql_connect( $server, $user, $pass );
  mysql_set_charset( 'utf8', $conn );
  */

  $db = new PDO('mysql:dbname=LAA0988684-mydb5;host=localhost;charset=utf8', 'LAA0988684','1234');

/*
  //$db = new PDO('mysql:dbname=LAA0988684-mydb5;host=localhost;charset=utf8', 'root','');
  //$db = new PDO('mysql:dbname=mydb5;host=localhost;charset=utf8', 'LAA0988684','1234');
  //catch(エラークラス　エラーのインスタンスを入れる変数)
*/
}catch(PDOException $e){
  //tryの処理ができなかった場合の処理：
  echo 'DB接続エラー：'.$e->getMessage();
}


?>
