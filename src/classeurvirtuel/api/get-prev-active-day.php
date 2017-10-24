<?php
@session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 19.08.2016
// But    : Lien vers timeclock.php du jour précédent le plus proche avec des cours
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
if (!isset($_GET['idclasse']) || !isset($_GET['timestamp']))
{
    http_response_code(400);
    die("Required parameters are: idclasse, timestamp");
}

// Appel du fichier d'interface avec la base de données
include_once("../include/mysql.inc.php");
$db = new dbIfc();

// Nombre maximum de jours à parcourir (pour éviter une boucle infinie si la classe n'a plus cours par exemple)
$maxDiff = 60;
$timestamp = $_GET['timestamp'];

// Trouve le prochain jour ayant un cours
do
{
    $timestamp -= 1 * 24 * 60 * 60;
    $maxDiff--;
    $classSchedule = $db->GetSchedule($_GET['idclasse'], $timestamp);
}
while(empty($classSchedule) && $maxDiff > 0);

// Si aucun résultat trouvé, remettre le jour au timestamp initial pour reservir la même page
if (empty($classSchedule))
    $timestamp = $_GET['timestamp'];

// Si un résultat brut a été demandé, uniquement retourner le nouveau timestamp
if (isset($_GET['raw']) && $_GET['raw'])
{
	print((string)$timestamp);
	return $timestamp;
}

// Redirection vers la bonne page avec le bon jour
header("Location:../pages/timeclock.php?idclasse=". $_GET['idclasse'] ."&timestamp=". $timestamp);
