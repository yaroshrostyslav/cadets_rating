<?php
require_once('config.php');
require_once('functions.php');
if (check_auth() == true){
  switch ($_SESSION['user_role']) {
      case '0':
          header("Location: admin/");
          break;
      case '1':
          header("Location: teacher/");
          break;
      case '2':
          header("Location: specialist/");
          break;
      case '3':
          header("Location: cadet/");
          break;
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?=$site_name?></title>
  </head>
  <body>
    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content">
      <div class="logo">
        <h1><?=$sub_site_name?></h1>
      </div>

      <div class="login-box">
        <form class="login-form" id="login_form">
          <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>Авторизація <?=$_SESSION['user_role']?></h3>
          <div class="alert alert-dismissible alert-danger" style="display: none;">
            Логін або пароль введено невірно!
          </div>
          <div class="form-group">
            <label class="control-label">Логін</label>
            <input class="form-control" type="text" placeholder="login" id="login" autofocus>
          </div>
          <div class="form-group">
            <label class="control-label">Пароль</label>
            <input class="form-control" type="password" id="password" placeholder="Password">
          </div>
          <div class="form-group btn-container">
            <button class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i>Увійти</button>
          </div>
        </form>
      </div>
    </section>
  </body>
  <script src="assets/js/jquery-2.1.4.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/plugins/pace.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/action/login.js"></script>
</html>