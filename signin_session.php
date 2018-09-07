<?php
session_start();

//バリデーション
try{

  if(!filter_input(INPUT_POST,"id") && !filter_input(INPUT_POST,"password")){
    $errors = "全て入力してください";
    throw new Exception($errors);
  }else if(!filter_input(INPUT_POST,"id")){
    $errors = "idが入力されていません";
    throw new Exception($errors);
  }else if( !filter_input(INPUT_POST,"password")){
    $errors = "パスワードが入力されていません";
    throw new Exception($errors);
  }

  //DB接続
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
    }catch (PDOException $errors){
      header('Content-Type: text/plain; charset=utf8', true, 500);
      exit($errors->getMessage());
    }
  
  //ハッシュ呼び出し  
  $hash_sql = 'SELECT hash FROM users WHERE id = ?';
  $stmt = $pdo -> prepare($hash_sql);
  $stmt -> bindvalue(1,$_POST["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $hash = $stmt->fetch();

  if(empty($hash)){
    $errors = "IDとPasswordが一致しません";
    throw new Exception($errors);
  }

  //ユーザー名呼び出し
  $username_sql = 'SELECT user_name FROM users WHERE id = ?';
  $stmt = $pdo -> prepare($username_sql);
  $stmt -> bindvalue(1,$_POST["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $row = $stmt->fetch();

  $username = $row["user_name"];

  //性別呼び出し
  $sex_sql = 'SELECT sex FROM users WHERE id = ?';
  $stmt = $pdo -> prepare($sex_sql);
  $stmt -> bindvalue(1,$_POST["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $row = $stmt->fetch();

  $sex = $row["sex"];

  //登録日呼び出し
  $registration_sql = 'SELECT	registration_date	FROM users WHERE id = ?';
  $stmt = $pdo -> prepare($registration_sql);
  $stmt -> bindvalue(1,$_POST["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $row = $stmt->fetch();

  $registration_date = $row["registration_date"];

  //ユーザーの権限
  $auth_sql = 'SELECT auth FROM users WHERE id = ?';
  $stmt = $pdo -> prepare($auth_sql);
  $stmt -> bindvalue(1,$_POST["id"],PDO::PARAM_STR);
  $stmt -> execute();
  $row = $stmt->fetch();

  $auth = $row["auth"];

}catch (Exception $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
}

//パスワードの照合
try{

    if(password_verify($_POST['password'], $hash["hash"])){

    $_SESSION["id"] = (string)filter_input(INPUT_POST,"id");
    $_SESSION["user_name"] = $username;
    $_SESSION["registration_date"] = $registration_date;
    $_SESSION["sex"] = $sex;
    $_SESSION["auth"] = $auth;
    
  }else{
    $errors = "IDとPasswordが一致しません";
    throw new Exception($errors);  
  }

}catch (Exception $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
}

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" 
    crossorigin="anonymous">
    <link rel="icon" href="img/favicon.ico">
    <title>サインイン認証</title>
  </head>
  <body>
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
            echo '<a class="nav-link" href="user.php">ユーザー管理</a>';
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
      <div style="margin-top:5rem;">
      <?php if($_SESSION["auth"] == 666): ?>
        <h2 class="text-center" style="font-size: 50px;"><?="お帰りなさいませ、管理人様。";?></h2>
      <?php else: ?>
        <p class="text-center"><?= $_SESSION["user_name"] ."さん、こんにちは！";?></p>
      <?php endif ?>
      </div>
      <div class="text-center" style="margin-top:10rem;">
        <a href="index.php" class="btn btn-primary">掲示板へ</a>
      </div>  
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
  </body>
</html>
