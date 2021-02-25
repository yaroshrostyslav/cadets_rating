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
foreach ($get_teachers as $key => $value) {
  $teachers[$key]['id'] = $value['id'];
  $teachers[$key]['full_name'] = $value['full_name'];
}
// Get All Subjects
$get_subjects = $mysqli->query("SELECT * FROM subjects ORDER BY id DESC");
// New Teacher
if (isset($_POST['new_teacher'])){
  $login = $_POST['login'];
  $password = md5($_POST['password']);
  $fio = $_POST['fio'];
  $role = '1';
  $insert_u = $mysqli->query("INSERT INTO users (login, password, role) VALUES ('$login', '$password', '$role')");
  if ($insert_u == true) {
    $id_user = $mysqli->insert_id;
    $insert_t = $mysqli->query("INSERT INTO teachers (id_user, full_name) VALUES ('$id_user', '$fio')");
    if ($insert_t == true) {
      header("Location: ".$_SERVER['HTTP_REFERER']);
    }
  }
}
// Add Subject
if (isset($_POST['add_subject'])){
  $id_teacher = $_POST['teacher'];
  $id_subject = $_POST['subject'];
  $mysqli->query("INSERT INTO teacher_subjects (id_subject, id_teacher) VALUES ('$id_subject', '$id_teacher')");
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
    <title><?=$site_name?> - Викладачі</title>
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
            <li class="active"><a href="teachers.php"><i class="fa fa-users"></i><span>Викладачі</span></a></li>
            <li><a href="specialists.php"><i class="fa fa-users"></i><span>Спеціалісти</span></a></li>
            <li><a href="cadets.php"><i class="fa fa-graduation-cap"></i><span>Курсанти</span></a></li>
            <li><a href="lessons.php"><i class="fa fa-calendar"></i><span>Заняття</span></a></li>
            <li><a href="subjects.php"><i class="fa fa-book"></i><span>Предмети</span></a></li>
            <li><a href="activity.php"><i class="fa fa-arrows"></i><span>Діяльності</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-users"></i> Викладачі</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Викладачі</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Новий викладач</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post" id="new_teacher">
                  <div class="alert alert-dismissible alert-danger" style="display: none;">
                    Введений логін зайнятий!
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">Логін</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="login" id="login" placeholder="Enter login" required="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">Пароль</label>
                    <div class="col-md-8">
                      <input class="form-control col-md-8" type="text" name="password" placeholder="Enter password" required="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">ПІБ</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="fio" placeholder="Enter full name" required="">
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" id="btn_new" type="button" name="new_teacher"><i class="fa fa-fw fa-lg fa-check-circle"></i>Додати</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Додати викладачу предмет</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post">
                  <div class="form-group">
                     <label class="control-label col-md-3" for="select">Викладач</label>
                     <div class="col-md-8">
                        <select class="form-control" id="" name="teacher" required="">
                           <?php $i=0; foreach ($teachers as $key => $teacher):?>
                            <option value="<?=$teacher['id']?>"><?=$teacher['id']?> - <?=$teacher['full_name']?></option>
                           <?php endforeach; ?> 
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-md-3" for="select">Предмет</label>
                     <div class="col-md-8">
                        <select class="form-control" id="" name="subject" required="">
                          <?php $i=0; if ($get_subjects->num_rows > 0){ while ($subject = $get_subjects->fetch_array()):?>
                           <option value="<?=$subject['id']?>"><?=$subject['id']?> - <?=$subject['name']?></option>
                          <?php endwhile; } ?> 
                        </select>
                     </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="add_subject"><i class="fa fa-fw fa-lg fa-check-circle"></i>Додати</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-12">
             <div class="card">
                <h3 class="card-title">Усі викладачі</h3>
                <div class="table-responsive">
                   <table class="table">
                      <thead>
                         <tr>
                            <th>ID</th>
                            <th>ПІБ</th>
                            <th>Предмети</th>
                         </tr>
                      </thead>
                      <tbody>
                        <?php $i=0; foreach ($teachers as $key => $teacher):?>
                        <?php
                          // Get Subjects of Teacher
                          $id_teacher = $teacher['id'];
                          $get_teacher_subjects = $mysqli->query("SELECT * FROM `teacher_subjects` INNER JOIN `subjects` ON `teacher_subjects`.`id_subject` = `subjects`.`id` WHERE id_teacher='$id_teacher' ");
                        ?>
                         <tr>
                            <td><?=$teacher['id']?></td>
                            <td><?=$teacher['full_name']?></td>
                            <td>
                              <?php $jk=1; if ($get_teacher_subjects->num_rows > 0){ while ($subject_t = $get_teacher_subjects->fetch_array()):?>
                                <?php if($jk==$get_teacher_subjects->num_rows){echo $subject_t['name'];}else{echo $subject_t['name'].", ";} ?>
                              <?php $jk++; endwhile; } ?> 
                            </td>
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
    <script src="../assets/js/action/check_loginName.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#login').on('input change', function(){
          check_loginName('#new_teacher');
        });
        $('#btn_new').click(function(){
          check_loginName('#new_teacher');
        });
      });
    </script>
  </body>
</html>