<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 21.08.2016
// But    : Route de mise à jour de toutes les remarques d'un élève
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

// Décode les remarques
$remarques = null;
if (isset($_POST['remarques']))
{
    $remarques = json_decode($_POST['remarques']);
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['eleve']) || is_null($remarques))
{
    http_response_code(400);
    die("Required parameters are: eleve, remarques");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Mise à jour des remarques
$db->UpdateStudentNotices($_POST['eleve'], $remarques);
unset($db);