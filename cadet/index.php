<?php
require_once('../config.php');
require_once('../functions.php');
if (check_auth() == false || $_SESSION['user_role'] !== '3'){
	header("Location: ../index.php");
}else{
	header("Location: rating_training.php");
}