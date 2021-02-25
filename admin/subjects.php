<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '0'){
	header("Location: ../index.php");
}

/******************** Actions ********************/
$mysqli = connect_db($host, $user, $pass, $db_name);
// Get All Subjects
$get_subjects = $mysqli->query("SELECT * FROM subjects ORDER BY id DESC");
// New Subject
if (isset($_POST['new_subject'])){
  $name = $_POST['subject_name'];
  $mysqli->query("INSERT INTO subjects (name) VALUES ('$name')");
  header("Location: ".$_SERVER['HTTP_REFERER']);
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?=$site_name?> - Предмети</title>
  </head>
  <body class="sidebar-mini fixed">
    <div class="wrapper">
      <header class="main-header hidden-print"><a class="logo" href="index.html"><?=$site_name?></a>
        <nav class="navbar navbar-static-top">
          <a class="sidebar-toggle" href="#" data-toggle="offcanvas"></a>
          <div class="navbar-custom-menu">
            <ul class="top-nav">
              <li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user fa-lg"></i></a>
                <ul class="dropdown-menu settings-menu">
                  <li><a href="../logout.php"><i class="fa fa-sign-out fa-lg"></i> Вийти</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <aside class="main-sidebar hidden-print">
        <section class="sidebar">
          <div class="user-panel">
            <div class="pull-left image"><img class="img-circle" src="../assets/images/user.png" alt="User Image"></div>
            <div class="pull-left info">
              <p>Адмін</p>
              <p class="designation"></p>
            </div>
          </div>
          <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa fa-dashboard"></i><span>Управління</span></a></li>
            <li><a href="teachers.php"><i class="fa fa-users"></i><span>Викладачі</span></a></li>
            <li><a href="specialists.php"><i class="fa fa-users"></i><span>Спеціалісти</span></a></li>
            <li><a href="cadets.php"><i class="fa fa-graduation-cap"></i><span>Курсанти</span></a></li>
            <li><a href="lessons.php"><i class="fa fa-calendar"></i><span>Заняття</span></a></li>
            <li class="active"><a href="subjects.php"><i class="fa fa-book"></i><span>Предмети</span></a></li>
            <li><a href="activity.php"><i class="fa fa-arrows"></i><span>Діяльності</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-book"></i> Предмети</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Предмети</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Новий предмети</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post">
                  <div class="form-group">
                    <label class="control-label col-md-3">Назва</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="subject_name" placeholder="Enter subject" required="">
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="new_subject"><i class="fa fa-fw fa-lg fa-check-circle"></i>Додати</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>

            </div>
          </div>

          <div class="col-md-12">
             <div class="card">
                <h3 class="card-title">Усі предмети</h3>
                <div class="table-responsive">
                   <table class="table">
                      <thead>
                         <tr>
                            <th>ID</th>
                            <th>Назва</th>
                         </tr>
                      </thead>
                      <tbody>
                        <?php $i=0; if ($get_subjects->num_rows > 0){ while ($subject = $get_subjects->fetch_array()):?>
                         <tr>
                            <td><?=$subject['id']?></td>
                            <td><?=$subject['name']?></td>
                         </tr>
                        <?php endwhile; } ?> 
                      </tbody>
                   </table>
                </div>
             </div>
          </div>


        </div>
      </div>
    </div>

    <script src="../assets/js/jquery-2.1.4.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/pace.min.js"></script>
    <script src="../assets/js/main.js"></script>
  </body>
</html>