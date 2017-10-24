<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Salaheddine JOUDAR
// Date dernière modification   : 03.01.2017
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
   if(isset($_GET['ajout'])=="oui"){
	   
	   
	    $sql1="select * from t_typeformation where id_typeformation=".$_POST['ddltypeformation']."";
       $req1=$bdd->prepare($sql1);
        $req1->execute();
        $depart=$req1->fetchAll();
		if(isset($_POST['ddlJoursCours2'])=="-"){
	   
       $sql="INSERT INTO `t_classe` ( `cla_nom`, `cla_niveau`, `cla_type`, `cla_jourdecours`, `cla_joursSport`, `idx_typeformation`, `idx_departement`) VALUES
					( '".$_POST['NomClasse']."', '".$_POST['ddlNiveau']."', '".$_POST['ddltypeClasse']."', '".$_POST['ddlJoursCours']."', '".$_POST['ddlJoursSport']."', ".$_POST['ddltypeformation'].",".$depart[0]['idx_departement'].")";
   
		}
		else
		{
			
			
       $sql="INSERT INTO `t_classe` ( `cla_nom`, `cla_niveau`, `cla_type`, `cla_jourdecours`, `cla_joursSport`, `idx_typeformation`, `idx_departement`) VALUES
					( '".$_POST['NomClasse']."', '".$_POST['ddlNiveau']."', '".$_POST['ddltypeClasse']."', '".$_POST['ddlJoursCours']."-".$_POST['ddlJoursCours2']."', '".$_POST['ddlJoursSport']."', ".$_POST['ddltypeformation'].",".$depart[0]['idx_departement'].")";
       //$sql="insert into a values ('12321')";
		}
       // $sql=" insert into a values('".$_GET['idclasse']."')";
       $change=$bdd->prepare($sql);
       $change->execute();
   }

   if(isset($_GET['eleve'])){

    }






include_once('../include/mysql.inc.php');
//Va cherhcer les informations de l'élève


$alreadydone=false;
//Traitement des informations de l'élève


$requete=$bdd->prepare('SELECT * FROM t_typeformation ORDER BY typ_nom ASC');
$requete->execute();
$typeformation=$requete->fetchAll();



$requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
$requete->execute();
$classe=$requete->fetchAll();

$requete1=$bdd->prepare('SELECT * FROM t_entreprise ORDER BY ent_nom ASC');
$requete1->execute();
$Allentreprise=$requete1->fetchAll();


$requete3=$bdd->prepare('SELECT * FROM t_representantlegal ORDER BY rep_nom ASC');
$requete3->execute();
$representant=$requete3->fetchAll();
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
            <div class="row">
                <div class="col-lg-12">
                    <!-- Titre -->
                    <h1 class="page-header">Créer une classe</h1>
                    <!-- Fin  de : Titre -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">



                        <!-- Informations de base -->
                        <table class="table table-striped">
                            <form method="POST" action="?ajout=oui">
                                    <tr>
                                        <td style="font-weight: bold">Nom Classe:</td>
                                        <td><input type="text" name="NomClasse" value=''/> </td>
                                    </tr>

                                <tr>
                                    <td style="font-weight: bold">Niveau:</td>
                                    <td><select class="dropdown form-control" name="ddlNiveau">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>

                                        </select> </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Type:</td>
                                    <td><select class="dropdown form-control" name="ddltypeClasse">
                                            <option value="1ère année">1ère année</option>
                                            <option value="Terminales">Terminales</option>
                                            <option value="Non-terminales">Non-terminales</option>


                                        </select> </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold" width="170">Jour Cours 1:</td>
                                    <td>  <select class="dropdown form-control" name="ddlJoursCours">
                                            <option value="Lundi">Lundi</option>
                                            <option value="Mardi">Mardi</option>
                                            <option value="Mercredi">Mercredi</option>
                                            <option value="Jeudi">Jeudi</option>
                                            <option value="Vendredi">Vendredi</option>
                                            <option value="Samedi">Samedi</option>
                                           <option value="Dimanche">Dimanche</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold" width="170">Jour Cours 2:</td>
                                    <td>  <select class="dropdown form-control" name="ddlJoursCours2">
                                            <option value="-">-</option>
                                            <option value="Lundi">Lundi</option>
                                            <option value="Mardi">Mardi</option>
                                            <option value="Mercredi">Mercredi</option>
                                            <option value="Jeudi">Jeudi</option>
                                            <option value="Vendredi">Vendredi</option>
                                            <option value="Samedi">Samedi</option>
                                            <option value="Dimanche">Dimanche</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Jour Sport:</td>
                                    <td>  <select class="dropdown form-control" name="ddlJoursSport">
                                            <option value="Lundi">Lundi</option>
                                            <option value="Mardi">Mardi</option>
                                            <option value="Mercredi">Mercredi</option>
                                            <option value="Jeudi">Jeudi</option>
                                            <option value="Vendredi">Vendredi</option>
                                            <option value="Samedi">Samedi</option>
                                            <option value="Dimanche">Dimanche</option>
                                        </select> </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Type Formation:</td>
                                    <td>

                                        <select class="dropdown form-control" name="ddltypeformation">
                                            <option value=""></option>
                                            <?php for($a=0;$a<count($typeformation);$a++)echo'<option value="'.$typeformation[$a]['id_typeformation'].'">'.$typeformation[$a]['typ_nom'].'</option>'; ?>
                                        </select>
                                    </td>
                                </tr>


<tr><td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter" float="right"></td></tr>

                            </form>

                            </table>
                        </div>


                    </div>





<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->                                

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
        <!-- Code JS Affichage des tableaux Absences - Arrivées tardives - Mises à la porte -->

        <!-- Initialisation des datePickers -->
        <script>

            function RefreshDatePickers()
            {
                var datePickers = jQuery(".date-picker");
                datePickers.datepicker({
                    format: "yyyy-mm-dd",
                    language: "fr",
                    calendarWeeks: true,
                    todayHighlight: true
                });
            }
            document.addEventListener("DOMContentLoaded", function() {
                RefreshDatePickers();
            });

        </script>

        <!-- CSS Tableau en accordéon (Entreprises - Maître d'apprentissage  - Représentant légal) -->
        <style>
            /* Keyframe pour faire tourner les spinners sur cette page */
            @keyframes spin
            {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            button.accordion {
                background-color: #eee;
                color: #444;
                cursor: pointer;
                padding: 18px;
                width: 100%;
                text-align: left;
                border: none;
                outline: none;
                transition: 0.4s;
            }

            /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
            button.accordion.active, button.accordion:hover {
                background-color: #ddd;
            }

            /* Style the accordion panel. Note: hidden by default */
            div.panel {
                padding: 0 18px;
                background-color: white;
                max-height: 0;
                overflow: hidden;
                transition: 0.6s ease-in-out;
                opacity: 0;
            }

            div.panel.show {
                opacity: 1;
                max-height: 500px; /* Whatever you like, as long as its more than the height of the content (on all screen sizes) */
            }

            button.accordion:after {
                content: '\02795'; /* Unicode character for "plus" sign (+) */
                font-size: 13px;
                color: #777;
                float: right;
                margin-left: 5px;
            }

            button.accordion.active:after {
                content: "\2796"; /* Unicode character for "minus" sign (-) */
            }


        </style>


        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="../bower_components/raphael/raphael-min.js"></script>
    <script src="../bower_components/morrisjs/morris.min.js"></script>
    <script src="../js/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <!-- DatePicker + locale -->
    <script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.fr-CH.min.js"></script>

    <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js" async></script>

</body>

</html>
