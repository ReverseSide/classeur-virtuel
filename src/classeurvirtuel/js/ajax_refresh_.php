<?php
// PDO connect *********
function connect() {
    return new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm', 'nlhj_databuser', 'classeur123', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$sql = "select * from t_classe where t_classe.cla_nom like (:keyword) ORDER BY cla_nom ASC LIMIT 0, 10";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
	// put in bold the written text
	$country_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['cla_nom']);
	// add new option
    echo '<li onclick="set_item(\''.str_replace("'", "\'", $rs['cla_nom']).'\')">'.$country_name.'</li>';
}
?>