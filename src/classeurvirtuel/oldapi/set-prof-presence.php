<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 17.09.2016
// But    : Route de modification du statut de présence d'un prof pour un cours
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['cours']) || !isset($_POST['date']) || !isset($_POST['present']))
{
    http_response_code(400);
    die("Required parameters are: cours, date, present");
}

// Vérifie que l'utilisateur soit connecté. Si non, renvoyer une erreur 403 (Forbidden)
if(empty($_SESSION['user_id']))
{
    http_response_code(403);
    die("Forbidden");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$bd = new dbIfc();

// Effectue la modification
if ($_POST['present'] == "true")
{
    $bd->AddProfPresence($_SESSION['user_id'], $_POST['cours'], $_POST['date']);
}
else
{
    $bd->RemoveProfPresence($_SESSION['user_id'], $_POST['cours'], $_POST['date']);
}
