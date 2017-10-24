<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Date dernière modification   : 02.05.2016
// But    : Page de gestion de ses classes
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
include '../include/bdd.php';

$bd=new dbIfc();

echo date('Y-m-d',strtotime('20.08.1994'));

$tab=$bd->IsVocation($_GET['date']);

echo $tab ? 'true' : 'false';

if($bd->IsAdmin($_SESSION['user_id'])==true){ echo "attention je suis admin"; }

if (preg_match("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/", '20.08.1994')){

    echo "Yes je suis une date";

}

echo similar_text("Hello les gars", "Hello les filles");
