<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 17.09.2016
// But    : Route de mise à jour des arrivées tardives sélectionnées
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
$arriveesTardives = null;
if (isset($_POST['tardives']))
{
    $arriveesTardives = json_decode($_POST['tardives']);
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (is_null($arriveesTardives))
{
    http_response_code(400);
    die("Required parameters are: tardives");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Mise à jour des remarques
$db->UpdateLates($arriveesTardives);
unset($db);