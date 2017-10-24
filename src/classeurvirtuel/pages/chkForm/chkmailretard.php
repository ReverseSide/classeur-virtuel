<?php
session_start();
/**
 * Created by PhpStorm.
 * User: drin
 * Date: 18.10.2016
 * Time: 6:10 PD
 */

//inclusion de la classe d'interaction avec la base de données
	include '../../include/bdd.php';
include_once('../../include/mysql.inc.php');
require('../../include/PHPMailer/PHPMailerAutoload.php');
// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

//Va cherhcer les informations de l'élève
$bd=new dbIfc();
//$tabStudent=$bd->GetStudent($_POST['stuBarcode']);
unset($bd);


//check var
// define variables and set to empty values
$conDate = $conCause = $conHeureDeb = $conHeureFin = $conNbPer = $conLieu = $conAbsence = $stuID = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	/*print($_POST["hiddenentreprise"]);
	
   print("<br>");
 print($_POST["hiddendate"]);
    print("<br>");
  print($_POST["hiddennom"]);
     print("<br>");
 print($_POST["hiddenid"]);
    print("<br>");*/
	$_POST["hiddennom"]= str_replace( " ", "|",$_POST["hiddennom"]);
	$_POST["hiddennom"]= str_replace( ",", " ",$_POST["hiddennom"]);
	$_POST["hiddennom"]= str_replace( "|", ",",$_POST["hiddennom"]);
		$id = explode(",", $_POST["hiddenid"]);
		$dateE=explode(",", $_POST["hiddendate"]);
		 $mails = explode(",", $_POST["hiddenmails"]);
		
				 $entreprises= explode(",", $_POST["hiddenentreprise"]);
		 
		 $noms =explode(",", $_POST["hiddennom"]);
		 
	//Code si case à cocher traiter est activée , rechercher l'id eleve et la date pour traiter le cas
	if (isset($_POST["ckbtraiter"])){
	
		 $i =0;
		 foreach ($id as $idex) {
			 
			 if ($idex!=""){
				
				$sql=" update t_tardive set tar_traite='1' where idx_eleve='".$idex."' and tar_date='".$dateE[$i++]."'";
			//	print($dateE[$i]);
				$traite=$bdd->prepare($sql);
				$traite->execute();
				}	
		}
	
	}
	// fin code si case à cocher traiter est activée , rechercher l'id eleve et la date pour traiter le cas////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	 
	
 $reste = explode("<br>", $_POST["hiddenreste"]);

 

  
  

    //check values
 /*   $conDate = test_input($_POST["inpconDate"]);
    $conCause = test_input($_POST["optradioCause"]);
    $conHeureDeb = test_input($_POST["inpconHeureDeb"]);
    $conHeureFin = test_input($_POST["inpconHeureFin"]);
    $conNbPer = test_input($_POST["inpconNbPer"]);
    $conLieu = test_input($_POST["inpconLieu"]);
    $conAbsence =isset($_POST["inpconAbsence"]) ? $_POST["inpconAbsence"]: array();
    $stuID = test_input($tabStudent[0]["id_eleve"]);

    $dtAndPerToDb = "";
    $dt = "";
    $dtstr = "";
    $statusChange = false;*/
 
 
 
   // if(null !== isset($dtstr) && $conCause == "0" || $conCause == "1"){
        $bd=new dbIfc();

    foreach ($id as $idex)
    {

        if ($idex!="")
        {

            $requete4=$bdd->prepare("SELECT ele_nom, ele_prenom, ele_mail, ent_mail	 FROM t_eleve, t_entreprise  WHERE id_eleve='".$idex."' AND id_entreprise=idx_entreprise");
            $requete4->execute();
            $adresses=$requete4->fetchAll();

            //Création du mail
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 3;

            //disable SMTP debugging.

            //Set PHPMailer to use SMTP.
            //$mail->isSMTP();
            //Set SMTP host name
            //$mail->Host = "mail.cepv.ch";
            //Set this to true if SMTP host requires authentication to send email
            //$mail->SMTPAuth = true;
            //Provide username and password
            //$mail->Username = "absences@cepv.ch";
            //$mail->Password = "b7Giull349Ma4qnu";
            //If SMTP requires TLS encryption then set it
            //$mail->SMTPSecure = "ssl";
            //Set TCP port to connect to
            //$mail->Port = 465;
            $mail->From = "absences@cepv.ch";
            $mail->FromName = "CEPV Convocation";

            if($adresses[0]['ele_mail']!="")
            {
                $elevemail=$adresses[0]['ele_mail'];
            }
            else {continue;}


            $mail->addAddress($elevemail);
            $mail->addCC('maurice.jaques@vd.ch');
            $mail->addCC('secretariat.cepv@vd.ch');
            if($adresses[0]['ent_mail']!="")
            {
                $mail->addCC($adresses[0]['ent_mail']);
            }

            $mail->isHTML(true);
            $mail->Subject = $_POST["txttitre"];

            $body = "";
            $nomprenom=$adresses[0]['ele_prenom']." ".$adresses[0]['ele_nom'];
            $body =" Concerne l'apprenti : " . $nomprenom ." ". $_POST["txtcorps"];

            $mail->Body = $body;

            if($mail->send())
            {
                $msg .= "et le mail a été correctement envoyé";
            }


        }
    }
}

        unset($bd);
    

   print("<br><br> Vous allez etre redirige dans quelques instants. <meta http-equiv='refresh' content='3; url=../management.php' />");
 

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}