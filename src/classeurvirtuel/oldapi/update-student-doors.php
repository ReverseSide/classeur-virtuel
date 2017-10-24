<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 17.09.2016
// But    : Route de mise à jour des mises à la porte sélectionnées
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
$portes = null;
if (isset($_POST['portes']))
{
    $portes = json_decode($_POST['portes']);
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (is_null($portes))
{
    http_response_code(400);
    die("Required parameters are: portes");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Mise à jour des remarques
$db->UpdateDoors($portes);
unset($db);