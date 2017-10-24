<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Salaheddine JOUDAR
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
   if(isset($_GET['ajout'])){
       $sql="insert into a values ('123')";
       // $sql=" insert into a values('".$_GET['idclasse']."')";
       $change=$bdd->prepare($sql);
       $change->execute();

   }
       // ajout d'un nouveau representant

include_once('../include/mysql.inc.php');
//Va cherhcer les informations de l'élève
$bd=new dbIfc();



$alreadydone=false;
//Traitement des informations de l'élève



$requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
$requete->execute();
$classe=$requete->fetchAll();

$requete1=$bdd->prepare('SELECT * FROM t_professeur ORDER BY pro_nom ASC');
$requete1->execute();
$prof=$requete1->fetchAll();


$requete2=$bdd->prepare('SELECT * FROM t_salle ORDER BY sal_nom ASC');
$requete2->execute();
$salle=$requete2->fetchAll();

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
                    <h1 class="page-header">Créer un cours</h1>
                    <!-- Fin  de : Titre -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">




                        <!-- Informations de base -->
                        <table class="table table-striped">
                            <form method="POST" action="?ajout=oui>">
                                    <tr>
                                        <td style="font-weight: bold">Cours code:</td>
                                        <td><input type="text" name="CoursCode" value=''/> </td>
                                    </tr>

                                <tr>
                                    <td style="font-weight: bold">Cours libellé:</td>

                                    <td>

                                    <input type="text" name="CoursLib" value=''/>
                                    </td>
                                </tr>


                                <tr>
                                    <td style="font-weight: bold">Jour du cours:</td>
                                    <td>  <select class="dropdown form-control" name="ddlJoursCours">
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
                                    <td style="font-weight: bold">Heure début:</td>
                                    <td><input type="text" name="date" placeholder="hh:mm"/>

                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Alternance:</td>
                                    <td>
                                        <select class="dropdown form-control" name="ddlalterance">

                                            <option value="H">H</option>
                                            <option value="S.I">S.I</option>
                                            <option value="S.P">S.P</option>
                                            <option value="S1">S1</option>
                                            <option value="S1p">S1p</option>
                                            <option value="S2">S2</option>
                                            <option value="S2p">S2p</option>

                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Période:</td>
                                    <td><input type="text" name="periodecours" value=''/> </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Professeur:</td>
                                    <td>
                                        <select class="dropdown form-control" name="ddltypeformation">
                                            <option value=""></option>
                                            <?php for($a=0;$a<count($prof);$a++)echo'<option value="'.$prof[$a]['id_professeur'].'">'.$prof[$a]['pro_nom'].' ' .$prof[$a]['pro_prenom'].'</option>'; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Salle:</td>
                                    <td>
                                        <select class="dropdown form-control" name="ddlsalle">
                                            <option value=""></option>
                                            <?php for($a=0;$a<count($salle);$a++)echo'<option value="'.$salle[$a]['id_salle'].'">'.$salle[$a]['sal_nom'].'</option>'; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Classe:</td>
                                    <td>
                                        <select class="dropdown form-control" name="ddlclasse">
                                            <option value=""></option>
                                            <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr><td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter" float="right"></td></tr>
                            </table>
                        </div>
</form>


<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->                                

        <!-- Initialisation des datePickers -->


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
    <script>
        function validateTime(el)
        {
            var result;
            // first, check if input is fully correct
            if (el.value.match(/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/))
                result = "OK";
            // then, check if it is not wrong
            else if (el.value.match(/^([0-9]|0[0-9]|1[0-9]|2[0-3])?(:([0-5]|[0-5][0-9])?)?$/))
                result=""; // don't bother user with excess messages
            else
                result="Please, correct your input";
            document.getElementById("validationresult").innerHTML=result;
        }
    </script>
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
