<?php
session_start();

if($_SERVER['REQUEST_METHOD']==='POST'){
    header('Location:https://localhost/bbs/user_infomation.php');	  
}

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

try{
  //未サインインは弾く
  if(empty($_SESSION["auth"])){
    $errors = "不正な画面遷移です";
    throw new Exception($errors);
  }

  //バリデーション
  if(filter_input(INPUT_POST,"user_name")){
  
    if(mb_strlen($_POST["user_name"]) < 3){
    $errors = "ユーザー名は3文字以上にしてください";
    throw new Exception($errors);

    }elseif(mb_strlen($_POST["user_name"]) > 12){
    $errors = "ユーザー名は12文字以下にしてください";
    throw new Exception($errors);

    }

  }

  //バリデーション
  if(filter_input(INPUT_POST,"password") || filter_input(INPUT_POST,"password_2")){

    if(!filter_input(INPUT_POST,"password") || !filter_input(INPUT_POST,"password_2")){
    $errors = "パスワードは二か所入力してください";
    throw new Exception($errors);
    
    }elseif($_POST["password"] !== $_POST["password_2"]){
    $errors = "パスワードが一致しません";
    throw new Exception($errors);

    }elseif(mb_strlen($_POST["password"]) < 6){
    $errors = "パスワードは6文字以上にしてください";
    throw new Exception($errors);

    }elseif(mb_strlen($_POST["password"]) > 24){ 
    $errors = "パスワードは24文字以下にしてください";
    throw new Exception($errors);
    }

  }

}catch (Exception $errors){
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit($errors->getMessage());
}

try{
  //DB接続
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

  //ユーザー名の更新
  if(filter_input(INPUT_POST,"user_name")){
    $update_user_name_sql = 'UPDATE users SET user_name = ? WHERE id = ?';
    $stmt = $pdo -> prepare($update_user_name_sql);
    $stmt -> bindvalue(1,$_POST["user_name"],PDO::PARAM_STR);
    $stmt -> bindvalue(2,$_SESSION["id"],PDO::PARAM_STR);
    $stmt -> execute();

    $user_name_sql = 'SELECT user_name FROM users WHERE id = ?';
    $stmt = $pdo -> prepare($user_name_sql);
    $stmt -> bindvalue(1,$_SESSION["id"],PDO::PARAM_STR);
    $stmt -> execute();

    if($row = $stmt->fetch()){
      $success["user_name"] = "ユーザー名を更新しました";
    }else{
      $errors = "ユーザー名を変更できませんでした";
      throw new Exception($errors);
    }

    $_SESSION["user_name"] = $row["user_name"];
  }

  //パスワードの更新
  if(filter_input(INPUT_POST,"password")){

    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $update_hash_sql = 'UPDATE users SET hash = ? WHERE id = ?';
    $stmt = $pdo -> prepare($update_hash_sql);
    $stmt -> bindvalue(1,$hash,PDO::PARAM_STR);
    $stmt -> bindvalue(2,$_SESSION["id"],PDO::PARAM_STR);
    $stmt -> execute();

    $hash_sql = 'SELECT hash FROM users WHERE id = ?';
    $stmt = $pdo -> prepare($hash_sql);
    $stmt -> bindvalue(1,$_SESSION["id"],PDO::PARAM_STR);
    $stmt -> execute();

    if($row = $stmt->fetch()){
      $success["pass"] = "パスワードを変更しました。";
    }else{
      $errors = "パスワードを変更できませんでした";
      throw new Exception($errors);
    }

  }

  //アイコンのアップロード
  if(isset($_FILES["icon"])){

    $file = $_FILES["icon"];
    $ext = substr($file["name"],-4);

    if($ext === ".jpg" || $ext === ".gif" || $ext == ".png"){

      $file_path = (STRING)"./img/post/".$_SESSION["id"].$ext;
      move_uploaded_file($file["tmp_name"],$file_path);
      $success["file"] = "ファイルのアップロードに成功しました。";

    }else{
      $errors = "画像のみアップロード可能です";
      throw new Exception($errors);
    }

    $stmt = $pdo-> prepare(
    'INSERT INTO images (id,icon_path,post_time)
    VALUES (?,?,NOW());'
    );
    $stmt -> bindValue(1, $_SESSION["id"], PDO::PARAM_STR);
    $stmt -> bindValue(2, $file_path, PDO::PARAM_STR);
    $stmt -> execute();

  }
  //アイコンの表示
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
<html　lang="ja" dir="ltr">
  <head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
  crossorigin="anonymous">
  <title>ユーザー情報変更</title>
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
          <a class="nav-link" href="user.php">Users</a>
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
      <?php
      if(isset($success)):?>
        <?php foreach($success as $values):
        echo '<div class="alert alert-primary" style="margin:1rem;" role="alert">'.$values.'</div>';?>
        <?php endforeach; ?>
      <?php endif;?>
      <div class="shadow" style="width:60rem; margin-top: 1rem; margin-bottom: 1rem">
        <div class="bg-dark rounded-top text-light text-center" style="padding:0.5rem;">
          <h2>ユーザー情報変更</h2>
        </div>
        <div style="margin:1rem; padding:1rem;">
          <img class="offset-lg-5 rounded-circle shadow" 
          style="width:150px; height:150px; margin-bottom:2rem;" src="<?= $icon_path; ?>" alt="アイコン">
          <!-- アイコンアップローダ -->
          <form class="form" action="user_infomation.php" method="post" enctype="multipart/form-data">
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-4">
                <input class="form-control-file" type="file" name="icon">
                <input class="btn btn-primary btn-sm" style="margin:1rem 0;" type="submit" value="アップロード">
              </div>
            </div> 
          </form>
          <!-- ユーザー情報変更フォーム -->   
          <form action="user_infomation.php" method="post">
            <div class="form-group row">
              <span class="col-lg-4 text-lg-right">現在のユーザー名</span>
              <span class="col-lg-6"><?= h($_SESSION["user_name"]) ?></span>
            </div>
            <div class="form-group row">
              <label class="col-lg-4 col-form-label text-lg-right" for="user_name">新しいユーザー名</label>
              <div class="col-lg-6">
                <input class="form-control" type="text" name="user_name" placeholder="新しいユーザー名を入力(3～12文字で入力)">
              </div>  
            </div>
            <div class="form-group row">  
              <label class="col-lg-4 col-form-label text-lg-right" for="password">新しいパスワード</label>
              <div class="col-lg-6">
                <input class="form-control" type="password" name="password" placeholder="新しいパスワードを入力(6文字以上)">
              </div>
            </div>     
            <div class="form-group row">  
              <label class="col-lg-4 col-form-label text-lg-right" for="password_2">新しいパスワード(確認用)</label>
              <div class="col-lg-6">
                <input class="form-control" type="password" name="password_2" placeholder="新しいパスワードを入力(6文字以上)">
              </div>  
            </div>    
            <div class="form-group row">
              <div class="col-lg-6 offset-lg-4">  
                <input class="btn btn-primary btn-sm" type="submit" value="変更する">
              </div>
            </div>    
          </form>                    
        </div>
      </div>  
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