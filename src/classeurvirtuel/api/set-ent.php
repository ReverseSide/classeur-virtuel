<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Sebastien Metthez
// Date dernière modification   : 10.06.2017
// Date de Création             : 10.06.2017
// But    : Route de modification d'élève
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


function print_array(&$a, $str = "")
{
    if ($str[0]) { echo "$str ="; }
    echo ' array( ';
    foreach ($a as $k => $v) {
        echo "[$k]".' => ';
        if (is_array($v)) print_array($v);
        else              echo "<strong>$a[$k]</strong> ";
    }   echo ')  ';
}

function return_array($array, $html = false, $level = 0) {
    $space = $html ? "&nbsp;" : " ";
    $newline = $html ? "<br />" : "\n";
    $spaces = "";
    for ($i = 1; $i <= 6; $i++)        $spaces .= $space;
    $tabs = $spaces;
    for ($i = 1; $i <= $level; $i++)   $tabs .= $spaces;
    $output = "Array" . $newline . $newline;
    foreach($array as $key => $value) {
        if (is_array($value)) {
            $level++;
            $value = return_array($value, $html, $level);
            $level--;
        }
        $output .= $tabs . "[" . $key . "] => " . $value . $newline;
    }
    return $output;
}

function set_student_redirect( $location )
{
	http_response_code( 302 );
	header("location:$location");
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if ( !isset( $_GET['stuid'] ) )
{   http_response_code( 400 );
    die( "Required parameters are: stu");
}
// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if ( !isset( $_GET['entID'] ) )
{   http_response_code( 400 );
    die( "Required parameters are: stu");
}

// Vérifie que l'utilisateur soit connecté. Si non, renvoyer une erreur 403 (Forbidden)
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
$stu =  $_GET['stuid'] ;
$rlgl =  $_GET['entID'] ;

$SETENT_DEBUG=0;

$repentre = array("id_entreprise","ent_canton", "ent_nom","ent_fax", "ent_rue", "ent_tel1", "ent_localite", "ent_tel2", "ent_npa", "ent_mobile1", "ent_mail", "ent_mobile2");
$previous = "javascript:history.go(-1)";

if(isset($_SERVER['HTTP_REFERER'])) { $previous = $_SERVER['HTTP_REFERER']; } 

if (isset($_POST['id_entreprise']) or isset($_POST['id_entreprise']))
{
	
		if (isset($_POST['ent_npa']))
		{	if (!is_numeric($_POST['ent_npa'])) {
				$_POST['ent_npa'] = 0 ;
			}
		} else 
			$_POST['ent_npa'] = 0;
		$postrray = return_array($_POST, true );
		$repentrerray = return_array($repentre, true );
		
		$tabStudent=$bd->GetStudent( $stu );
		$studentrray = return_array($tabStudent, true );
		$i=0;
		if ( $SETENT_DEBUG==1) {
			echo "<b>stu : $stu </b><br>";
			echo "<b>POST</b><br>";
			echo "$postrray";
			echo "<br><b>STUDENT DB : </b><br><br>";
			echo "<b>tabStudent : </b><br>";
			echo "$studentrray";
			print_r( $repentre ) ;
			echo "<br>------------------------------------------<br><br><br><br>";
		}
		if ($rlgl == 0 )
		{
			if ( $SETENT_DEBUG==1) echo "0rlgl0<br>";
			$entsql1="insert into t_entreprise (ent_nom, ent_rue, ent_npa, ent_localite, ent_tel1, ent_tel2, ent_mail) values ('', '', 0, '', '', '', '')";
			//$ent="INSERT INTO t_entreprise (ent_nom, ent_rue, ent_npa, ent_localite, ent_canton, ent_tel1, ent_tel2, ent_mail) values('".$_POST['ent_nom']."', '".$_POST['ent_rue']."', ".$_POST['ent_npa'].", '".$_POST['ent_localite']."', '".$_POST['ent_canton']."', '".$_POST['ent_tel1']."', '".$_POST['ent_tel2']."', '".$_POST['ent_mail']."')";
			$req=$bdd->prepare($entsql1);
			$req->execute();
			if ( $SETENT_DEBUG==1) echo "111:$rlgl;<br>";
			$rlgl = $bdd->lastInsertId();
			if ( $SETENT_DEBUG==1) {
				echo "222:$rlgl;<br>";
				echo "$entsql1<br>";
				echo "0rlgl0 - insert<br>";
			}
			
			// $sql1='Update t_eleve set idx_entreprise=(SELECT max(id_entreprise) from t_entreprise) where id_codebarre='.$stu.'';
			$sql1='Update t_eleve set idx_entreprise='.$rlgl.' where id_codebarre='.$stu.'';
			if ( $SETENT_DEBUG==1) echo "SQL1::::<br>$sql1<br>";
			$req1=$bdd->prepare($sql1);
			$req1->execute();
		}							
		foreach($_POST as $key => $val) {
			//if ( $SETENT_DEBUG==1) echo "foor each <br>";
			if(array_key_exists( $key, $tabStudent[0] ))
			{
				//if ( $SETENT_DEBUG==1) echo "---------if(array_key_exists(<br>";
				if ($tabStudent[0][$key] != $val) {
						//if ( $SETENT_DEBUG==1) echo "-----------------if (tabStudent[0][$key] != $val)<br>";
						$i++;
						if(array_key_exists( $key, $repentre ))
						{
								if ( $SETENT_DEBUG==1) {
									echo "$rlgl rlgl $rlgl - Update t_entreprise <br>";
								}
								$sql='Update t_entreprise set '.$key.'="'.$val.'" where id_entreprise='.$rlgl.'';
								$requete3=$bdd->prepare($sql);
								$requete3->execute();
								if ( $SETENT_DEBUG==1) echo "SQL1::::<br>$sql<br>";
						} else if(array_key_exists( $key, $tabStudent[0] ))
						{
								//if ( $SETENT_DEBUG==1) echo "- $rlgl rlgl $rlgl - Update set  '.$key.'='.$val.' t_eleve <br>";
								$sql='Update t_entreprise set  '.$key.'="'.$val.'" where id_entreprise='.$rlgl.'';
								if ( $SETENT_DEBUG==1) echo "SQL1::::<br>$sql<br>";
								$requete4=$bdd->prepare( $sql );
								$requete4->execute( );
						}
				}				
			} else { echo " $key is not in tabStudent <br>" ; }
		}	
		if ( $SETENT_DEBUG==0)  set_student_redirect( $previous );
	
}
unset($bd);