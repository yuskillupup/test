<?php
/* ここに、PHPのプログラムを記述します */
try{
  //行いたい処理
  //$dbにインスタンスを生成
  $db = new PDO('mysql:dbname=mydb5;host=127.0.0.1;charset=utf8', 'root','');
  //catch(エラークラス　エラーのインスタンスを入れる変数)
}catch(PDOException $e){
  //tryの処理ができなかった場合の処理：
  echo 'DB接続エラー：'.$e->getMessage();
}


?>
