<?php
session_start();

//バリデーション
try{

  if(!filter_input(INPUT_POST,"user_name") || !filter_input(INPUT_POST,"password") || 
    !filter_input(INPUT_POST,"user_id") || !filter_input(INPUT_POST,"sex")){
    $errors = "入力欄を全て入力してください";
    throw new Exception($errors);
    
  }elseif(mb_strlen($_POST["user_name"]) < 3){
    $errors = "ユーザー名は3文字以上にしてください";
    throw new Exception($errors);

  }elseif(mb_strlen($_POST["user_name"]) > 12){
    $errors = "ユーザー名は12文字以下にしてください";
    throw new Exception($errors);

  }elseif(mb_strlen($_POST["user_id"]) < 6){
    $errors = "ユーザーIDは6文字以上にしてください";
    throw new Exception($errors);

  }elseif(mb_strlen($_POST["user_id"]) > 12){ 
    $errors = "ユーザーIDは12文字以下にしてください";
    throw new Exception($errors);

  }elseif(mb_strlen($_POST["password"]) < 6){
    $errors = "パスワードは6文字以上にしてください";
    throw new Exception($errors);

  }elseif(mb_strlen($_POST["password"]) > 24){
    $errors = "パスワードは24文字以下にしてください";
    throw new Exception($errors);
  } 

}catch (Exception $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
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


} catch (PDOException $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
}

//登録
try{
  
  $user_name = (string) filter_input(INPUT_POST,"user_name");
  $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $sex = (string) filter_input(INPUT_POST,"sex");
  $id = (string) filter_input(INPUT_POST,"user_id");

  $stmt = $pdo-> prepare(
    'INSERT INTO users (id,user_name,sex,registration_date,auth,updated_at,hash)
    VALUES (:id,:user_name,:sex,NOW(),"1",NOW(),:hash);'
  );

  $stmt -> bindValue(":id", $id, PDO::PARAM_STR);
  $stmt -> bindValue(":user_name", $user_name, PDO::PARAM_STR);
  $stmt -> bindValue(":hash", $hash, PDO::PARAM_STR);
  $stmt -> bindValue(":sex", $sex, PDO::PARAM_STR);

  if(!$stmt->execute()){
    $errors = "登録できませんでした";
  }

} catch (Exception $errors) {
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
    <title>登録完了</title>
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
        <h2 class="text-center" style="font-size: 50px;"><?="ようこそ、".$user_name."さん";?></h2>
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
