<?php
session_start();

if(isset($_SESSION["auth"])){
  echo "サインイン済みです。";
  header('Content-Type: text/plain; charset=utf8', true, 500);
  exit;
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
    <title>サインイン</title>
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
          if($_SERVER["REQUEST_URI"] != "index.php"){
            echo '<a class="nav-link" href="index.php">BBS</a>';
          }?>
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
      <div class="row justify-content-center" style="margin-top:5em;">
        <div style="width:40rem;">
          <div class="card">
            <div class="card-header bg-dark text-white">Sign in</div>    
              <div class="card-body bg-white text-dark">
                <form class="form" action="signin_session.php" method="post">
                  <div class="form-group row">
                    <label class="col-lg-4 col-form-label text-lg-right" for="id">ユーザーID</label>
                    <div class="col-lg-6">
                      <input class="form-control" type="text" name="id" id="id" placeholder="ユーザーIDを入力">
                    </div>
                  </div>    
                  <div class="form-group row">
                    <label class="col-lg-4 col-form-label text-lg-right" class="padding:5 0rem" for="pass">パスワード</label>
                    <div class="col-lg-6">
                      <input class="form-control" type="password" name="password" id="pass" placeholder="パスワードを入力">
                    </div>
                  </div>
                  <div class="row col-lg-6 offset-lg-4">
                      <input class="form-group btn btn-primary" style="margin-right:1rem" type="submit" value="サインイン">
                      <a href="register.php" class="form-group btn btn-outline-primary" role="button"><?="新規登録"?></a>
                  </div> 
                </form>
              </div>
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
