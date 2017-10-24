<?php
session_start(  );
// Check si l'utilisateur est connect
if ( empty( $_SESSION['user_id'] ) )
{
    header( "Location:login.php" );
}

// PDO connect *********
function connect() {
    return new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm', 'nlhj_databuser', 'classeur123', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$sql = "SELECT pro_nom, pro_prenom, id_professeur from t_professeur where CONCAT_WS('', pro_nom, pro_prenom) like (:keyword)  group by pro_nom ORDER BY pro_nom ASC LIMIT 0, 15 ";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();

$sgl2="";

foreach ($list as $rs) {
	$classe_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['pro_nom']);
    echo "<a onclick=\"chadd('".$rs['id_professeur']."', '".$rs['pro_prenom']." ".$rs['pro_nom']."')\" class='list-group-item'>".$rs['pro_prenom']." ".$rs['pro_nom']."</a>";
}
?>