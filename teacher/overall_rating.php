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
if (isset($_POST['get_rating'])){
  $id_teacher = $user['id'];
  $id_cadet = $_POST['cadet'];
  $id_group = $_POST['group'];
  $get_rating_lessons = $mysqli->query("SELECT `subjects`.`name`, SUM(`grade`) FROM `grade_lesson` INNER JOIN `lessons` ON `grade_lesson`.`id_lesson` = `lessons`.`id` INNER JOIN `teachers` ON `lessons`.`id_teacher` = `teachers`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `lessons`.`id_group` = '$id_group' AND `id_cadet` = '$id_cadet' GROUP BY `subjects`.`name` ");
  $get_cadet = $mysqli->query("SELECT `id`, `full_name` FROM `cadets` WHERE id ='$id_cadet'  ");
  $cadet = $get_cadet->fetch_array();
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
            <li class="active"><a href="overall_rating.php"><i class="fa fa-bar-chart"></i><span>Загальний рейтинг</span></a></li>
            <li><a href="rating_group.php"><i class="fa fa-bar-chart"></i><span>Груповий рейтинг</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-bar-chart"></i> Загальний рейтинг</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Загальний рейтинг</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Переглянути загальний рейтинг курсанта</h3>
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
                  <div class="form-group">
                     <label class="control-label col-md-3" for="cadet">Курсант</label>
                     <div class="col-md-8">
                        <select class="form-control" id="cadet" name="cadet" required="">
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

          <div class="col-md-12" <?php if (isset($cadet['full_name']) == false){echo 'style="display: none;"';} ?> >
            <div class="card">
              <h3 class="card-title"><?=$cadet['full_name']?></h3>
              <div class="table-responsive ">

                <?php

                  $arr_sum = [];
                  $total_rate = 0;

                ?>

                <div class="col-md-3">
                 <table class="table">
                    <thead>
                       <tr>
                          <th>Навчання</th>
                          <th>Оцінка</th>
                       </tr>
                    </thead>
                    <tbody>
                      <?php $w=0; if ($get_rating_lessons->num_rows > 0){ while ($rating_lessons = $get_rating_lessons->fetch_array()):?>
                       <tr>
                        <td><?=$rating_lessons['name']?></td>
                        <td><?=$rating_lessons['SUM(`grade`)']?></td>
                       </tr>
                       <?php $w++; $arr_sum['rat_les'] += $rating_lessons['SUM(`grade`)']; endwhile; } ?> 
                    </tbody>
                    <tbody <?php if($w==0){echo 'style="display: none;"';} ?>>
                       <tr>
                        <td><b>Sum</b></td>
                        <td><?=$arr_sum['rat_les']?></td>
                       </tr>
                       <tr>
                        <td><b>R = </b></td>
                        <td><?php echo $calc = @calcRate(0, $arr_sum['rat_les']/$w); $total_rate += $calc ?></td>
                       </tr>
                    </tbody>
                 </table>
                </div>

                <?php
                  // get activity
                  $id_cadet = $cadet['id'];
                  $get_activity = $mysqli->query("SELECT `activity`.`id` AS `activity_id`, `activity`.`name` AS `activity_name`, `type_activity`.`id` AS `id_type_activity`, `grade` FROM `grade_activity` INNER JOIN `type_activity` ON `grade_activity`.`id_type_activity` = `type_activity`.`id` INNER JOIN `activity` ON `type_activity`.`id_activity` = `activity`.`id` WHERE `id_cadet` = '$id_cadet' GROUP BY `activity`.`id` ");
                ?>

                <?php $i=0; foreach ($get_activity as $key => $value):?>
                  <?php
                    // get type activity
                    $activity_id = $value['activity_id'];
                    $get_type_activity = $mysqli->query("SELECT `type_activity`.`name`, `grade` FROM `grade_activity` INNER JOIN `type_activity` ON `grade_activity`.`id_type_activity` = `type_activity`.`id` INNER JOIN `activity` ON `type_activity`.`id_activity` = `activity`.`id` WHERE `id_cadet` = '$id_cadet' AND `activity`.`id` = '$activity_id' ");
                  ?>
                  <div class="col-md-3">
                   <table class="table">
                      <thead>
                         <tr>
                            <th><?=$value['activity_name']?></th>
                            <th>Оцінка</th>
                         </tr>
                      </thead>
                      <tbody>
                         
                          <?php $q=0; $grade_activity=0; foreach ($get_type_activity as $key => $type_activity):?>
                          <tr>
                            <td><?=$type_activity['name']?></td>
                            <td><?=$type_activity['grade']?></td>
                            </tr>
                          <?php $q++; $arr_sum['rat_act_'.$i] += $type_activity['grade']; endforeach; ?> 
                         
                      </tbody>
                      <tbody>
                         <tr>
                          <td><b>Sum</b></td>
                          <td><?=$arr_sum['rat_act_'.$i]?></td>
                         </tr>
                         <tr>
                          <td><b>R = </b></td>
                          <td><?php $calc = @calcRate($value['activity_id'], $arr_sum['rat_act_'.$i]); $total_rate += $calc; echo round($calc, 2); ?></td>
                         </tr>
                      </tbody>
                   </table>
                  </div>
                <?php $i++; endforeach; ?> 

              </div>
              <h4 class="card-title">Загальний рейтинг = <?=round($total_rate, 2);?></h4>
            </div>
          </div>

        </div>
      </div>
    </div>

    <script src="../assets/js/jquery-2.1.4.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/pace.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script type="text/javascript">
      // Get Cadet of Group
      var id_group = $('#group').val();
      get_cadet(id_group);
      $('#group').on('change', function(){
        id_group = $(this).val();
        $('#cadet').find('option').remove();
        get_cadet(id_group);
      });

      function get_cadet(id_group){
        $.ajax({
            url: '../handler.php',
            type: "POST",
            data: ({
                action: 'get_cadet',
                id_group: id_group,
            }),
            dataType: "html",
            success: function(data){
                data = JSON.parse(data);
                // console.log(data)
                $.each(data['result'], function(index, value){
                  $('#cadet').append('<option value="'+value['id']+'">'+value['full_name']+'</option>');
                });
            }
        });
      }

    </script>
  </body>
</html>