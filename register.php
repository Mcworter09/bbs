<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" 
    crossorigin="anonymous">
    <link rel="icon" href="img/favicon.ico">
    <title>ユーザー登録</title>
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
      <div class="row justify-content-md-center mt-5">
        <div class="col-md-8 col-md-offset-2">
          <div class="card"> 
            <div class="card-header bg-dark text-light">
              <h1>Hello World!</h1>
              <p>ユーザーを登録して利用を開始しましょう。</p>
            </div>
            <div class="card-body">
              <form class="form" action="register_session.php" method="post">
                <div class="form-group row">
                  <label class="col-lg-4 col-form-label text-lg-right" for="user_name">ユーザー名</label>
                  <div class="col-lg-6">
                    <input class="form-control" type="text" name="user_name" placeholder="ユーザー名を入力(3～12文字で入力)">
                  </div>  
                </div>  
                <div class="form-group row">  
                  <label class="col-lg-4 col-form-label text-lg-right" for="user_id">ユーザーID</label>
                  <div class="col-lg-6">
                    <input class="form-control" type="text" name="user_id" placeholder="ユーザーIDを入力(6～12文字で入力)">
                  </div>
                </div>
                <div class="form-group row">  
                  <label class="col-lg-4 col-form-label text-lg-right" for="password">パスワード</label>
                  <div class="col-lg-6">
                    <input class="form-control" type="password" name="password" placeholder="パスワードを入力(6文字以上)">
                  </div>  
                </div>
                <div class="form-group row">
                  <label class="col-lg-4 col-form-label text-lg-right" for="sex">性別</label>
                  <div class="col-lg-6 col-form-label">
                    <label for="sex">男性</label> 
                    <input type="radio" name="sex" value="male">                
                    <label for="sex">女性</label>
                    <input type="radio" name="sex" value="famale"> 
                    <label for="sex">その他</label>
                    <input type="radio" name="sex" value="x">
                  </div>
                </div>    
                <div class="form-group row">
                  <div class="col-lg-6 offset-lg-4">  
                    <input class="btn btn-primary" type="submit" value="ユーザーを登録">
                  </div>
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
