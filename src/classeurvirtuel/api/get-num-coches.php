<?php
if(!isset($_SESSION)){session_start();}
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 16.08.2016
// But    : Obtient le nombre de coches d'un certain type pour l'élève donné
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

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['eleve']) || !isset($_POST['type']))
{
    http_response_code(400);
    die("Required parameters are: eleve, type");
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$db = new dbIfc();

// Récupération du nouveau nombre de coches
$nbCoches = $db->GetNumCoches($_POST['eleve'], $_POST['type']);

$bgColor = "#F9F9F9";
if ($nbCoches > 0) $bgColor = "#FBFB95";
if ($nbCoches > 2) $bgColor = "#EB8732";
if ($nbCoches > 4) $bgColor = "#E84600";

$addScript = "AddCoche(this, ". $_POST['eleve'] .", ". $_POST['type'] .")";
$removeScript = "RemoveCoche(this, ". $_POST['eleve'] .", ". $_POST['type'] .");event.stopPropagation();";
?>
<td style="background-color:<?= $bgColor ?>" onclick="<?= $addScript ?>">
    <div style="position:relative;" class="nb-container"><?= $nbCoches ?><div class="minus" onclick="<?= $removeScript ?>">-</div>
    </div>
</td>
