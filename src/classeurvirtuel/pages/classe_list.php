<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : JOUDAR Salaheddine
// Création   : 28.12.2016
// But    : Listing dynamique des classes
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

//Va chercher le tableau contenant les classes du profil
if(!empty($_SESSION['IdClasse']))
{
    $bd=new dbIfc();
    $tabmyclass=$bd->GetMyClass($_SESSION['IdClasse']);
    unset($bd);

}
else
{
    $tabmyclass =0;


    //Si pas connecté renvoi à la page de login
   //header("Location:classe_edt.php");
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <script type="text/javascript" src="../js/script_classe.js"></script>
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
                    <h1 class="page-header">Gestion des Classes</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


            <!-- /.row -->
            <div class="row"><p style="text-align: right"><a class="btn btn-default" href="classe_add.php" role="button">Ajouter une classe</a></p><br /></div>
            <div class="row">

                <form method="POST">
                    <div class="label_div">Rechercher la classe : </div>
                    <div class="input_container">
                        <input type="text" id="classe_id"  class="form-control" placeholder="Search..." onkeyup="autocomplet()">

                        <ul id="classe_list_id"></ul>
                     <div id ='moi'> </div>
                    </div>
                </form>

                <div class="col-xs-12">

                    <div id="timeline-1">
                        <!-- Liste des classes -->
                        <ul class="list-group">
                            <?php
                            IF (isset($_SESSION['idclasse'])) {

                                //Affichage des classes
                                // var $entry =0 ;

                            }
                            ?>
                        </ul>
                        <!-- Fin de : Liste des classes -->


                    </div>
                    <!-- PAGE CONTENT ENDS -->
                </div><!-- /.col -->
            </div><!-- /.row -->

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
