<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '0'){
	header("Location: ../index.php");
}

/******************** Actions ********************/
$mysqli = connect_db($host, $user, $pass, $db_name);
// Get All Teachers
$get_teachers = $mysqli->query("SELECT * FROM teachers ORDER BY id DESC");
// Get All Lessons
$get_lessons = $mysqli->query("SELECT `teachers`.`full_name`, `subjects`.`name`, `date` FROM `lessons` INNER JOIN `teachers` ON `lessons`.`id_teacher` = `teachers`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` ORDER BY `teachers`.`id`, `groups`.`id` ");
// Get All Groups
$get_groups = $mysqli->query("SELECT * FROM groups ORDER BY id DESC");
foreach ($get_groups as $key => $value) {
  $groups[$key]['id'] = $value['id'];
  $groups[$key]['name'] = $value['name'];
}
// New Lesson
if (isset($_POST['new_lesson'])){
  $id_teacher = $_POST['teacher'];
  $id_subject = $_POST['subject'];
  $id_group = $_POST['group'];
  $date = strtotime($_POST['date']);
  $mysqli->query("INSERT INTO lessons (`id_teacher`, `id_group`, `id_subject`, `date`) VALUES ('$id_teacher', '$id_group', '$id_subject', '$date')");
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
    <title><?=$site_name?> - Заняття</title>
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
            <li class="active"><a href="lessons.php"><i class="fa fa-calendar"></i><span>Заняття</span></a></li>
            <li><a href="subjects.php"><i class="fa fa-book"></i><span>Предмети</span></a></li>
            <li><a href="activity.php"><i class="fa fa-arrows"></i><span>Діяльності</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-calendar"></i> Заняття</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Заняття</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Додати заняття</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post">
                  <div class="form-group">
                     <label class="control-label col-md-3" for="select">Викладач</label>
                     <div class="col-md-8">
                        <select class="form-control" id="teacher" name="teacher" required="">
                           <?php $i=0; foreach ($get_teachers as $key => $teacher):?>
                            <option value="<?=$teacher['id']?>"><?=$teacher['id']?> - <?=$teacher['full_name']?></option>
                           <?php endforeach; ?> 
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-md-3" for="select">Предмет</label>
                     <div class="col-md-8">
                        <select class="form-control" id="subject" name="subject" required="">
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-md-3" for="select">Група</label>
                     <div class="col-md-8">
                        <select class="form-control" id="" name="group" required="">
                           <?php $i=0; foreach ($groups as $key => $group):?>
                            <option value="<?=$group['id']?>"><?=$group['id']?> - <?=$group['name']?></option>
                           <?php endforeach; ?> 
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">Дата</label>
                    <div class="col-md-8">
                      <input class="form-control col-md-8" type="text" name="date" id="date" placeholder="Enter date" required="">
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="new_lesson"><i class="fa fa-fw fa-lg fa-check-circle"></i>Додати</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-12">
             <div class="card">
                <h3 class="card-title">Усі заняття</h3>

                
                <div class="table-responsive">
                   <table class="table">
                      <thead>
                         <tr>
                            <th>ID</th>
                            <th>Викладач</th>
                            <th>Предмет</th>
                            <th>Дата</th>
                         </tr>
                      </thead>
                      <tbody>
                        <?php $i=1; foreach ($get_lessons as $key => $lesson):?>
                          <tr>
                            <td><?=$i?></td>
                            <td><?=$lesson['full_name']?></td>
                            <td><?=$lesson['name']?></td>
                            <td><?=date("Y-m-d H:i", $lesson['date'])?></td>
                          </tr>
                        <?php $i++; endforeach; ?> 
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
    <script src="../assets/js/imask.js"></script>
    <script type="text/javascript">
      var dateMask = {
       mask: '0000-00-00 00:00',
       lazy: false
      };
      var mask = IMask(document.getElementById('date'), dateMask);

      var id_teacher = $('#teacher').val();
      get_subjects(id_teacher);
      $('#teacher').on('change', function(){
        id_teacher = $(this).val();
        $('#subject').find('option').remove();
        get_subjects(id_teacher);
      });

      function get_subjects(id_teacher){
        $.ajax({
            url: '../handler.php',
            type: "POST",
            data: ({
                action: 'get_subjects',
                id_teacher: id_teacher,
            }),
            dataType: "html",
            success: function(data){
                data = JSON.parse(data);
                // console.log(data)
                $.each(data['result'], function(index, value){
                  $('#subject').append('<option value="'+value['id']+'">'+value['name']+'</option>');
                });
            }
        });
      }

    </script>
  </body>
</html>