<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 10.08.2016
// But    : Affichage d'un trombinoscope d'une classe
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************


// Check si connecté
if(empty($_SESSION['user_id']) )
{
    header("Location:login.php");
    die("Vous n'êtes pas connecté. Redirection en cours...");
}

// Récupération de l'id de la classe voulue. Si pas fourni on met une valeur impossible à la place
$classId = -1;
if (isset($_GET['idclasse']))
{
    $classId = $_GET['idclasse'];
}

// Récupération de la classe demandée en BdD
include_once('../include/mysql.inc.php');
$bd = new dbIfc();
$tabStudents = $bd->GetMyClass($classId);

// Récupère le nom de la classe (sur le premier élève)
$className = "";
if (!empty($tabStudents))
{
    $className = $tabStudents['0']['cla_nom'];
}

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

// Tout est prêt pour l'affichage du trombinoscope
?>
<!DOCTYPE html>
<html lang="en">
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

            <div id="page-wrapper" class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Trombinoscope <small><?= $className ?></small></h1>
                    </div>
                </div>



                <!-- Rendu du trombinoscope -->
                <div class="row">
                    <?php for($i = 0; $i < count($tabStudents); $i++): ?>
                        <?php
                            $studentFullName = $tabStudents[$i]['ele_prenom'] ." ". $tabStudents[$i]['ele_nom'];
                            $studentImage = $tabStudents[$i]['image'];
                        ?>

                        <div class="col-md-3 bs-col-override">
                            <div class="student-container">
                                <img src="<?= $studentImage ?>" alt="<?= $studentFullName ?>" />
                                <span><?= $studentFullName ?></span>
                            </div>
                        </div>

                    <?php endfor; ?>
                </div>

            </div>

        </div>


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

        <!-- Styles pour cette page -->
        <style>

            .student-container {
                position: relative;
                margin-bottom: 30px;
                border-radius: 3px;
                overflow: hidden;
                box-shadow: 0 0 12px 0 rgba(80, 80, 80, 0.6);
                transition: box-shadow 0.3s;
            }

            .student-container > img {
                width: 100%;
            }

            .student-container > span {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 8px 23%;
                font-size: 18px;
                background-color: rgba(25, 25, 25, 0.8);
                color: white;
                text-align: center;
                transition: all 0.6s;
            }

            .student-container:hover {
                box-shadow: 0 0 18px 2px rgba(30, 30, 30, 0.5);
            }

            .student-container:hover > span {
                bottom: 12px;
                transform: translateY(100%);
                -ms-transform: translateY(100%);
            }

            .student-container > span:hover {
                bottom: 0;
                padding-left: 2%;
                padding-right: 2%;
                font-size: 33px;
                transform: translateY(0);
                -ms-transform: translateY(0);
            }

            /* Pour une raison obscure, les différentes tailles de grilles bootstrap ne fonctionnent pas */
            @media screen and (max-width: 1300px) {
                .bs-col-override {
                    width: 33.3%;
                }
            }
            @media screen and (max-width: 980px) {
                .bs-col-override {
                    float: left;
                    width: 50%;
                }
            }
            @media screen and (max-width: 420px) {
                .bs-col-override {
                    width: 100%;
                }
            }

        </style>

    </body>
</html>
