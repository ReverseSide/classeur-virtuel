<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Date dernière modification   : 02.05.2016
// But    : Page de gestion de ses classes
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

include '../include/bdd.php';
$bd=new dbIfc();
//Check si connecté
if(!empty($_SESSION['user_id']) )
{
    if(!empty($_GET['idclasse']))
    {
        $_SESSION['class']=$_GET['idclasse'];
    }
}
else
{
    header("Location:login.php");
}



$requete3=$bdd->prepare("SELECT * FROM t_departement order by id_departement");
$requete3->execute();
$departement=$requete3->fetchAll();

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
<form ACTION="dashboard.php?dpt= <?php if(isset($_GET['ddldpt'])){ echo $_GET['ddldpt'] ;}?>">


        <div class="row">
            <div class="col-lg-5 col-lg-12">
                <!-- small box -->
                <div class="small-box bg-aqua">


                    <!-- <a class="small-box-footer" href="/EduSec/index.php?r=student%2Fstu-master%2Findex" target="_blank">Plus de détails <i class="fa fa-arrow-circle-right"></i></a>       -->                     </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->

                    <div class="inner">

                        <br><br><br> <h2>Choix du département</h2>
                        <select class="dropdown form-control" name="ddldpt">

                            <?php for($a=0;$a<count($departement);$a++)echo'<option value="'.$departement[$a]['id_departement'].'">'.$departement[$a]['dep_nom'].'</option>'; ?>
                        </select>
                        <br>

                    </div>
                    <div class="icon">
                        <i class="ion ion-person" title="salah"></i>
                    </div>
                <button class="btn btn-default" type="submit" method="POST">Entrer</button>


        </div>
    </div>





    </div>

</form>




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

    <script>
        // Permet de fixer le header du tableau lorsque l'utilisateur scroll
        var element = $(".keep-visible");
        element.data("original-offset", element.offset().top);
        $(window).scroll(function()
        {
            var diff = element.data("original-offset") - $(window).scrollTop();
            if (diff < 0)
            {
                element.css("top", Math.round(diff * -1) + "px");
            }
            else
            {
                element.css("top", "0");
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Initialise les tooltips s'il y en a
            jQuery('[data-toggle="tooltip"]').tooltip()

            // Bloque le clic sur les cases grisées
            jQuery('.dont-click').click(function(evt) {
                evt.stopPropagation();
                evt.preventDefault();
            });
        });
    </script>

    <style>

        /* Keyframe pour faire tourner les spinners sur cette page */
        @keyframes spin
        {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .scrollable-table {
            margin-right: 12px;
        }

        .table-header-rotated {
            margin-bottom: 0;
        }

        .table-header-rotated th.row-header{
            width: auto;
        }

        .table-header-rotated td{
            position: relative;
            width: 60px;
            border-top: 2px solid #dddddd;
            border-left: 1px solid #dddddd;
            border-right: 1px solid #dddddd;
            vertical-align: middle;
            text-align: center;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .table-header-rotated td .dont-click { display: none; }
        .table-header-rotated td .dont-click.locked {
            display: block;
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            background-color: #FEFEFE;
            opacity: 0.6;
        }

        .table-header-rotated th.rotate-45{
            position: relative;
            left: 40px; /* 80 * tan(45) / 2 = 40 where 80 is the height on the cell and 45 is the transform angle*/
            height: 80px;
            width: 50px;
            min-width: 50px;
            max-width: 50px;
            vertical-align: bottom;
            padding: 0;
            font-size: 11px;
            line-height: 0.8;
            -ms-transform:skew(-45deg,0deg);
            -moz-transform:skew(-45deg,0deg);
            -webkit-transform:skew(-45deg,0deg);
            -o-transform:skew(-45deg,0deg);
            transform:skew(-45deg,0deg);
        }

        .table-header-rotated th.rotate-45 > * {
            display: block;
            height: 100%;
            overflow: hidden;
            border-left: 1px solid #dddddd;
            border-right: 1px solid #dddddd;
            border-top: 2px solid #dddddd;
            background-color: #F2F2F2;
        }

        .table-header-rotated th.rotate-45 span {
            -ms-transform:skew(45deg,0deg) rotate(315deg);
            -moz-transform:skew(45deg,0deg) rotate(315deg);
            -webkit-transform:skew(45deg,0deg) rotate(315deg);
            -o-transform:skew(45deg,0deg) rotate(315deg);
            transform:skew(45deg,0deg) rotate(315deg);
            position: absolute;
            bottom: 30px; /* 40 cos(45) = 28 with an additional 2px margin*/
            left: -15px; /*Because it looked good, but there is probably a mathematical link here as well*/
            display: inline-block;
        // width: 100%;
            width: 85px; /* 80 / cos(45) - 40 cos (45) = 85 where 80 is the height of the cell, 40 the width of the cell and 45 the transform angle*/
            text-align: left;
        // white-space: nowrap; /*whether to display in one line or not*/
        }

        .page-header .toolbar-right {
            float: right;
        }

        .keep-visible {
            position: relative;
            z-index: 5;
        }

        .widget-main a {
            margin-right: 12px;
        }
        .widget-main .label {
            margin-left: 5px;
        }

        .student-thumbnail {
            transition: transform 0.2s;
        }
        .student-thumbnail:hover {
            /* transform: scale(1.8); */
            transform: scale(2.6) translateX(-15px);
        }

        .presence-ratio {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            background-color: #6FB3E0;
            color: white;
        }

    </style>

</body>

</html>
