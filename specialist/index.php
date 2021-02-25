<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '2'){
	header("Location: ../index.php");
}

/******************** Actions ********************/
$mysqli = connect_db($host, $user, $pass, $db_name);
// Get Info of Account
$user_id = $_SESSION['user_id'];
$get_user = $mysqli->query("SELECT `users`.`login`, `specialists`.`full_name`, `specialists`.`id` FROM `users` INNER JOIN `specialists` ON `specialists`.`id_user` = `users`.`id` WHERE id_user ='$user_id' ");
$user = $get_user->fetch_array();
// Get All Grades
$id_specialist = $user['id'];
$get_grades = $mysqli->query("SELECT `cadets`.`full_name`, `groups`.`name` AS `group_name`, `activity`.`name` AS `activity_name`, `type_activity`.`name` AS `type_activity_name`, `grade` FROM `grade_activity` INNER JOIN `cadets` ON `grade_activity`.`id_cadet` = `cadets`.`id` INNER JOIN `groups` ON `cadets`.`id_group` = `groups`.`id` INNER JOIN `type_activity` ON `grade_activity`.`id_type_activity` = `type_activity`.`id` INNER JOIN `activity` ON `type_activity`.`id_activity` = `activity`.`id` INNER JOIN `specialist_activity` ON `activity`.`id` = `specialist_activity`.`id_activity` INNER JOIN `specialists` ON `specialist_activity`.`id_specialist` = `specialists`.`id` WHERE `specialists`.`id` = '$id_specialist' ORDER BY `grade_activity`.`id` DESC  ");
foreach ($get_grades as $key => $value) {
  $grades[$key]['full_name'] = $value['full_name'];
  $grades[$key]['group_name'] = $value['group_name'];
  $grades[$key]['activity_name'] = $value['activity_name'];
  $grades[$key]['type_activity_name'] = $value['type_activity_name'];
  $grades[$key]['grade'] = $value['grade'];
}
// Get All Groups
$get_groups = $mysqli->query("SELECT * FROM groups ORDER BY id DESC");
foreach ($get_groups as $key => $value) {
  $groups[$key]['id'] = $value['id'];
  $groups[$key]['name'] = $value['name'];
}
// Get Activity of Specialist
$get_activity = $mysqli->query("SELECT `activity`.`id`, `activity`.`name` FROM `specialist_activity` INNER JOIN `specialists` ON `specialist_activity`.`id_specialist` = `specialists`.`id` INNER JOIN `activity` ON `specialist_activity`.`id_activity` = `activity`.`id` WHERE `id_user` = '$user_id' GROUP BY `specialist_activity`.`id_activity`");
foreach ($get_activity as $key => $value) {
  $activities[$key]['id'] = $value['id'];
  $activities[$key]['name'] = $value['name'];
}

// Add Grade
if (isset($_POST['add_grade'])){
  $id_cadet = $_POST['cadet'];
  $id_type_activity = $_POST['type_activity'];
  $grade = $_POST['grade'];

  $check_grade = $mysqli->query("SELECT * FROM `grade_activity` WHERE `id_cadet` = '$id_cadet' AND `id_type_activity` = '$id_type_activity' ");
  if ($check_grade->num_rows > 0){
    $get_grade = $check_grade->fetch_array();
    $id_grade = $get_grade['id'];
    $mysqli->query("UPDATE `grade_activity` SET `grade` = '$grade' WHERE `id` = '$id_grade' ");
  }else{
    $mysqli->query("INSERT INTO grade_activity (id_cadet, id_type_activity, grade) VALUES ('$id_cadet', '$id_type_activity', '$grade')");
  }
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
            <li class="active"><a href="index.php"><i class="fa fa-check-square-o"></i><span>Виставити оцінку</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-check-square-o"></i> Виставити оцінку</h1>
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
              <h3 class="card-title">Виставити оцінку</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post" id="">
                  <div class="form-group">
                     <label class="control-label col-md-3" for="activity">Діяльність</label>
                     <div class="col-md-8">
                        <select class="form-control" id="activity" name="activity" required="">
                          <?php $i=0; foreach ($activities as $key => $activity):?>
                           <option value="<?=$activity['id']?>"><?=$activity['id']?> - <?=$activity['name']?></option>
                          <?php endforeach; ?> 
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-md-3" for="type_activity">Вид діяльності</label>
                     <div class="col-md-8">
                        <select class="form-control" id="type_activity" name="type_activity" required="">
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-md-3" for="group">Група</label>
                     <div class="col-md-8">
                        <select class="form-control" id="group" name="group" required="">
                          <?php $i=0; foreach ($groups as $key => $group):?>
                           <option value="<?=$group['id']?>"><?=$group['id']?> - <?=$group['name']?></option>
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
                  <div class="form-group">
                    <label class="control-label col-md-3">Оцінка</label>
                    <div class="col-md-8">
                      <input class="form-control" type="tel" inputmode="numeric" name="grade" maxlength="3" placeholder="Enter grade" required="">
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="add_grade"><i class="fa fa-fw fa-lg fa-check-circle"></i>Додати</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-12">
             <div class="card">
                <h3 class="card-title">Усі оцінки</h3>

                
                <div class="table-responsive">
                   <table class="table">
                      <thead>
                         <tr>
                            <th>ПІБ</th>
                            <th>Група</th>
                            <th>Діяльність</th>
                            <th>Вид діяльності</th>
                            <th>Оцінка</th>
                         </tr>
                      </thead>
                      <tbody>
                        <?php $i=0; foreach ($grades as $key => $grade):?>
                         <tr>
                            <td><?=$grade['full_name']?></td>
                            <td><?=$grade['group_name']?></td>
                            <td><?=$grade['activity_name']?></td>
                            <td><?=$grade['type_activity_name']?></td>
                            <td><?=$grade['grade']?></td>
                         </tr>
                         <?php endforeach; ?> 
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
      // Get Type of Activity
      var id_activity = $('#activity').val();
      get_type_activity(id_activity);
      $('#activity').on('change', function(){
        id_activity = $(this).val();
        $('#type_activity').find('option').remove();
        get_type_activity(id_activity);
      });

      function get_type_activity(id_activity){
        $.ajax({
            url: '../handler.php',
            type: "POST",
            data: ({
                action: 'get_type_activity',
                id_user: '<?=$user_id?>',
                id_activity: id_activity,
            }),
            dataType: "html",
            success: function(data){
                data = JSON.parse(data);
                // console.log(data)
                $.each(data['result'], function(index, value){
                  $('#type_activity').append('<option value="'+value['id']+'">'+value['name']+'</option>');
                });
            }
        });
      }

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