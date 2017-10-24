<?php
session_start();
/**
 * Created by PhpStorm.
 * User: drin
 * Date: 18.10.2016
 * Time: 6:10 PD
 * MALADIE
 *
 */

//inclusion de la classe d'interaction avec la base de données
require('../../include/PHPMailer/PHPMailerAutoload.php');
// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

// define variables and set to empty values
$AbsenceMalNom = $AbsenceMalDate = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //check values
    $AbsenceMalDate = test_input($_POST["hiddendatec"]);
    $AbsenceMalNom = $_POST["hiddennomc"];
    $iDEleve =  $_POST["hiddenidc"];
    $EmailEleve = $_POST["hiddenmailsc"];
    $EmailEntreprise = $_POST["hiddenentreprisec"];
    //$conAbsence = isset($_POST["inpconAbsence"]) ? $_POST["inpconAbsence"] : array();
}


$ArrayAbsenceMalNom = explode(",", rtrim($AbsenceMalNom, ","));
$ArrayMalDate = explode(",", rtrim($AbsenceMalDate, ","));
$ArrayiDEleve = explode(",", rtrim($iDEleve, ","));
$ArrayEmailEleve = explode(",", rtrim($EmailEleve, ","));
$ArrayEmailEntreprise = explode(",", rtrim($EmailEntreprise, ","));


foreach($ArrayAbsenceMalNom as $key=>$Nom) {

    $mail = new PHPMailer;
    try {
        $mail->CharSet = 'UTF-8';

        //disable SMTP debugging.
        //$mail->SMTPDebug = 3;
        //Set PHPMailer to use SMTP.
        $mail->isSMTP();
        //Set SMTP host name
        $mail->Host = "mail.infomaniak.com";
        //Set this to true if SMTP host requires authentication to send email
        $mail->SMTPAuth = true;
        //Provide username and password
        $mail->Username = "info@cepm.ch";
        $mail->Password = "AikYlFNC4DK-";
        //If SMTP requires TLS encryption then set it
        //$mail->SMTPSecure = "ssl";
        //Set TCP port to connect to
        //$mail->Port = 465;

        $mail->From = "info@cepm.ch";
        $mail->FromName = "CEPM";

        $mail->addAddress($ArrayEmailEntreprise[$key]);
        $mail->AddCC('info@cepm.ch');

        if (!empty($_POST["ckbcopieEleve"])) {
            $mail->AddCC($ArrayEmailEleve[$key]);
        }

        $mail->isHTML(true);
        $mail->Subject = "Absence aux cours professionnels - Départ santé";

        $body = $_POST["txtcorpsc"];
        $body = str_replace('[NOM-PRENOM]'," ".$Nom,$body);
        $body = str_replace('[ABSENCES]'," ".date("d.m.Y", strtotime($ArrayMalDate[$key])),$body);

        $mail->Body = $body;

        $mail->send();
        $msg .= "<br>";
        $msg .= "Le mail a été correctement envoyé à ";
        $msg .= $ArrayEmailEntreprise[$key];
        if (!empty($_POST["ckbcopieEleve"])) {
            $msg .= "<br>";
            $msg .= "Et en copie l'élève à l'adresse ";
            $msg .= $ArrayEmailEleve[$key];
        }
        $msg .= "<br>";
    } catch (Exception $e) {
        $msg .= 'Message could not be sent.';
        $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

print("{$msg}<br><br>");
//<meta http-equiv='refresh' content='3; url=../management.php' />
//}

function test_input($data) {
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data);

return $data;
}