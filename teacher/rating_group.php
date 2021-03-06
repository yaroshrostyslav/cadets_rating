<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '1'){
	header("Location: ../index.php");
}

/******************** Actions ********************/
$mysqli = connect_db($host, $user, $pass, $db_name);
// Get Info of Account
$user_id = $_SESSION['user_id'];
$get_user = $mysqli->query("SELECT `users`.`login`, `teachers`.`full_name`, `teachers`.`id` FROM `users` INNER JOIN `teachers` ON `teachers`.`id_user` = `users`.`id` WHERE id_user ='$user_id' ");
$user = $get_user->fetch_array();
// Get All Lessons of Teacher (group)
$id_teacher = $user['id'];
$get_groups = $mysqli->query("SELECT `groups`.`id` AS `id_group`, `groups`.`name` AS `name_group` FROM `lessons` INNER JOIN `teachers` ON `lessons`.`id_teacher` = `teachers`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` WHERE `teachers`.`id` = '$id_teacher' GROUP BY `groups`.`id` ");
foreach ($get_groups as $key => $value) {
  $groups[$key]['id_group'] = $value['id_group'];
  $groups[$key]['name_group'] = $value['name_group'];
}
 
// Get Rating
$get_cadets = [];
if (isset($_POST['get_rating'])){
  $id_group = $_POST['group'];
  // $get_cadets = $mysqli->query("SELECT `cadets`.`id`, `cadets`.`full_name` FROM `groups` INNER JOIN `cadets` ON `groups`.`id` = `cadets`.`id_group` WHERE `groups`.`id` = '$id_group' ");
  $get_cadets = $mysqli->query("SELECT `cadets`.`id`, `cadets`.`full_name`, `grade_lesson`.`grade` FROM `grade_lesson` INNER JOIN `cadets` ON `grade_lesson`.`id_cadet` = `cadets`.`id` INNER JOIN `groups` ON `cadets`.`id_group` = `groups`.`id` WHERE `groups`.`id` = '$id_group' GROUP BY `cadets`.`id` ORDER BY `grade_lesson`.`grade` ASC  ");
  $get_groups = $mysqli->query("SELECT `name` FROM `groups` WHERE id ='$id_group' ");
  $get_group = $get_groups->fetch_array();
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
          	<li><a href="index.php"><i class="fa fa-calendar"></i><span>Розклад</span></a></li>
            <li><a href="grade.php"><i class="fa fa-check-square-o"></i><span>Виставити оцінку</span></a></li>
            <li><a href="rating_training.php"><i class="fa fa-bar-chart"></i><span>Навчальний рейтинг</span></a></li>
            <li><a href="overall_rating.php"><i class="fa fa-bar-chart"></i><span>Загальний рейтинг</span></a></li>
            <li class="active"><a href="rating_group.php"><i class="fa fa-bar-chart"></i><span>Груповий рейтинг</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-bar-chart"></i> Груповий рейтинг</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Груповий рейтинг</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Переглянути рейтинг курсанта</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post" id="">
                  <div class="form-group">
                     <label class="control-label col-md-3" for="group">Група</label>
                     <div class="col-md-8">
                        <select class="form-control" id="group" name="group" required="">
                          <?php $i=0; foreach ($groups as $key => $group):?>
                           <option value="<?=$group['id_group']?>"><?=$group['name_group']?></option>
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

          <?php

            $group_rate = 0;

          ?>

          <div class="col-md-12" <?php if (isset($get_group['name']) == false){echo 'style="display: none;"';} ?> >
            <div class="card">
              <h3 class="card-title">Група - <?=$get_group['name']?></h3>
              <div class="table-responsive">
                 <table class="table">
                    <thead>
                       <tr>
                          <th>ПІБ</th>
                          <th>Заг. рейт.</th>
                       </tr>
                    </thead>
                    <tbody>
                      <?php $z=0; foreach ($get_cadets as $key => $cadet):?>
                        <tr>
                          <td><?=$cadet['full_name']?></td>
                          <?php
                            // Get Rating of Cadet
                            $id_cadet = $cadet['id'];
                            // Get Rating
                            $total_rate = 0;
                            $arr_sum = [];
                            $w=0;
                            $i=0;
                            // Get Rating of Lessons
                            $get_rating_lessons = $mysqli->query("SELECT `subjects`.`name`, SUM(`grade`) FROM `grade_lesson` INNER JOIN `lessons` ON `grade_lesson`.`id_lesson` = `lessons`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `lessons`.`id_group` = '$id_group' AND `id_cadet` = '$id_cadet' GROUP BY `subjects`.`name` ");
                            foreach ($get_rating_lessons as $key => $value) {
                              $arr_sum['rat_les'] += $value['SUM(`grade`)'];
                              
                              $w++;
                            }
                            $calc = @calcRate(0, $arr_sum['rat_les']/$w);
                            $total_rate += $calc;
                            // Get Rating of Activity
                            $get_rating_activity = $mysqli->query("SELECT `activity`.`id` AS `activity_id`, `activity`.`name` AS `activity_name`, `type_activity`.`id` AS `id_type_activity`, `grade` FROM `grade_activity` INNER JOIN `type_activity` ON `grade_activity`.`id_type_activity` = `type_activity`.`id` INNER JOIN `activity` ON `type_activity`.`id_activity` = `activity`.`id` WHERE `id_cadet` = '$id_cadet' ");
                            foreach ($get_rating_activity as $key => $value) {
                              $id_type_activity = $value['id_type_activity'];
                              $get_type_activity = $mysqli->query("SELECT `type_activity`.`name`, `grade` FROM `grade_activity` INNER JOIN `type_activity` ON `grade_activity`.`id_type_activity` = `type_activity`.`id` INNER JOIN `activity` ON `type_activity`.`id_activity` = `activity`.`id` WHERE `id_cadet` = '$id_cadet' AND `type_activity`.`id` = '$id_type_activity' ");
                               $arr_sum['rat_act_'.$i] += $value['grade'];
                               $calc = calcRate($value['activity_id'], $arr_sum['rat_act_'.$i]);
                               $total_rate += $calc;
                               $i++;
                            }
                          ?>
                          <td><?=round($total_rate, 2)?></td>
                        </tr>
                      <?php $z++; $group_rate += $total_rate; endforeach; ?> 
                    </tbody>
                 </table>
              </div>
              <h4 class="card-title">Груповий рейтинг = <?php if ($z==0){$calc = 0;} else{$calc=$group_rate/$z;} echo round($calc, 2); ?></h4>
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