<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 12.08.2016
// But    : Route de création d'un nouveau commentaire
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
if(!empty($_POST['classe']))
{
    $_SESSION['class']=$_POST['classe'];
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['commentaire']) || !isset($_POST['matcode']) || !isset($_POST['professeur']) || !isset($_POST['classe']))
{
    http_response_code(400);
    die("Required parameters are: commentaire, matcode, professeur, classe");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$bd = new dbIfc();

// Insertion du nouveau commentaire
$bd->InsertComment($_POST['commentaire'], $_POST['matcode'], $_POST['professeur'], $_POST['classe']);
unset($bd);

// Affichage de la liste des commentaires en retour
include_once "get-comments.php";
