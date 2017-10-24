<?php
session_start(  );
//*********************************************************************************
// Societe: CEPM
// Auteur : Sébastien MEtthez
// Date dernière modification   : 12.06.2017
// But    : System de messagerie
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************
/*
CREATE TABLE t_messages (
id_msg INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
msg_source TEXT NOT NULL,
msg_destination TEXT NOT NULL,
msg_titre TEXT NOT NULL,
msg_contenu TEXT NOT NULL,
reg_date TIMESTAMP
);

CREATE TABLE t_mlu (
id_mlu INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
idx_message INT NOT NULL,
idx_prof INT NOT NULL,
reg_date TIMESTAMP
);

CREATE TABLE `t_messages` (
  `id_msg` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `msg_source` text NOT NULL,
  `msg_destination` text NOT NULL,
  `msg_titre` text NOT NULL,
  `msg_contenu` text NOT NULL,
  `msg_lu` int NOT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

*/


//inclusion de la classe d'interaction avec la base de donnes

include '../include/bdd.php';
include_once( '../include/mysql.inc.php' );
include_once( '../api/api.inc.php' );

$previous = "javascript:history.go(-1)";

if ( isset( $_SERVER['HTTP_REFERER'] ) ) { $previous = $_SERVER['HTTP_REFERER']; } 

if ( empty( $_SESSION['user_id'] ) )
{
    header( "Location:login.php" );
}
$msgid = 0 ;
$newMSG = "" ;
$bd = new dbIfc( ) ;



		$requete3=$bdd->prepare("SELECT * FROM t_messages WHERE msg_destination='".urldecode( $_SESSION['user_name'] )."' AND msg_lu='0'");
		$requete3->execute();
		$msgs=$requete3->fetchAll();
		
		$nonlu =0;
		foreach ($msgs as $ta)
		{				
				print_r($ta);
				$nonlu++;
		}
		die( urldecode ( $nonlu ) );
	
									

?>