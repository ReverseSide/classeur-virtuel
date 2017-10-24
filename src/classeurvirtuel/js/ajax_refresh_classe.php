<?php
// PDO connect *********
//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$sql = "
select count(t_eleve.id_eleve )as 'Nombre',t_classe.cla_nom ,t_classe.id_classe from t_eleve,t_classe where t_classe.id_classe = t_eleve.idx_classe and t_classe.cla_nom like  (:keyword)  group by t_classe.cla_nom ORDER BY cla_nom ASC LIMIT 0, 15 ";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
	// put in bold the written text
	$classe_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['cla_nom']);
	// add new option
     $id=$entry['id_classe'];

    $_SESSION['IdClasse'] =  $rs['id_classe'];


    echo "<a href='classe_edt.php?idclasse=" . str_replace("'", "\'", $rs['id_classe']). " ' class='list-group-item'>".$classe_name ."  <span class='badge'> ".$rs['Nombre']." Elèves </span>  </a>";




}

?>