<?php
include '../include/bdd.php';

//SELECT les élèves et synchronise les codes barres entre les bases t_eleve & t_sportete
$reponse = $bdd->query('SELECT id_eleve, id_codebarre FROM `t_eleve` ORDER BY id_eleve');

//parcourt 
while ($donnees = $reponse->fetch()) {
	$codeBarre = $donnees['id_codebarre'];
	$id = $donnees['id_eleve'];
	//echo $codeBarre . '<br>';
	// On ajoute une entrée dans la table t_sportete
	$sqlAdd = "INSERT INTO `t_sportete`(id_eleve, choixSport, depart, arrivee, codeBarre) VALUES ('$id', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '$codeBarre')";
	$queryAdd = $bdd->prepare($sqlAdd);
	$queryAdd->execute();
}
$reponse->closeCursor();

Echo 'OK';

?>