<?php
session_start();

//送信後にリダイレクトする
if($_SERVER['REQUEST_METHOD']==='POST'){
		header('Location:https://localhost/bbs/index.php');	
}
  
function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

try{
  //データベースに接続
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


$bbs = $pdo -> query('SELECT * FROM bbs');
 
try{

  if((string)filter_input(INPUT_POST,"content")){

    if(mb_strlen($_POST["content"]) > 40){
      $errors = "40文字以内で入力してください";
      throw new Exception($errors);
    }

  //投稿毎にユニークキーを作成
    function Rand_Str($length = 10) {
      $array = array_merge(range("a","z"),range("A","Z"),range("0","9")); 
      $str = NULL;
      for($i=1;$i <= $length;$i++){
        $str .=  $array[mt_rand(0,count($array))];
      }    
      return $str;  
    }

    $unique_key = Rand_Str();

    //データベースに投稿内容を送信  
    $sql = 'INSERT INTO bbs (id,user_name,post_time,content,unique_key)
    VALUES(:id,:user_name,NOW(),:content,:unique_key);';
    $stmt = $pdo-> prepare($sql);
    $stmt -> bindValue(":id",$_SESSION["id"]);
    $stmt -> bindValue(":user_name",$_SESSION["user_name"]);
    $stmt -> bindValue(":content",$_POST["content"]);
    $stmt -> bindValue(":unique_key",$unique_key);
    $stmt -> execute();
  }

}catch(Exception $errors){
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
    <title>CRUD掲示板</title>
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
          if(isset($_SESSION["user_name"])){
            echo '<a class="nav-link" href="user.php">Users</a>';
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
      <h1 class="text-center" style="margin-top:3rem;">CRUD掲示板</h1>
      <div class="shadow">  
        <table class="table table-striped">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>投稿者</th>
              <th>投稿日時</th>
              <th>投稿</th>
            </tr>
          </thead>  
          <?php
            for($i = 0;$i < 100;$i++):
              $row = $bbs->fetch();
              if($row == ""){
                break;
              };
            ?>
            <tr>
              <td><?= $row["id"]?></td>
              <td><?= $row["user_name"]?></td>
              <td><?= $row["post_time"]?></td>
              <td><?= h($row["content"])?></td>
            </tr>  
            <?php endfor; ?>   
        </table>
      </div>  
      <?php
      if(isset($_SESSION["user_name"])):?>
      <div class="bg-dark shadow rounded" style="width:25rem;height:10rem; margin:1rem auto; padding:1rem;">
        <p class="text-light">投稿フォーム</p>
        <form class="form" action="index.php" method="post" class="text-center">
          <div class="form-group" style="margin-top:0;">
            <input class="form-control" type="text" name="content" placeholder="40字以内で投稿を入力">
            <input class="btn btn-primary form-control mt-3" type="submit" value="送信">
          </div>
        </form>
      </div>  
      <?php endif; ?>
      <?php
        if(!isset($_SESSION["auth"])):?>
        <div class="row justify-content-md-center">
          <div class="card text-center mt-2 mb-2 shadow" style="width: 30rem;">
            <div class="card-body">
              <h4 class="card-title"><?="サインインされておりません。"?></h4>
              <p class="card-text"><?="サインインをすれば書き込みができるようになります。"?><p>
              <a href="signin.php" class="btn btn-outline-primary mr-4" role="button"><?="サインインはこちら"?></a>
              <a href="register.php" class="btn btn-outline-primary ml-4" role="button"><?="会員登録はこちら"?></a>
            <?php endif; ?>
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
