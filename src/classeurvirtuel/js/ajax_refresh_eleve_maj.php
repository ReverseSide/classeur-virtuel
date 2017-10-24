<?php
// PDO connect *********
session_start();
function connect() {
    return new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=nlhj_cepm', 'nlhj_databuser', 'classeur123', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$id_codebarre = $_GET['id_codebarre'];
$sql = "select t_eleve.id_eleve ,t_eleve.ele_nom ,t_eleve.ele_prenom,t_eleve.id_codebarre from t_eleve  where t_eleve.ele_nom like  (:keyword) or t_eleve.ele_prenom like  (:keyword) ORDER BY t_eleve.ele_nom ASC LIMIT 0, 15 ";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
	// put in bold the written text
	$Eleve_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['ele_nom'] .' ' .$rs['ele_prenom']);
	// add new option
   //  $id=$entry['ele_nom'];

    $_SESSION['id_codebarre'] =  $rs['id_codebarre'];


    echo "<a href='student_edt.php?stu='". $rs['id_codebarre']. "'>".$Eleve_name ."   </a>";




}

?>