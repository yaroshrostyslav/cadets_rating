<?php
require_once('config.php');
require_once('functions.php');

$action = $_POST['action'];
$mysqli = connect_db($host, $user, $pass, $db_name);

if ($action == 'login'){
	$login = $_POST['login'];
	$password = md5($_POST['password']);
	$query = $mysqli->query("SELECT * FROM users WHERE login = '$login' AND password = '$password' ");

	if ($query->num_rows > 0){
		$get_arr = $query->fetch_array();
		$_SESSION['user_id'] = $get_arr['id'];
		$role = $get_arr['role'];
		settype($role, "string");
		$_SESSION['user_role'] = $get_arr['role'];
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $query->num_rows
	));
}

if ($action == 'check_loginName'){
	$login = $_POST['login'];
	$query = $mysqli->query("SELECT `login` FROM users WHERE login = '$login' ");

	if ($query->num_rows > 0){
		$result = false;
	}else{
		$result = true;
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $result
	));
}	

if ($action == 'get_subjects'){
	$id_teacher = $_POST['id_teacher'];
	$get_teachers = $mysqli->query("SELECT `subjects`.`id`, `subjects`.`name` FROM `teacher_subjects` INNER JOIN `teachers` ON `teacher_subjects`.`id_teacher` = `teachers`.`id` INNER JOIN `subjects` ON `teacher_subjects`.`id_subject` = `subjects`.`id` WHERE id_teacher='$id_teacher'");
	foreach ($get_teachers as $key => $value) {
	  $teachers[$key]['id'] = $value['id'];
	  $teachers[$key]['name'] = $value['name'];
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $teachers
	));
}

if ($action == 'get_type_activity'){
	$id_user = $_POST['id_user'];
	$id_activity = $_POST['id_activity'];
	$get_type_activity = $mysqli->query("SELECT `type_activity`.`id`, `type_activity`.`name` FROM `specialist_activity` INNER JOIN `specialists` ON `specialist_activity`.`id_specialist` = `specialists`.`id` INNER JOIN `activity` ON `specialist_activity`.`id_activity` = `activity`.`id` INNER JOIN `type_activity` ON `type_activity`.`id_activity` = `activity`.`id` WHERE `id_user` = '$id_user' AND `type_activity`.`id_activity` = '$id_activity' ");
	foreach ($get_type_activity as $key => $value) {
	  $type_activity[$key]['id'] = $value['id'];
	  $type_activity[$key]['name'] = $value['name'];
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $type_activity
	));
}

if ($action == 'get_cadet'){
	$id_group = $_POST['id_group'];
	$get_cadets = $mysqli->query("SELECT `cadets`.`id`, `cadets`.`full_name` FROM `groups` INNER JOIN `cadets` ON `groups`.`id` = `cadets`.`id_group` WHERE `cadets`.`id_group` = '$id_group' ");
	foreach ($get_cadets as $key => $value) {
	  $cadets[$key]['id'] = $value['id'];
	  $cadets[$key]['full_name'] = $value['full_name'];
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $cadets
	));
}

if ($action == 'get_subject_of_group'){
	$id_teacher = $_POST['id_teacher'];
	$id_group = $_POST['id_group'];
	$get_subjects = $mysqli->query("SELECT `subjects`.`id`, `subjects`.`name` FROM `lessons` INNER JOIN `teachers` ON `lessons`.`id_teacher` = `teachers`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `teachers`.`id` = '$id_teacher' AND `lessons`.`id_group` = '$id_group' GROUP BY `subjects`.`id` ");
	foreach ($get_subjects as $key => $value) {
	  $subjects[$key]['id'] = $value['id'];
	  $subjects[$key]['name'] = $value['name'];
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $subjects
	));
}

if ($action == 'get_date_of_lesson'){
	$id_teacher = $_POST['id_teacher'];
	$id_group = $_POST['id_group'];
	$id_subject = $_POST['id_subject'];
	$get_subjects = $mysqli->query("SELECT `subjects`.`id`, `subjects`.`name`, `date` FROM `lessons` INNER JOIN `teachers` ON `lessons`.`id_teacher` = `teachers`.`id` INNER JOIN `groups` ON `lessons`.`id_group` = `groups`.`id` INNER JOIN `subjects` ON `lessons`.`id_subject` = `subjects`.`id` WHERE `teachers`.`id` = '$id_teacher' AND `lessons`.`id_group` = '$id_group' AND `subjects`.`id` = '$id_subject' ");
	foreach ($get_subjects as $key => $value) {
	  $subjects[$key]['timestamp'] = $value['date'];
	  $subjects[$key]['date'] = date("Y-m-d H:i", $value['date']);
	}

	echo json_encode(array(
		'status' => 'good',
		'result' => $subjects
	));
}







?>