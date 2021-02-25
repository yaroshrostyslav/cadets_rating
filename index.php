<?php
require_once('config.php');
require_once('functions.php');

if (check_auth() == false){
	header("Location: login.php");
}else{
	switch ($_SESSION['user_role']) {
	    case '0':
	        header("Location: admin/");
	        break;
	    case '1':
	        header("Location: teacher/");
	        break;
	    case '2':
	        header("Location: specialist/");
	        break;
	    case '3':
	        header("Location: cadet/");
	        break;
	}
}

?>