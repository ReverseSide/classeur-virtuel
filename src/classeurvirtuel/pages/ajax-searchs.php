
<?php
$con = new mysqli("nlhj.myd.infomaniak.com","nlhj_databuser","nlhj_cepm","classeur123");

 
/* veillez bien à vous connecter à votre base de données */

$term = $_GET['term'];

$requete = $bdd->prepare('select * from t_classe where t_classe.cla_nom like :term'); // j'effectue ma requête SQL grâce au mot-clé LIKE
$requete->execute(array('term' => '%'.$term.'%'));

$array = array(); // on créé le tableau

while($donnee = $requete->fetch()) // on effectue une boucle pour obtenir les données
{
    array_push($array, $donnee['cla_nom']); // et on ajoute celles-ci à notre tableau
}

echo json_encode($array); // il n'y a plus qu'à convertir en JSON

?>
 
 
 <?php
 
$mysqli = new MySQLi("nlhj.myd.infomaniak.com","nlhj_databuser","nlhj_cepm","classeur123");
/* Connect to database and set charset to UTF-8 */
if($mysqli->connect_error) {
  echo 'Database connection failed...' . 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
  exit;
} else {
  $mysqli->set_charset('utf8');
}
/* retrieve the search term that autocomplete sends */
$term = trim(strip_tags($_GET['term'])); 
$a_json = array();
$a_json_row = array();
if ($data = $mysqli->query("select * from t_classe where t_classe.cla_nom like  '%$term%'")) {
	while($row = mysqli_fetch_array($data)) {
		$firstname = htmlentities(stripslashes($row['cla_nom ']));
		$lastname = htmlentities(stripslashes($row['cla_nom']));
		$code = htmlentities(stripslashes($row['cla_nom']));
		$a_json_row["id"] = $code;
		$a_json_row["value"] = $firstname.' '.$lastname;
		$a_json_row["label"] = $firstname.' '.$lastname;
		array_push($a_json, $a_json_row);
	}
}
// jQuery wants JSON data
echo json_encode($a_json);
flush();

$mysqli->close();
?>