<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 27.04.2017
// But    : Crée une note de suivi
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Check si utilisateur connecté
if(empty($_SESSION['user_id']))
{
	http_response_code(403);
	die("Forbidden");
}

// Check des paramètres obligatoires
if (!isset($_POST['type']) || !isset($_POST['commentaire']) || !isset($_POST['date']) || !isset($_POST['matcode']) || !isset($_POST['classe']))
{
	http_response_code(400);
	die("Required parameters are: type, commentaire, date, matcode, classe");
}

// Appel du fichier d'interface avec la base de données
include_once("../include/mysql.inc.php");
$db = new dbIfc();

// Formatte certains paramètres
if (is_numeric($_POST['date']))
{
	$_POST['date'] = strftime("%Y-%m-%d", $_POST['date']);
}

// Enregistre en base
$db->InsertSuiviCours($_POST['type'], $_POST['commentaire'], $_POST['date'], $_POST['matcode'], $_POST['classe'], $_SESSION['user_id']);
http_response_code(201);
