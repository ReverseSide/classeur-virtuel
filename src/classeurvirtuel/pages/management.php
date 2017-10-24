<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Création   : 23.05.2016
// But    : Page de choix de ses classes pour aller à sa feuille de présence
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

//Va chercher le tableau contenant les classes du profil
if(!empty($_SESSION['user_id']))
{
    $bd=new dbIfc();
    $tabmyclass=$bd->GetMyClasses($_SESSION['user_id']);
    unset($bd);

}
else
{
    //Si pas connecté renvoi à la page de login
    header("Location:login.php");
}


?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CEPM Classeur Virtuel v2.1</title>

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
                    <h1 class="page-header">Mes classes</h1>

                        <div class="pull-right">
                            <tr>
                                <th></th>
                                <div style='min-width:165px !important; float:right;' ><span style='padding: 6px 8px;' class='badge'><th>Jours de cours</th></span></div>
                                <div style='min-width:250px !important; float:right;' ><span style='padding: 6px 8px;background-color:#337ab7;' class='label-info label badge'><th>Professeur principal</th></span></div>
                            </tr>
                        </div>
                    <br> <br>
                </div>
                <!-- /.col-lg-12 -->

                <div class="col-xs-12">

                    <div id="timeline-1">
                        <!-- Liste des classes -->
                        <ul class="list-group">
                            <?php
                            //Affichage des classes
                            foreach($tabmyclass as $entry)
                            {

                                $nom=$entry['cla_nom'];
                                $jour=$entry['cla_jourdecours'];
                                $id=$entry['id_classe'];
                                $profprincipal=$entry['pro_prenom']. " ".$entry['pro_nom'];
                                echo "<tr><td><a href='timeclock.php?idclasse=$id' class='list-group-item'></td>
                                <div style='min-width:150px !important; float:right;' ><span style='padding: 6px 8px;' class='badge'><td>$jour</td></span></div>
                                <div style='min-width:250px !important; float:right;' ><span style='background-color:#337ab7; padding: 6px 8px;' class='label-info label badge'><td>$profprincipal</td></span></div>
                                $nom
                            </a></tr>";
                            }
                            ?>
                        </ul>
                        <!-- Fin de : Liste des classes -->

                    </div>
                    <!-- PAGE CONTENT ENDS -->
                </div><!-- /.col -->

            </div><!-- /.row -->

            <p style="text-align: right"><a class="btn btn-primary" href="handleclasses.php" role="button">Gérer mes classes</a></p>
            <!-- /.row -->
        </div>
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

</body>

</html>
