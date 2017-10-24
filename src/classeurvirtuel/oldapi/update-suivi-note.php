<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 28.04.2017
// But    : Modifie une note de suivi
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
if (!isset($_POST['id']))
{
	http_response_code(400);
	die("Required parameters are: id");
}

// Appel du fichier d'interface avec la base de données
include_once("../include/mysql.inc.php");
$db = new dbIfc();


$newData = array();
$newData['idx_professeur'] = $_SESSION['user_id'];
if (isset($_POST['type']))
	$newData['sui_type'] = $_POST['type'];

if (isset($_POST['commentaire']))
	$newData['sui_commentaire'] = $_POST['commentaire'];

if (isset($_POST['date']))
{
	if (is_numeric($_POST['date']))
		$_POST['date'] = strftime("%Y-%m-%d", $_POST['date']);

	$newData['sui_date'] = $_POST['date'];
}

if (isset($_POST['matcode']))
	$newData['idx_matcode'] = $_POST['matcode'];

if (isset($_POST['classe']))
	$newData['idx_classe'] = $_POST['classe'];

// Enregistre en base
$db->UpdateSuiviCours($_POST['id'], $newData);
