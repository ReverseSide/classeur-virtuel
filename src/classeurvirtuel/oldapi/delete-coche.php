<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 16.08.2016
// But    : Route de suppression d'un coche pour un élève
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Vérifie que l'utilisateur soit connecté. Si non, renvoyer une erreur 403 (Forbidden)
if(empty($_SESSION['user_id']))
{
    http_response_code(403);
    die("Forbidden");
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['eleve']) || !isset($_POST['type']))
{
    http_response_code(400);
    die("Required parameters are: eleve, type");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Suppression d'une coche
$db->RemoveCoche($_POST['eleve'], $_POST['type']);
unset($db);

include_once "get-num-coches.php";
