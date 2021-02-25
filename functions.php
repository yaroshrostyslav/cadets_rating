<?php
require_once('config.php');
error_reporting(E_ALL & ~E_NOTICE);
session_start();

function connect_db($host, $user, $pass, $db_name){
	$mysqli = new mysqli("$host", "$user", "$pass", "$db_name");
	$mysqli->set_charset("utf8");
	if ($mysqli->connect_errno) {
        printf("Ошибка соеденения", $mysqli->connect_error);
	    exit();
	}
	return $mysqli;
}

function check_auth() {
	if ($_SESSION['user_role'] == null || $_SESSION['user_role'] == ''){
		return false;
	}else{
		return true;
	}
}


function calcRate($id_activity, $sum){
	if ($id_activity == 0){
		return $calc = ($sum) * 0.7;
		return round($calc, 2);
	}
	elseif ($id_activity == 1){
		return $calc = ($sum / 3) * 0.08;
		return round($calc, 2);
	}
	elseif ($id_activity == 2){
		return $calc = (($sum / 425) * 100) * 0.15;
		return round($calc, 2);
	}
	elseif ($id_activity == 3){
		return $calc = ($sum / 5) * 0.07;
		return round($calc, 2);
	}
	else{
		return 0;
	}
}




?>