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

if ( isset( $_GET['srcID'] ) ) 
	if ( is_numeric( $_GET['srcID'] ) )
	{

		$msgid = $_GET['srcID']  ;	
		$requete3 = $bdd->prepare( "SELECT * FROM t_messages WHERE id_msg='".$msgid."' " );
		$requete3->execute(  );
		$msgs = $requete3->fetchAll(  ) ;
		
		foreach ( $msgs as $ta )
		{										
			$sql = "SELECT pro_prenom, pro_nom from t_professeur WHERE  id_professeur='".$ta['msg_source']."'";
			$req = $bdd->prepare( $sql ) ;
			$req->execute(  );
			$list = $req->fetchAll(  ) ;
			
			foreach ($list as $rs)
			{
				//echo "".$rs['prenom']." ".$rs['nom']."";
				//$proiName["".$rs['id_professeur'].""] = "".$rs['pro_prenom']." ".$rs['pro_nom']."" ;
				die( urldecode ( "".$rs['pro_prenom']." ".$rs['pro_nom'].""   ) ) ;
			}

			
		}
	}
	
if ( isset( $_GET['msgID'] ) ) 
	if ( is_numeric( $_GET['msgID'] ) )
	{
		$msgid = $_GET['msgID']  ;	
		$requete3 = $bdd->prepare("SELECT * FROM t_messages WHERE id_msg='".$msgid."' ") ;
		$requete3->execute(  );
		$msgs = $requete3->fetchAll(  ) ;
		
		foreach ($msgs as $ta)
		{										
			if ( $ta['msg_source'] != urldecode ( $_SESSION['user_name'] ) )
			{
				$re = $bdd->prepare( "UPDATE t_messages SET msg_lu=1 WHERE id_msg='".$msgid."' " ) ;
				$re->execute(  );
			}
			die( urldecode ( $ta['msg_contenu'] ) );
		}
	}

?>