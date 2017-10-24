<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Sebastien Metthez
// Date dernière modification   : 02.06.2017
// Date de Création             : 02.06.2017
// But    : Route de modification d'élève
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************



include 'api.inc.php';

if ( !isset( $_GET['rlgl'] ) )
{   http_response_code( 400 ) ;
    die( "Required parameters are: rl") ;
} else	
	$rlgl =  $_GET['rlgl'] ;

if ( !isset( $_GET['stuid'] ) )
{   http_response_code( 400 ) ;
    die( "Required parameters are: stu") ;
} else
	$stu =  $_GET['stuid'] ;

if ( empty( $_SESSION['user_id'] ) )
{   http_response_code( 403 ) ;
    die( "Forbidden" ) ;
}

if ( empty( $_SESSION['user_id'] ) )
{   http_response_code( 403 ) ;
    die( "Forbidden" ) ;
}

include '../include/bdd.php';
include_once('../include/mysql.inc.php');
$bd = new dbIfc( ) ;


$SETREPLEGAL_DEBUG = 0 ;
$replegal = array("rep_politesse", "rep_prenom", "rep_nom", "rep_Rue", "rep_npa", "rep_localite", "rep_tel1", "rep_tel2", "rep_numeromobile");
$previous = "javascript:history.go(-1)";

if(isset($_SERVER['HTTP_REFERER'])) { $previous = $_SERVER['HTTP_REFERER']; } 

	
if (isset( $_POST['rep_nom'] ) && isset( $_POST['rep_prenom'] ) && isset( $_POST['rep_politesse'] ) && isset( $_POST['rep_Rue']) && isset( $_POST['rep_npa'] ) && isset( $_POST['rep_localite'] ) && isset( $_POST['rep_tel1'] ) && isset( $_POST['rep_tel2'] ) && isset( $_POST['rep_numeromobile'] ))
{
		$rep_npa = $_POST['rep_npa'];
		if ( !is_numeric( $rep_npa ) )
			$rep_npa = 0;
		$rep_nom = $_POST['rep_nom'];
		$rep_prenom = $_POST['rep_prenom'];
		$rep_politesse = $_POST['rep_politesse'];
		$rep_Rue = $_POST['rep_Rue'];
		$rep_localite = $_POST['rep_localite'];
		$rep_tel1 = $_POST['rep_tel1'] ;
		$rep_tel2 = $_POST['rep_tel2'] ;
		$rep_numeromobile = $_POST['rep_numeromobile'];
        // $representant1="insert into t_representantlegal (rep_nom, rep_prenom, rep_politesse, rep_Rue, rep_npa, rep_localite, rep_tel1, rep_tel2, rep_numeromobile) values ('".$rep_nom."', '".$rep_prenom."', '".@$_POST['rep_politesse']."', '".@$_POST['rep_Rue']."', '".@$_POST['rep_npa']."', '".@$_POST['rep_localite']."', '".@$_POST['rep_tel1']."', '".@$_POST['rep_tel2']."', '".@$_POST['rep_numeromobile']."')";
        $representant1="insert into t_representantlegal (rep_nom, rep_prenom, rep_politesse, rep_Rue, rep_npa, rep_localite, rep_tel1, rep_tel2, rep_numeromobile) values ('".$rep_nom."', '".$rep_prenom."', '".$rep_politesse."', '".$rep_Rue."', '".$rep_npa."', '".$rep_localite."', '".$rep_tel1."', '".$rep_tel2."', '".$rep_numeromobile."')";
		
        $req=$bdd->prepare($representant1);
        $req->execute();
        $sql1='Update t_eleve set idx_representantlegal=(SELECT max(id_representantlegal) from t_representantlegal) where id_codebarre='.$stu.'';
        $req1=$bdd->prepare($sql1);
        $req1->execute();
}
api_redirect( $previous );
unset($bd);