<?php
session_start();
/**
 * Created by PhpStorm.
 * User: drin
 * Date: 18.10.2016
 * Time: 6:10 PD
 */

//inclusion de la classe d'interaction avec la base de données
include_once('../../include/mysql.inc.php');

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

//Va cherhcer les informations de l'élève
$bd=new dbIfc();
$tabStudent=$bd->GetStudent($_POST['stuBarcode']);
unset($bd);


//check var
// define variables and set to empty values
$stDateDeb = $stDateFin = $stEntNom = $stEntRue = $stEntNPA = $stEntLocalite = $stEntCant = "";
$stEntContNom = $stEntContPrenom = $stEntContTel = $stEntContMob = $stEntContEmail = $stuID = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //check values
    $stDateDeb = test_input($_POST["inpstaDateDeb"]);
    $stDateFin = test_input($_POST["inpstaDateFin"]);
    $stEntNom = test_input($_POST["inpstaEntNom"]);
    $stEntRue = test_input($_POST["inpstaEntRue"]);
    $stEntNPA = test_input($_POST["inpstaEntNpa"]);
    $stEntLocalite = test_input($_POST["inpstaEntLocalite"]);
    $stEntCant = test_input($_POST["inpstaEntCanton"]);
    $stEntContNom = test_input($_POST["inpstaEntConNom"]);
    $stEntContPrenom = test_input($_POST["inpstaEntConPrenom"]);
    $stEntContTel = test_input($_POST["inpstaEntConTel"]);
    $stEntContMob = test_input($_POST["inpstaEntConMob"]);
    $stEntContEmail = test_input($_POST["inpstaEntConEmail"]);
    $stuID = test_input($tabStudent[0]["id_eleve"]);
	$codebarre = test_input($tabStudent[0]["id_codebarre"]);


    $msgAddInternShip = "une erreur s'est produite lors de l'enregistrement du stage";
    if(!empty(trim($stDateDeb)) && !empty(trim($stDateFin)) && date_create($stDateFin) >= date_create($stDateDeb)){
        // add internship
        $bd=new dbIfc();
        $msgAddInternShip=$bd->AddInternShip($stDateDeb, $stDateFin, $stEntNom, $stEntRue, $stEntNPA, $stEntLocalite, $stEntCant, $stEntContNom, $stEntContPrenom, $stEntContTel, $stEntContMob, $stEntContEmail, $stuID);
        unset($bd);
    }
    print("{$msgAddInternShip}<br><br> Vous allez etre redirige dans quelques instants. <meta http-equiv='refresh' content='3; url=../student_edt.php?stu=".$codebarre."' />");
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}