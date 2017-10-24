<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 20.08.2016
// But    : Route de modification et de suppression d'un commentaire
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['commentaire']) || !isset($_POST['id']))
{
    http_response_code(400);
    die("Required parameters are: commentaire, id");
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

// Insertion du nouveau commentaire
$bd->UpdateComment($_POST['id'], $_POST['commentaire']);
unset($bd);
