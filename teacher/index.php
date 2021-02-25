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
 
// Add Grade
if (isset($_POST['add_grade'])){
  // get lessons ID
  $id_group = $_POST['group'];
  $id_subject = $_POST['subject'];
  $date = $_POST['date'];
  $id_teacher = $user['id'];
  $get_lesson = $mysqli->query("SELECT * FROM `lessons` WHERE `id_teacher` = '$id_teacher' AND `id_group` = '$id_group' AND `id_subject` = '$id_subject' AND `date` = '$date' ");
  $get_lesson_id = $get_lesson->fetch_array();
  $id_lesson = $get_lesson_id['id'];
  // add grade
  $id_cadet = $_POST['cadet'];
  $grade = $_POST['grade'];
  $check_grade = $mysqli->query("SELECT * FROM `grade_lesson` WHERE `id_cadet` = '$id_cadet' AND `id_lesson` = '$id_lesson' ");
  if ($check_grade->num_rows > 0){
    $get_grade = $check_grade->fetch_array();
    $id_grade = $get_grade['id'];
    $mysqli->query("UPDATE `grade_lesson` SET `grade` = '$grade' WHERE `id` = '$id_grade' ");
  }else{
    $mysqli->query("INSERT INTO grade_lesson (id_cadet, id_lesson, grade) VALUES ('$id_cadet', '$id_lesson', '$grade')");
  }
  // header("Location: ".$_SERVER['HTTP_REFERER']);
}

if (isset($_POST['show_lessons'])){
	$id_teacher = $user['id'];
	$id_group = $_POST['group'];
	$id_subject = $_POST['subject'];
	$get_lessons = $mysqli->query("SELECT `subjects`.`name` AS `subject_name`, `groups`.`name` AS `group_name`, `date` FROM `lessons` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `id_teacher` = '$id_teacher' AND `id_group` = '$id_group' AND `id_subject` = '$id_subject'  ");
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
          	<li class="active"><a href="index.php"><i class="fa fa-calendar"></i><span>Розклад</span></a></li>
            <li><a href="grade.php"><i class="fa fa-check-square-o"></i><span>Виставити оцінку</span></a></li>
            <li><a href="rating_training.php"><i class="fa fa-bar-chart"></i><span>Навчальний рейтинг</span></a></li>
            <li><a href="overall_rating.php"><i class="fa fa-bar-chart"></i><span>Загальний рейтинг</span></a></li>
            <li><a href="rating_group.php"><i class="fa fa-bar-chart"></i><span>Груповий рейтинг</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-calendar"></i> Розклад</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Виставити оцінку</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Преглянути розклад</h3>
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
                     <label class="control-label col-md-3" for="subject">Предмет</label>
                     <div class="col-md-8">
                        <select class="form-control" id="subject" name="subject" required="">
                        </select>
                     </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="show_lessons"><i class="fa fa-fw fa-lg fa-check-circle"></i>Преглянути</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-12">
             <div class="card">
                <h3 class="card-title">Розклад</h3>

                

                <div class="table-responsive">
                   <table class="table">
                      <thead>
                         <tr>
                            <th>№</th>
                            <th>Група</th>
                            <th>Предмет</th>
                            <th>Дата</th>
                         </tr>
                      </thead>
                      <tbody>
                      	<?php $i=1; if ($get_lessons->num_rows > 0){ while ($lesson = $get_lessons->fetch_array()):?>
                         <tr>
                         	<td><?=$i?></td>
                         	<td><?=$lesson['group_name']?></td>
                         	<td><?=$lesson['subject_name']?></td>
                         	<td><?=date("Y-m-d H:i", $lesson['date'])?></td>
                         </tr>
                         <?php $i++; endwhile; } ?> 
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
    <script type="text/javascript">
      // Get Subject of Group
      var id_group = $('#group').val();
      get_subject(id_group);
      $('#group').on('change', function(){
        id_group = $(this).val();
        $('#subject').find('option').remove();
        get_subject(id_group);
      });

      function get_subject(id_group){
        $.ajax({
            url: '../handler.php',
            type: "POST",
            data: ({
                action: 'get_subject_of_group',
                id_teacher: '<?=$id_teacher?>',
                id_group: id_group,
            }),
            dataType: "html",
            success: function(data){
                data = JSON.parse(data);
                // console.log(data)
                $.each(data['result'], function(index, value){
                  $('#subject').append('<option value="'+value['id']+'">'+value['name']+'</option>');
                });
                get_date_of_lesson($('#subject').val());
            }
        });

      }

    </script>

  </body>
</html>