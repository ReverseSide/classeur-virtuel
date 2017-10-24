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

$GETENT_DEBUG = 0; 

$whattoget = api_get_ordie( 'whattoget' );

function api_safe( $in )
{
	return htmlentities( $in ) ;
}
if ( empty( $_SESSION['user_id'] ) )
{   http_response_code( 403 );
    die( "Forbidden" );
}

if ( empty( $_SESSION['user_id'] ) )
{   http_response_code( 403 );
    die( "Forbidden" );
}

include '../include/bdd.php';
include_once('../include/mysql.inc.php');
$bd = new dbIfc();



if ($whattoget == "ent" )
{
	$entID = api_get( 'entID' );
	//$maiID = api_get( 'maiID' );
	if ($GETENT_DEBUG)	echo "<b> entID : </b> $entID<br><b> maiID : </b> $maiID <br>";
		$sqlm="SELECT * FROM t_entreprise";
		$rq=$bdd->prepare($sqlm);
		$rq->execute();
		$maitre=$rq->fetchAll();
		$ret = "";
		$iret = 0;
		foreach($maitre as $emtry)
		{
			
			//if ($GETENT_DEBUG)			echo "::".$entry['idx_entreprise']." > ".$entry['mai_nom']." ".$entry['mai_prenom']."<br>";
			if ($entID == 0)
			{
				if ($emtry['id_entreprise'] != 0)
				{
				$entry = array(); 
				$entry['value'] 			= api_safe( $emtry['id_entreprise'] );
				$entry['text'] 				= api_safe( $emtry['ent_nom'] );
				
				if ($iret >= 1)		$ret .= ", \n". str_replace(":null,", ":\"\",", json_encode($entry) ) ;
				else				$ret = json_encode($entry) ;
				$iret++;
				}
				
			} 
			else if ($entID > 0)
			{}
			/*if ($emtry['id_maitredapprentissage'] == $maiID )
			{	$entry = array(); 
				$entry['idx_entreprise'] 			= $entID;
				$entry['id_maitredapprentissage'] 	= api_safe( $emtry['id_maitredapprentissage'] );
				$entry['mai_nom'] 					= api_safe( $emtry['mai_nom'] ) ;
				$entry['mai_prenom'] 				= api_safe( $emtry['mai_prenom'] ) ;
				$entry['mai_tel1'] 					= api_safe( $emtry['mai_tel1'] ) ;
				$entry['mai_tel2'] 					= api_safe( $emtry['mai_tel2'] ) ;
				$entry['mai_mobile'] 				= api_safe( $emtry['mai_mobile'] ) ;
				
				if ($iret >= 1)		$ret .= ", \n". str_replace(":null,", ":\"\",", json_encode($entry) ) ;
				else				$ret = json_encode($entry) ;
				$iret++;
				
				break;
			}*/
		}
		echo str_replace(":null,", ":\"\",", $ret ) ;
}else if ($whattoget == "mai" )
{
	$entID = api_get( 'entID' );
	$maiID = api_get_ordie( 'maiID' );
	if ($GETENT_DEBUG)	echo "<b> entID : </b> $entID<br><b> maiID : </b> $maiID <br>";
		$sqlm="SELECT * FROM t_maitredapprentissage";
		$rq=$bdd->prepare($sqlm);
		$rq->execute();
		$maitre=$rq->fetchAll();
		$ret = "";
		$iret = 0;
		foreach($maitre as $emtry)
		{
			//if ($GETENT_DEBUG)			echo "::".$entry['idx_entreprise']." > ".$entry['mai_nom']." ".$entry['mai_prenom']."<br>";
			if ($emtry['id_maitredapprentissage'] == $maiID )
			{	$entry = array(); 
				$entry['idx_entreprise'] 			= $entID;
				$entry['id_maitredapprentissage'] 	= api_safe( $emtry['id_maitredapprentissage'] );
				$entry['mai_nom'] 					= api_safe( $emtry['mai_nom'] ) ;
				$entry['mai_prenom'] 				= api_safe( $emtry['mai_prenom'] ) ;
				$entry['mai_tel1'] 					= api_safe( $emtry['mai_tel1'] ) ;
				$entry['mai_tel2'] 					= api_safe( $emtry['mai_tel2'] ) ;
				$entry['mai_mobile'] 				= api_safe( $emtry['mai_mobile'] ) ;
				
				if ($iret >= 1)		$ret .= ", \n". str_replace(":null,", ":\"\",", json_encode($entry) ) ;
				else				$ret = json_encode($entry) ;
				$iret++;
				break;
			}
		}
		echo str_replace(":null,", ":\"\",", $ret ) ;
		
}else if ($whattoget == "maient" )
{
	$entID = api_get_ordie( 'entID' );
	$maiID = api_get( 'maiID' );
	if ($GETENT_DEBUG)	echo "<b> entID : </b> $entID<br><b> maiID : </b> $maiID <br>";
		$sqlm="SELECT * FROM t_maitredapprentissage";
		$rq=$bdd->prepare($sqlm);
		$rq->execute();
		$maitre=$rq->fetchAll();
		$ret = "";
		$iret = 0;
		foreach($maitre as $emtry)
		{
			//if ($GETENT_DEBUG)			echo "::".$entry['idx_entreprise']." > ".$entry['mai_nom']." ".$entry['mai_prenom']."<br>";
			if ($emtry['idx_entreprise'] == $entID )
			{	$entry = array(); 
				$entry['idx_entreprise'] 			= $entID;
				$entry['id_maitredapprentissage'] 	= api_safe( $emtry['id_maitredapprentissage'] ) ;
				$entry['mai_nom'] 					= api_safe( $emtry['mai_nom'] ) ;
				$entry['mai_prenom'] 				= api_safe( $emtry['mai_prenom'] ) ;
				$entry['mai_tel1'] 					= api_safe( $emtry['mai_tel1'] ) ;
				$entry['mai_tel2'] 					= api_safe( $emtry['mai_tel2'] ) ;
				$entry['mai_mobile'] 				= api_safe( $emtry['mai_mobile'] ) ;
				
				if ($iret >= 1)		$ret .= ", \n". str_replace(":null,", ":\"\",", json_encode($entry) ) ;
				else				$ret .= str_replace(":null,", ":\"\",", json_encode($entry) ) ;
				$iret++;
			}
		}
		echo $ret;
}else if ($whattoget == "entada" )
{
	
}else {}
/////////////////////////////
//        CODE MORT      ///
/////////////////////////////
 // where ent_nom='".$ent_nom."' LIMIT 1)";
//			echo "::".$entry['idx_entreprise']." > ".$entry['mai_nom']." ".$entry['mai_prenom']."<br>";
/*foreach($entry as $emtry){	echo "::::$emtry<br>"; }*/
/*$sqlm="SELECT * FROM t_maitredapprentissage ORDER BY mai_nom ASC";
$requete2=$bdd->prepare($sqlm);
$requete2->execute();
$maitre=$requete2->fetchAll();*/
?>