<?php
session_start();

if($_SERVER['REQUEST_METHOD']==='POST'){
    header('Location:https://localhost/bbs/user.php');
}
  
function h($s){
  return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

try{

  $pdo = new PDO(
   'mysql:host=127.0.0.1;dbname=test;charset=utf8',
    "root",
    "",
      [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]
  );

} catch (PDOException $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
}

try{
  //未ログインは弾く
  if(!isset($_SESSION["auth"])){
    $errors = "不正な画面遷移です";
    throw new Exception($errors);
  }

  //投稿を表示(管理人)
    if($_SESSION["auth"] == 666){
    $show = 'SELECT * FROM bbs';
    $bbs = $pdo ->prepare($show);
    $bbs -> execute();
  }else{
    //投稿を表示
    $show = 'SELECT * FROM bbs WHERE id = ?';
    $bbs = $pdo ->prepare($show);
    $bbs -> bindvalue(1,$_SESSION["id"],PDO::PARAM_STR);
    $bbs -> execute();
  }

  //更新日時を引っ張ってくる
  $updated_at_sql = 'SELECT updated_at FROM users WHERE id = ?';
  $stmt = $pdo -> prepare($updated_at_sql);
  $stmt -> bindvalue(1,$_SESSION["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $row = $stmt->fetch();

  $updated = $row["updated_at"];  

  //魔王様(管理人)専用　
  if(isset($_POST["del"])){
    $del_sql="DELETE FROM bbs WHERE unique_key = ?";
    $stmt = $pdo -> prepare($del_sql);
    $stmt -> bindvalue(1,$_POST["del"],PDO::PARAM_STR);
    $stmt -> execute();
  }

  //魔王様(管理人)専用
  if(isset($_POST["del-all"])){
    $del_all_sql="DELETE FROM bbs";
    $stmt = $pdo -> prepare($del_all_sql);
    $stmt -> execute();
  }
  
  //アイコンの参照
  $icon_sql = 'SELECT icon_path FROM images WHERE id = ?';
  $stmt = $pdo -> prepare($icon_sql);
  $stmt -> bindvalue(1,$_SESSION["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $row = $stmt->fetch();

  if($row["icon_path"] === NULL){
    $icon_path = "./img/icon.png";
  }else{  
    $icon_path = $row["icon_path"];
    
  }

} catch (Exception $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
}


?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" 
    crossorigin="anonymous">
    <link rel="icon" href="img/favicon.ico">
    <title>ユーザー管理</title>
  </head>
  <body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <span class="navbar-brand">CRUD掲示板</span>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
          <?php
          if($_SERVER["REQUEST_URI"] != "user.php" && isset($_SESSION["user_name"])){
            echo '<a class="nav-link" href="index.php">BBS</a>';
          }elseif(isset($_SESSION["user_name"])){
            echo '<a class="nav-link" href="user.php">Users</a>';
          }
          ?> 
          </li>
          <li class="nav-item">
          <?php
          if(empty($_SESSION["user_name"])){
            echo '<a class="nav-link" href="signin.php">Sign in</a>'; 
          }else{
            echo '<a class="nav-link" href="signout.php"">Sign out</a>';
          }?>   
          </li> 
        </ul>
      </div>
    </nav>
    <div class="container">
      <div class="row justify-content-center">
        <div class="shadow" style="width:40rem; margin-top: 1rem; margin-bottom: 1rem;">
          <div class="bg-dark rounded-top text-light text-center" style="padding:0.5rem;">
            <h2>ユーザー情報</h2>
          </div>         
          <div class="m-auto" style="width:20rem; text-align:start;">
            <div style="margin:1rem; padding:1rem;">
              <div class="justify-content-center">
                <div style="margin-left:3rem;">
                  <img class="rounded-circle shadow " 
                  style="width:150px; height:150px; margin-bottom:2rem;" src="<?= $icon_path; ?>" alt="アイコン"> 
                </div>
              </div>
              <ul class="list-inline">
                <li class="list-inline-item">ユーザー名：</li>
                <li class="list-inline-item"><?= h($_SESSION["user_name"]) ?></li>
              </ul>
                <ul class="list-inline">
                <li class="list-inline-item">ユーザーID：</li>
                <li class="list-inline-item"><?= h($_SESSION["id"]) ?></li>
              </ul>
              <ul class="list-inline">
                <li class="list-inline-item">性別：</li>
                <li class="list-inline-item"><?= $_SESSION["sex"] ?></li>
              </ul>
              <ul class="list-inline">
                <li class="list-inline-item">登録日時：</li>
                <li class="list-inline-item"><?= $_SESSION["registration_date"] ?></li>
              </ul>
              <ul class="list-inline">
                <li class="list-inline-item">更新日時：</li>
                <li class="list-inline-item"><?= $updated ?></li>
              </ul>
              <a href="user_infomation.php" style="text-decoration:none;">
                <button class="btn btn-primary">ユーザー情報を変更</button>
              </a>　   
            </div>
          </div>
        </div>       
      </div>
      <div class="row">
        <table class="table table-striped">
          <?php
          if($_SESSION["auth"] == 666):?>
          <h2><?="全レス一覧"?></h2>
          <?php else :?>
          <h2><?="あなたのレス一覧"?></h2>
          <?php endif; 
          while ($row = $bbs->fetch()) {
          ?>
          <tr>
            <td><?= $row["user_name"]?></td>
            <td><?= $row["post_time"]?></td>
            <td><?= h($row["content"])?></td>
            <td>
            <?php 
              if($_SESSION["auth"] == 666):?>
              <form method="post" action="user.php">
                <input type="hidden" name="del" value="<?=$row['unique_key']?>">
                <input class="btn btn-danger" type="submit" value="削除">
              </form>
              <?php endif; ?>   
            </td>  
          </tr>  
        <?php } ?>
        </table>
      </div>
      <?php
      if($_SESSION["auth"] == 666):?>
      <div class="row" style="margin-top: 1rem;margin-bottom:1rem;">
        <div class="bg-dark shadow rounded col-2">      
          <div class="text-white">
            <h2>全レス削除</h2>
          </div>
          <form class="form text-center" method="post" action="user.php">
            <div class="form-group">
              <input type="hidden" name="del-all" value="hoge">
              <input class="btn btn-danger" type="submit" value="テポドン！">
            </div>
          </form>
        </div>
      </div>  
      <?php endif; ?>     
    </div>   
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
    integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
    crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script>
    <script src="http://fb.me/react-0.13.3.js"></script>
    <script src="http://fb.me/JSXTransformer-0.13.3.js"></script>
  </body>
</html>