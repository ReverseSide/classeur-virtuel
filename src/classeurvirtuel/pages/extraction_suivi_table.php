<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Création   : 29.04.2017
// But    : Extraction CSV des notes de suivi
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
	header("Location:login.php");
}

// Import de la classe de connexion avec la base de données
include_once("../include/mysql.inc.php");
$db = new dbIfc();

$timestamps = array();

// Si le timestamp est "CURRENT_YEAR", récupérer tous les timestamps des jours où il y a eur cours dans l'année scolaire
if (isset($_GET['timestamp']) && $_GET['timestamp'] === "CURRENT_YEAR")
{
	// Trouve l'année scolaire en cours
	$startYear = (int)strftime("%Y", time());
	if (strftime("%m", time()) <= 7)
		$startYear--;

	// Le début d'une année scolaire se situe aparement un Lundi entre le 21 et le 27 août...
	// Du coup, on prend une date au 27 août et on calcule l'écart qu'il y a en trop pour tomber sur un Lundi
	$startRange = strtotime("27.08.$startYear");
	$dayOfWeek = (int)strftime("%w", $startRange);
	$startRange -= $dayOfWeek * 86400;

	// Encore de la pure supposition, mais il semble que la meilleure façon de trouver la fin de l'année scolaire
	// est de chercher un vendredi compris entre le 30 juin et le 6 juillet. Les années scolaires n'ont pas toutes
	// la même longueur! (p.ex l'année 2017-2018 fait une semaine de plus que d'habitude).
	$endRange = strtotime("30.06." . ($startYear + 1));
	$dayOfWeek = (int)strftime("%w", $endRange);
	$endRange += ((11 - $dayOfWeek) % 7) * 86400;
	$endRange += 86399; // Ajout d'un jour moins une seconde pour avoir la fin du vendredi

	// On commence par reculer légèrement la date de début, comme ça on tombe sur le dimanche avant la rentrée
	// Cela permet à la fonction get-next-active-day() de trouver le premier lundi si cette classe a cours le lundi
	$_GET['raw'] = true;
	$_GET['timestamp'] = $startRange - 1;

	// Il y a sans doute un façon plus optimisée de faire ça, mais pour l'instant ça suffira:
	// On récupère tous les jours où cette classe à cours en appellant la route get-next-active-day()
	do
	{
		ob_start();
		$nextTimestamp = include("../api/get-next-active-day.php");
		ob_end_clean();

		if ($nextTimestamp > $endRange)
			break;

		$timestamps[] = $nextTimestamp;
		$_GET['timestamp'] = $nextTimestamp;
	}
	while(true);
}
// Récupère les tables à afficher. Il peut y avoir plusieurs tables en passant plusieurs timestamps séparés par des virgules
else if (isset($_GET['timestamp']))
{
	$timestamps = $_GET['timestamp'];
	$timestamps = explode(",", $timestamps);
}

// Récupère les données
$comments = array();
foreach ($timestamps as $timestamp)
{
	$newData = $db->GetSuiviCours($_GET['idclasse'], $timestamp);
	$comments = array_merge($comments, $newData);
}
if (!empty($comments))
{
	$headerRow = array();
	foreach ($comments['0'] as $colName => $value)
	{
		$headerRow[] = $colName;
	}
	$comments = array_merge(array($headerRow), $comments);
}

// Génère le CSV dans un flux temporarire
$fp = fopen("php://memory", "w+");
foreach ($comments as $comment)
{
	fputcsv($fp, $comment, ";");
}
fseek($fp, 0);
$out = stream_get_contents($fp);
fclose($fp);


// Headers de sortie pour le CSV
//$out = iconv("UTF-8", "WINDOWS-1252", $out);
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Length: " . strlen($out));
header("Content-type: text/x-csv;charset=WINDOWS-1252");
header("Content-Disposition: attachment; filename=suivi-cours.csv");
echo $out;
