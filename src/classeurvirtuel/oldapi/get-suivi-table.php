<?php
@session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 25.04.2017
// But    : Génère un tableau avec le suivi des cours d'un jour donné
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

if (!is_numeric($_GET['timestamp']))
{
	$_GET['timestamp'] = strtotime($_GET['timestamp']);
}

// Appel du fichier d'interface avec la base de données
include_once("../include/mysql.inc.php");
$db = new dbIfc();




// Récupère toutes les notes pour le jour
$notes = $db->GetSuiviCours($_GET['idclasse'], $_GET['timestamp']);

// Récupère toutes les matières du jour
$branches = $db->GetSchedule($_GET['idclasse'], $_GET['timestamp']);

// Ajoute un champ virtuel "Remarques". La plupart des champs ne sont pas utilisés et pourraient être enlevés si qqn a le temps de tester
$branches[] = array(
	'id_sdh' => 0,
	'idx_classe' => 0,
	'cou_duree' => 0,
	'cou_heuredebut' => '23:59:59',
	'cla_nom' => '',
	'cou_alternance' => 'H',
	'cou_matcode' => '',
	'cou_matlibelle' => 'Remarques',
	'pro_nom' => '',
	'pro_prenom' => ''
);

// Traitement du résultat
$formattedNotes = array();
foreach ($branches as $branch)
{
	// Raccourcis vers des variables dans le tableau
	$matCode = $branch['cou_matcode'];
	$matName = $branch['cou_matlibelle'];

	// Ne pas dupliquer les cours
	if (isset($formattedNotes[$matCode]))
		continue;

	// Ajoute le nouveau noeud
	$formattedNotes[$matCode] = array(
		'name' => $matName,
		'matCode' => $matCode,
		'date' => $_GET['timestamp'],
		'class' => $_GET['idclasse'],
	);

	// Ajoute les notes et les devoirs au noeud
	$formattedNotes[$matCode]['trackingNotes'] = array();
	$formattedNotes[$matCode]['homeWorks'] = array();
	foreach ($notes as $note)
	{
		if ($matCode !== $note['idx_matcode'])
			continue;

		$profName = "";
		if (!empty($note['pro_prenom']))
			$profName .= $note['pro_prenom']['0'] . ". ";
		if (!empty($note['pro_nom']))
			$profName .= $note['pro_nom'];

		$formattedNotes[$matCode][$note['sui_type']][] = array(
			'id' => $note['id_suivicours'],
			'date' => $_GET['timestamp'],
			'matCode' => $note['idx_matcode'],
			'type' => $note['sui_type'],
			'comment' => $note['sui_commentaire'],
			'class' => $_GET['idclasse'],
			'canEdit' => ($note['idx_professeur'] == $_SESSION['user_id']),
			'profName' => $profName
		);
	}
}

// Supprime les clés pour avoir un tableau (et pas un objet) dans le JSON
$formattedNotes = array_values($formattedNotes);

// Retour du résultat au format JSON
print(json_encode($formattedNotes));

// Retour PHP au cas où le fichier est inclus au lieu d'être appelé en HTTP
return $formattedNotes;
