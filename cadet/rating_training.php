<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '3'){
  header("Location: ../index.php");
}

/******************** Actions ********************/
$mysqli = connect_db($host, $user, $pass, $db_name);
// Get Info of Account
$user_id = $_SESSION['user_id'];
$get_user = $mysqli->query("SELECT `users`.`login`, `cadets`.`full_name`, `cadets`.`id`, `cadets`.`id_group` AS `id_group` FROM `users` INNER JOIN `cadets` ON `cadets`.`id_user` = `users`.`id` WHERE id_user ='$user_id' ");
$user = $get_user->fetch_array();
$id_cadet = $user['id'];
$id_group = $user['id_group'];
// Get Subjects
$get_subjects = $mysqli->query("SELECT `subjects`.`id`, `subjects`.`name` FROM `grade_lesson` INNER JOIN `lessons` ON `grade_lesson`.`id_lesson` = `lessons`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `lessons`.`id_group` = '$id_group' AND `id_cadet` = '$id_cadet' GROUP BY `subjects`.`name` ");
 
// Get Rating
if (isset($_POST['get_rating'])){
  $id_subject = $_POST['subject'];
  $get_rating = $mysqli->query("SELECT `date`, `grade`, `subjects`.`name` FROM `grade_lesson` INNER JOIN `lessons` ON `grade_lesson`.`id_lesson` = `lessons`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `lessons`.`id_group` = '$id_group' AND `subjects`.`id` = '$id_subject' AND `id_cadet` = '$id_cadet' ");
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
    <title><?=$site_name?> - Спеціаліст</title>
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
              <p><?=$user['login']?></p>
              <p class="designation"><?=$user['full_name']?></p>
            </div>
          </div>
          <ul class="sidebar-menu">
            <li class="active"><a href="rating_training.php"><i class="fa fa-bar-chart"></i><span>Навчальний рейтинг</span></a></li>
            <li><a href="overall_rating.php"><i class="fa fa-bar-chart"></i><span>Загальний рейтинг</span></a></li>
            <li><a href="rating_group.php"><i class="fa fa-bar-chart"></i><span>Груповий рейтинг</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-bar-chart"></i> Навчальний рейтинг</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Навчальний рейтинг</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Переглянути особистий рейтинг</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post" id="">
                  <div class="form-group">
                     <label class="control-label col-md-3" for="subject">Предмет</label>
                     <div class="col-md-8">
                        <select class="form-control" id="subject" name="subject" required="">
                          <?php foreach ($get_subjects as $key => $subject):?>
                            <option value="<?=$subject['id']?>"><?=$subject['name']?></option>
                          <?php endforeach; ?> 
                        </select>
                     </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="get_rating"><i class="fa fa-fw fa-lg fa-check-circle"></i>Переглянути</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-4" <?php if (isset($_POST['get_rating']) == false){echo 'style="display: none;"';} ?>>
            <div class="card">
              <h3 class="card-title"><?=$get_rating->fetch_array()['name']?></h3>
              <div class="table-responsive">
                 <table class="table">
                    <thead>
                       <tr>
                          <th>Дата</th>
                          <th>Оцінка</th>
                       </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; $grade=0; if ($get_rating->num_rows > 0){ while ($rating = $get_rating->fetch_array()):?>
                       <tr>
                        <td><?=date("Y-m-d H:i", $rating['date'])?></td>
                        <td><?=$rating['grade']?></td>
                       </tr>
                       <?php $i++; $grade += $rating['grade']; endwhile; } ?> 
                    </tbody>
                    <tbody>
                       <tr>
                        <td><b>Sum</b></td>
                        <td><?=$grade?></td>
                       </tr>
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