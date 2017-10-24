<?php
// PDO connect *********
session_start();

function connect() {
    return new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm', 'nlhj_databuser', 'classeur123', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$sql = "select id_entreprise , ent_nom ,ent_rue, ent_localite from t_entreprise where ent_nom like (:keyword) or ent_rue like (:keyword) or ent_localite like (:keyword) ORDER BY ent_nom ASC LIMIT 0, 15 ";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
	// put in bold the written text
	$entreprise_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['ent_nom'] .' | ' .$rs['ent_localite']);
	// add new option
    echo "<a href='entreprise_edit.php?id_entreprise=".$rs['id_entreprise']."' class='list-group-item'>".$entreprise_name."   </a>";
}

?>