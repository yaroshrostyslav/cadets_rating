<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '0'){
	header("Location: ../index.php");
}

/******************** Actions ********************/
$mysqli = connect_db($host, $user, $pass, $db_name);
// Get All Activities
$get_activity = $mysqli->query("SELECT * FROM activity ORDER BY id DESC");
foreach ($get_activity as $key => $value) {
  $activities[$key]['id'] = $value['id'];
  $activities[$key]['name'] = $value['name'];
}

// New Type Activity
if (isset($_POST['add_type_activity'])){
  $id_activity  = $_POST['activity'];
  $name = str_replace("'", "\'", $_POST['name_type']);
  $mysqli->query("INSERT INTO type_activity (id_activity, name) VALUES ('$id_activity', '$name')");
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
    <title><?=$site_name?> - Діяльності</title>
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
            <li><a href="subjects.php"><i class="fa fa-book"></i><span>Предмети</span></a></li>
            <li class="active"><a href="activity.php"><i class="fa fa-arrows"></i><span>Діяльності</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-arrows"></i> Діяльності</h1>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li><a href="#">Діяльності</a></li>
            </ul>
          </div>
        </div>
        <div class="row">

          <div class="col-md-6">
            <div class="card">
              <h3 class="card-title">Додати діяльності вид</h3>
              <div class="card-body">
                <form class="form-horizontal" action="" method="post">
                  <div class="form-group">
                     <label class="control-label col-md-3" for="select">Діяльність</label>
                     <div class="col-md-8">
                        <select class="form-control" id="" name="activity" required="">
                          <?php $i=0; foreach ($activities as $key => $activity):?>
                           <option value="<?=$activity['id']?>"><?=$activity['id']?> - <?=$activity['name']?></option>
                          <?php endforeach; ?> 
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">Назва виду</label>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="name_type" placeholder="Enter type" required="">
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-8 col-md-offset-3">
                        <button class="btn btn-primary icon-btn" type="submit" name="add_type_activity"><i class="fa fa-fw fa-lg fa-check-circle"></i>Додати</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-12">
             <div class="card">
                <h3 class="card-title">Усі діяльності</h3>
                <div class="table-responsive">
                   <table class="table">
                      <thead>
                         <tr>
                            <th>ID</th>
                            <th>Назва</th>
                            <th>Види</th>
                         </tr>
                      </thead>
                      <tbody>
                        <?php $i=0; foreach ($activities as $key => $activity):?>
                        <?php
                          // Get Type of Activity
                          $id_activity = $activity['id'];
                          $get_type_activity = $mysqli->query("SELECT `type_activity`.`name` FROM `type_activity` INNER JOIN `activity` ON `type_activity`.`id_activity` = `activity`.`id` WHERE id_activity='$id_activity' ");
                        ?>
                         <tr>
                            <td><?=$activity['id']?></td>
                            <td><?=$activity['name']?></td>
                            <td>
                              <?php $jk=1; if ($get_type_activity->num_rows > 0){ while ($type_activity = $get_type_activity->fetch_array()):?>
                                <?php if($jk==$get_type_activity->num_rows){echo $type_activity['name'];}else{echo $type_activity['name'].", ";} ?>
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
  </body>
</html>