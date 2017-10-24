<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Sebastien Metthez
// Date dernière modification   : 01.06.2017
// Date de Création             : 01.06.2017
// But    : Route de modification d'élève
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

//$stu =  $_GET['stu'] ;
//echo "<b>stu : $stu </b><br>";
//$tabStudent=$bd->GetStudent( $stu );
//echo json_encode($tabStudent[0]);
/*
	echo "<b>tabStudent : </b><br>";
	//print_r($tabStudent);
	echo return_array($tabStudent, true );
	//$bd->UpdateComment($_POST['id'], $_POST['commentaire']);
	echo "\n<br>\n<br>\n";
	echo "<b>JSNStudent : </b><br>";
*/

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
$rlgl =  $_GET['rlgl'] ;

$SETSTUDENT_DEBUG=0;
$replegal = array("rep_politesse", "rep_prenom", "rep_nom", "rep_Rue", "rep_npa", "rep_localite", "rep_tel1", "rep_tel2", "rep_numeromobile");
$previous = "javascript:history.go(-1)";

if(isset($_SERVER['HTTP_REFERER'])) { $previous = $_SERVER['HTTP_REFERER']; } 

if (isset($_POST['ele_politesse']) or isset($_POST['rep_politesse']))
{	//echo return_array($_POST, true );
    $stu =  $_GET['stuid'] ;
    /*echo "<br>";echo "<br><b>stu : $stu </b><br><br>";*/
    //set_student_redirect( $previous );
	if ($SETSTUDENT_DEBUG)
	{ 	
		$postrray = return_array($_POST, true );
		$stu =  $_GET['stuid'] ;
		echo "<b>stu : $stu </b><br>";
		echo "<b>POST</b><br>";
		echo "$postrray";
		echo "<br><b>STUDENT DB : </b><br><br>";
		$tabStudent=$bd->GetStudent( $stu );
		//echo json_encode($tabStudent[0]);
		$studentrray = return_array($tabStudent, true );
		echo "<b>tabStudent : </b><br>";
		echo "$studentrray";
		$i=0;
		foreach($_POST as $key => $val) {
			//echo "<b> $i : K:$key;  V:$val;</b></br>";
			if(array_key_exists( $key, $tabStudent[0] ))
			{
				if ($tabStudent[0][$key] != $val) {
						echo "<b> $i : K:$key;  V:$val != ".$tabStudent[0][$key].":V</b></br>";
						$i++;
						$sql='Update t_eleve set  '.$key.'="'.$val.'" where id_codebarre='.$stu.'';
						$requete4=$bdd->prepare($sql);
						$requete4->execute();
						//$rupture=$requete4->fetchAll();
				}
				//echo "<b> ! $key ! </b><br>";				
			}else echo "<b>merde</b><br>";
		}
	} else { 
		$postrray = return_array($_POST, true );
		$replegalrray = return_array($replegal, true );
		$stu =  $_GET['stuid'] ;
		$tabStudent=$bd->GetStudent( $stu );
		$studentrray = return_array($tabStudent, true );
		$i=0;
		/*
		echo "<b>stu : $stu </b><br>";
		echo "<b>POST</b><br>";
		echo "$postrray";
		echo "<br><b>STUDENT DB : </b><br><br>";
		echo "<b>tabStudent : </b><br>";
		echo "$studentrray";
		print_r( $replegal ) ;
		*/
		foreach($_POST as $key => $val) {
			if(array_key_exists( $key, $tabStudent[0] ))
			{
				if ($tabStudent[0][$key] != $val) {
						$i++;
						if(array_key_exists( $key, $replegal ))
						{
								if ($rlgl == 0 )
								{
									echo "0rlgl0<br>";
									$representant1="insert into t_representantlegal (rep_nom, rep_prenom, rep_politesse, rep_Rue, rep_npa, rep_localite, rep_tel1, rep_tel2, rep_numeromobile) values ('', '', '', '', '', '', '', '', '')";
									$req=$bdd->prepare($representant1);
									$req->execute();
									echo "0rlgl0 - insert<br>";
									$sql1='Update t_eleve set idx_representantlegal=(SELECT max(id_representantlegal) from t_representantlegal) where id_codebarre='.$stu.'';
									$req1=$bdd->prepare($sql1);
									$req1->execute();
									echo "0rlgl0 - update<br>";
									$sql='Update t_representantlegal set  '.$key.'="'.$val.'" where id_codebarre='.$stu.'';
									$requete3=$bdd->prepare($sql);
									$requete3->execute();
									echo "0rlgl0 - Update t_representantlegal <br>";
								}else{
									echo "$rlgl rlgl $rlgl - Update t_representantlegal <br>";
									$sql='Update t_representantlegal set  '.$key.'="'.$val.'" where id_codebarre='.$stu.'';
									$requete3=$bdd->prepare($sql);
									$requete3->execute();
								}
								
						} else if(array_key_exists( $key, $tabStudent[0] ))
						{
								echo "- $rlgl rlgl $rlgl - Update set  '.$key.'='.$val.' t_eleve <br>";
								$sql='Update t_eleve set  '.$key.'="'.$val.'" where id_codebarre='.$stu.'';
								$requete4=$bdd->prepare($sql);
								$requete4->execute();
						}
				}				
			} else { echo " $key is not in tabStudent <br>" ; }
		}	
		set_student_redirect( $previous );
	}
}






unset($bd);