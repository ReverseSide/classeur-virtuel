<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 22.08.2016
// But    : Route de mise à jour d'un mot de passe
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
if (!isset($_POST['oldPassword']) || !isset($_POST['newPassword']))
{
    http_response_code(400);
    die("Required parameters are: oldPassword, newPassword");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Tentative de login de l'utilisateur (pour vérifier son mot de passe)
$logged = $db->Login($_SESSION['login'], $_POST['oldPassword']);

if ($logged)
{
    $db->UpdatePassword($_SESSION['user_id'], $_POST['newPassword']);
    die("success");
}
else
{
    die("failure");
}
