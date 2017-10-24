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


unset($bd);