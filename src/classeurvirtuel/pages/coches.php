<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 15.08.2016
// But    : Page d'affichage et de saisie des coches pour une classe
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Check si connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

if(!empty($_GET['idclasse']))
{
    $_SESSION['class']=$_GET['idclasse'];
}

//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

// Récupération des données nécessaires à l'affichage initial en base de données
$db=new dbIfc();
$tabCochesNames = $db->GetCoches();
$tabClassCoches = $db->GetClassCoches($_SESSION['class']);
$tabStudents = $db->GetMyClass($_SESSION['class']);

// Parcours de la liste des élèves pour y joindre les chemins vers les photos
foreach ($tabStudents as &$student)
{
    // Image par défaut, sera remplacée si une autre est trouvée
    $student['image'] = "images/utilisateurs/usermale.png";

    // Liste des noms d'images à tester
    $tryFiles = [
        "images/utilisateurs/". $student['id_codebarre'] .".jpg",
        "images/utilisateurs/". $student['id_codebarre'] .".JPG"
    ];

    foreach ($tryFiles as $filename)
    {
        if (!file_exists($filename)) continue;
        $student['image'] = $filename;
        break;
    }
}

?>
<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>CEPM Scan System V2.0</title>

        <!-- Bootstrap Core CSS -->

        <link rel="stylesheet" href="assets/css/ace.min.css" />

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
                        <h1 class="page-header">
                            <span>Gestion des coches</span>
                            <span class="toolbar-right">
                                <a class="btn btn-info" href="timeclock.php?idclasse=<?= $_SESSION['class'] ?>" role="button">Présences</a>
                                <a class="btn btn-info" href="trombinoscope.php?idclasse=<?= $_SESSION['class'] ?>" role="button">Trombinoscope</a>
                            </span>
                        </h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>

                <div id="timeline-1">
                    <div class="row">
                        <div class="col-xs-12 col-sm-10 col-sm-offset-1 keep-visible" style="background-color: rgba(255, 255, 255, 0.8);box-shadow: 0 0 15px 3px rgba(255,255,255,0.8);">
                            <div style="float: right">
                                <!-- Création de la grille des coches -->
                                <div class="scrollable-table">
                                    <table class="table table-striped table-header-rotated">
                                        <thead>
                                            <tr>

                                                <th>Type:</th>
                                                <?php
                                                $blnnocourse=true;
                                                foreach($tabCochesNames as $entry)
                                                {
                                                    $cocheName = $entry['typ_nom'];
                                                    echo "<th class='rotate-45'><div><span>$cocheName</span></div></th>";
                                                }
                                                ?>
                                                <th class="rotate-45 total"><div><span>Totaux</span></div></th>

                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- Fin de : Création de la grille d'horaire -->





                            </div>
                        </div>
                        <!-- Affichage de la liste des élèves -->
                        <div class="col-xs-12 col-sm-10 col-sm-offset-1" style="margin-bottom: 40px;">

                            <div class="timeline-container">

                                <!-- Affiche le nom de la classe (trouvé dans le premier élève -->
                                <?php if (!empty($tabStudents)): ?>
                                <div class='timeline-label'>
                                    <span class='label label-primary arrowed-in-right label-lg'>
                                        <b><?= $tabStudents['0']['cla_nom'] ?></b>
                                    </span>
                                </div>
                                <?php endif; ?>

                                <!-- Affiche les élèves -->
                                <div class='timeline-items'>
                                    <?php foreach($tabStudents as $entry): ?>

                                    <?php
                                    $id_eleve = $entry['id_eleve'];
                                    $ele_image = $entry['image'];
                                    $ele_fullName = $entry['ele_nom'] ." ". $entry['ele_prenom'];
                                    ?>

                                    <div class='timeline-item clearfix'>
                                        <div class='timeline-info'>
                                            <img class='student-thumbnail' alt="<?= $ele_fullName ?>" src="<?= $ele_image ?>">
                                        </div>

                                        <div class='widget-box transparent'>
                                            <div class='widget-body'>
                                                <div class='widget-main'>
                                                    <a href='student_dtl.php?stu=<?= $entry['id_codebarre'] ?>'><?= $ele_fullName ?></a>

                                                    <div class="input-group comment-container" style="max-width:230px;position:absolute;top:4px;right:560px;visibility:hidden">
                                                        <input type="text" class="form-control" placeholder="commentaire...">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default" type="button" onclick="UpdateLastCocheComment(this, <?= $id_eleve ?>)">Ok</button>
                                                        </span>
                                                    </div>

                                                    <div style='position: absolute; top: 2px; right: 0'>

                                                        <div class='scrollable-table'>
                                                            <table class='table table-striped table-header-rotated table-coches'>
                                                                <thead>
                                                                    <tr></tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>

                                                                    <?php
                                                                    $studentCoches = 0;
                                                                    ?>

                                                                    <?php foreach($tabCochesNames as $entry): ?>

                                                                        <?php
                                                                        $cocheId = $entry['id_typecoche'];
                                                                        $cocheName = $entry['typ_nom'];
                                                                        $nbCoches = 0;
                                                                        if (isset($tabClassCoches[$id_eleve]) && isset($tabClassCoches[$id_eleve][$cocheId]))
                                                                        {
                                                                            $nbCoches = $tabClassCoches[$id_eleve][$cocheId]['ammount'];
                                                                        }
                                                                        $studentCoches += $nbCoches;
                                                                        $addScript = "AddCoche(this, $id_eleve, $cocheId)";
                                                                        $removeScript = "RemoveCoche(this, $id_eleve, $cocheId);event.stopPropagation();";
                                                                        $bgColor = "#F9F9F9";
                                                                        if ($nbCoches > 0) $bgColor = "#FBFB95";
                                                                        if ($nbCoches > 2) $bgColor = "#EB8732";
                                                                        if ($nbCoches > 4) $bgColor = "#E84600";
                                                                        ?>
                                                                        <td style="background-color:<?= $bgColor ?>" onclick="<?= $addScript ?>">
                                                                            <div style="position:relative;" class="nb-container"><?= $nbCoches ?><div class="minus" onclick="<?= $removeScript ?>">-</div></div>
                                                                        </td>

                                                                    <?php endforeach; ?>
                                                                        <td class="total"><?= $studentCoches ?></td>

                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                    <div class='space-6'></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php endforeach; ?>

                                </div><!-- /.timeline-items -->
                            </div><!-- /.timeline-container -->

                        </div>
                    </div>
                </div>








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
        </script>

        <style>

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

            .table-header-rotated th.rotate-45{
                position: relative;
                left: 50px; /* 100 * tan(45) / 2 = 50 where 100 is the height on the cell and 45 is the transform angle*/
                height: 100px;
                width: 60px;
                min-width: 60px;
                max-width: 60px;
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
                bottom: 40px; /* 40 cos(45) = 28 with an additional 2px margin*/
                left: -25px; /*Because it looked good, but there is probably a mathematical link here as well*/
                display: inline-block;
                width: 105px; /* 100 / cos(45) - 50 cos (45) = 106.06 where 100 is the height of the cell, 40 the width of the cell and 45 the transform angle*/
                text-align: left;
            }

            .widget-body .table {
                border-bottom: 1px solid #E5E5E5;
            }

            th.total > div,
            td.total {
                background-color: #CDCDCD !important;
                cursor: default;
            }

            td .nb-container > .minus {
                visibility: hidden;
                position: absolute;
                top: -9px;
                right: -9px;
                line-height: 12px;
                width: 14px;
                background-color: white;
                border: 1px solid #D3D3D3;
                opacity: 0;
                transition: opacity 0.4s 0.5s, visibility 0s 1s;
            }

            td .nb-container:hover > .minus {
                visibility: visible;
                opacity: 1;
                transition: none;
            }

            .page-header .toolbar-right {
                float: right;
            }

            .keep-visible {
                position: relative;
                z-index: 5;
            }

            .student-thumbnail {
                transition: transform 0.2s;
            }
            .student-thumbnail:hover {
                /* transform: scale(1.8); */
                transform: scale(2.6) translateX(-15px);
            }

        </style>

    </body>
</html>
