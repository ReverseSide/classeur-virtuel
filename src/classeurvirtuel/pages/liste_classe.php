<?php
session_start();
/**
 * Created by PhpStorm.
 * User: vincent.montet
 * Date: 11.08.2017
 * Time: 13:42
 *
 * Comment : Quick and Dirty
 *
 */

//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

include '../include/bdd.php';

$bd=new dbIfc();
$classInfo = $bd->GetClassInfo($_GET['idclasse']);


//Check si connecté
if(!empty($_SESSION['user_id']) )
{
    if(!empty($_GET['idclasse']))
    {
        $_SESSION['class']=$_GET['idclasse'];
    }
}
else
{
    header("Location:login.php");
}


// filename for download
$filename = "liste_de_classe_" . $classInfo[cla_nom] ."_". date('Ymd') . ".xls";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/vnd.ms-excel");

$flag = false;
$db = new dbIfc();

$tabStudent=$db->GetMyClass($_GET['idclasse']);

echo "<h1>" . $classInfo[dep_nom] . " - " . $classInfo[cla_nom] . " - " . $classInfo[type_nom] ."</h1>";
echo "<h2> Jour(s) de cours : ". $classInfo[cla_jourdecours]  ."</h2>";

echo "<table><tr><th>Photo</th>﻿<th> ID </th><th> Code Barre </th><th> Nom </th><th> Prénom </th><th> Classe </th><th> Dispence ECG </th><th> Date de Naissance</th><th> Dispense BT</th><th> Dispense Sport</th><th> Dérogation</th><th> Desavantage</th><th> Statut de l'élève </th></tr>";

foreach($tabStudent as $key=>$row) {
    echo "<tr style=\"height:60px;\" >";
    $id_eleve_photo = substr("000000" . $row["id_codebarre"], -6);
    echo '<td><img src="https://classeur.cepm.ch/pages/images/utilisateurs/'. $id_eleve_photo .'.jpg" alt="'. $row[ele_nom] ." ". $row[ele_prenom] . '" height="60"></td>';
    foreach($row as $key1=>$row1){
        echo "<td>" . $row1 . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

exit;