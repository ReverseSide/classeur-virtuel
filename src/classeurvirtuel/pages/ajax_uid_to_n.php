<?php
session_start(  );
// Check si l'utilisateur est connect
if ( empty( $_SESSION['user_id'] ) )
{
    header( "Location:login.php" );
}

// PDO connect *********
function connect() {
    return new PDO('mysql:host=localhost;dbname=cepv_users', 'cepv_users_ro', 'KbP4xjVT', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();

if ( isset( $_GET['uID'] ) ) 
	if ( is_numeric( $_GET['uID'] ) ) {
		$msgid = $_GET['uID']  ;
		$sql = "SELECT users.nom, users.prenom from users where users.uid='".$msgid."' ";
		$query = $pdo->prepare($sql);		
		$query->execute();
		$list = $query->fetchAll();
		foreach ($list as $rs) {			
			echo "".$rs['prenom']." ".$rs['nom']."";
		}

	}
?>