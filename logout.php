<?php
require_once('config.php');
require_once('functions.php');

unset($_SESSION['user_role']);
header("Location: login.php");

?>