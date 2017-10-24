<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 08.08.2016
// But    : Route de mise à jour du status d'un élève
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
if(!empty($_POST['idclasse']))
{
    $_SESSION['class']=$_POST['idclasse'];
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if (!isset($_POST['id']) || !isset($_POST['period']) || !isset($_POST['status']) || !isset($_POST['codebarre']) || !isset($_POST['cours']))
{
    http_response_code(400);
    die("Required parameters are: id, period, status, codebarre, cours");
}

// Calcul de la date actuelle si aucun paramètre de date fourni
$operationDate = time();
if (isset($_POST['timestamp']))
{
    $operationDate = $_POST['timestamp'];
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$bd = new dbIfc();

// En cas de dispense de l'élève, revenir à l'état "P"


// Pertistage des cas particuliers dans la BDD
$id_eleve=$_POST['id'];
$id_professeur=$_SESSION['user_id'];
$periode=$_POST['period'];
$status=$_POST['status'];
$classe=$_SESSION['class'];
$codebarre=$_POST['codebarre'];
$idCours=$_POST['cours'];
$blnInsert = $bd->InsertParticuliar($id_eleve,$id_professeur,$periode,$status, $classe, $idCours, $codebarre, $operationDate);

// Récupère le résultat inscrit en BdD
$tabLates = $bd->GetClassLate($_SESSION['class'], $operationDate);
$tabDoors = $bd->GetClassDoor($_SESSION['class'], $operationDate);
$tabMissings = $bd->GetClassMissing($_SESSION['class'], $operationDate);
$tabGym = $bd->GetClassOubliGym($_SESSION['class'], $operationDate);
$tabSante = $bd->GetClassSante($_SESSION['class'], $operationDate);
unset($bd);

// Essaie de retrouver le résultat inscrit en BdD. Cela permet de s'assurer que l'enregistrement s'est bien déroulé
$couleur = "#00BE67";
$status = "p";
foreach ($tabMissings as $entrymissing)
{
    if($entrymissing['idx_eleve']==$id_eleve && $entrymissing['abs_periode']==$periode)
    {
        if ($entrymissing['abs_excuse'] === "Oui")
        {
            $couleur = "#CCCCCC";
            $status = "e";
        }
        else
        {
            $couleur = "#12BBF0";
            $status = "a";
        }
    }
}
foreach($tabLates as $entrylates)
{
    if($entrylates['idx_eleve']==$id_eleve && $entrylates['tar_periode']==$periode)
    {
        $couleur = "#D4AC0D";
        $status = "t";
    }
}
foreach ($tabDoors as $late)
{
    if($late['idx_eleve'] == $id_eleve && $late['por_periode'] == $periode)
    {
        $couleur = "#F87373";
        $status = "mp";
    }
}
foreach ($tabGym as $gym)
{
    if($gym['idx_eleve'] == $id_eleve && $gym['oub_periode'] == $periode)
    {
        $couleur = "#FFF033";
        $status = "g";
    }
}
foreach ($tabSante as $sante)
{
    if($sante['idx_eleve'] == $id_eleve && $sante['san_periode'] == $periode)
    {
        $couleur = "#EA94FF";
        $status = "s";
    }
}

// Retour du résultat, il faut remplacer en JS la valeur de l'élément cliqué par cette valeur à jour
$ucStatus = strtoupper($status);
$script = "UpdateParticuliar(this, $id_eleve, $periode, \"$status\", $codebarre, $idCours, $operationDate)";
print("<td style='background-color: $couleur' onclick='$script'><div class='dont-click'></div>$ucStatus</td>");

