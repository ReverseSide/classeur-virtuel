<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 20.08.2016
// But    : Route d'ajout d'un commentaire à la dernière coche ajoutée
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
if (!isset($_POST['eleve']) || !isset($_POST['commentaire']))
{
    http_response_code(400);
    die("Required parameters are: eleve, commentaire");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Ajout du commentaire
$db->AddLastCocheComment($_POST['eleve'], $_POST['commentaire']);
unset($db);
