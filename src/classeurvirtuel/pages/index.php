<?php
session_start();
if(!empty($_SESSION['user_id']))
{
    header("Location:management.php");
} else {
	header("Location:login.php");
}
?>
