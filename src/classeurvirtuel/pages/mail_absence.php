<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Alessandro Sipala
// Date dernière modification   : 02.05.2016
// But    : Page administrateur permet la modification et l'ajout des informations des élèves
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

//inclusion de la classe d'interaction avec la base de données

include '../include/bdd.php';

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}
    // vérifie qu' un formulaire d'ajout a été complété

if(isset($_GET['excuset'])=='Oui'){
$sql=" update t_absence set abs_excuse ='Oui' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
$change=$bdd->prepare($sql);
$change->execute();
}
if(isset($_GET['traitet'])=='Oui'){
$sql=" update t_absence set Traite='1' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
$change=$bdd->prepare($sql);
$change->execute();
}
	if(isset($_GET['excuse'])=='Oui'){
		 $sql=" update t_absence set abs_excuse='Oui' where id_absence='".$_GET['idabs']."'";
            $change=$bdd->prepare($sql);
            $change->execute();
	}
	if(isset($_GET['Traite'])=='Oui'){
		 $sql=" update t_absence set Traite='1' where id_absence='".$_GET['idabs']."'";
            $change=$bdd->prepare($sql);
            $change->execute();
	}



include_once('../include/mysql.inc.php');
//Va cherhcer les informations de l'élève
$bd=new dbIfc();
//$tabStudent=$bd->GetStudent($_GET['stu']);
unset($bd);


$alreadydone=false;
//Traitement des informations de l'élève



$requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
$requete->execute();
$classe=$requete->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CEPM Classeur Virtuel v2.0</title>

    <!-- Bootstrap Core CSS -->

    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="../dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../bower_components/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300" />

    <!-- DatePicker -->
    <link href="../bower_components/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">




    <!-- inline styles related to this page -->

    <!-- ace settings handler -->


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <?php include("../include/menu.php"); ?>

        <div id="page-wrapper">
        
         
							<div class="" role="document">
                                <div class="modal-content" style="border-radius: 0;">
                                    <div class="modal-header" style="background: #d43f3a;color: white;">
                                       
                                        <h4 class="modal-title" id="myModalLabel">Absence à traiter: Mailing</h4>
                                    </div>
									<br>
									<input type="submit" class="btn btn-primary" value="Envoyer" name="frmConSubmit" />
									<br>
									<br>
									 <label for="copiemail">Formulaire d'envoi :</label>
									<div class="row">
                                                <div class="col-xs-4">
                                                    <div class="form-group">
                                                        <label for="copiemail">Titre :</label>
                                                        <input id="txttitre" name="copiemail"  type="email" class="form-control">
                                                    </div>
                                                </div>
											</div>
                                    <div class="col-xs-4">
                                        <form name="formulaire" id="formulaire" action="index.html" method="post">
						<label for="texte" >Corps du message : </label>
					<textarea id="texte" name="texte" rows="25" ></textarea>
					<br /><br />
						</form>

                                            
                                            
                                            </div>
											
											<div class="row">
                                                <div class="col-xs-4">
                                                    <div class="absencedest">
                                                        <label for="copiemail">Déstinataires :</label>
                                                        <input type="text" name="bookId" id="bookId" value=""/>
                                                    </div>
                                                </div>
											</div>
<div class="row">
                                                <div class="col-xs-4">
                                                    <div class="form-group">
                                                   <label for="cbox2">
 Traiter les cas
</label>     <input type='checkbox' name='ckbabsence' value='". $rs['id_eleve'] .$rs['abs_date']."'>
                                                       <input type="submit" class="btn btn-danger" value="Envoyer" name="frmConSubmit" />
                                                    </div>
                                                </div>
											</div>

                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group text-right">
                                                        
                                                     
                                                        <input type="hidden" value='' name="stuBarcode" id="stuBarcode" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                       
                        <!-- fin Modal add stage -->
						 </div>
                        </div>
