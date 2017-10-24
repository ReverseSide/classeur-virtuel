<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 22.08.2016
// But    : Vérification du nom d'utilisateur et du mot de passe
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Vérifie que l'utilisateur soit connecté. Si non, renvoyer une erreur 403 (Forbidden)
/*
if(empty($_SESSION['user_id']))
{
    http_response_code(403);
    die("Forbidden");
}*/

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['username']) || !isset($_POST['password']))
{
    http_response_code(400);
    die("Required parameters are: username, password");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Tentative de login de l'utilisateur (pour vérifier son mot de passe)
$logged = $db->Login($_POST['username'], $_POST['password']);

if ($logged)
{
    die("success");
}
else
{
    die("failure");
}
