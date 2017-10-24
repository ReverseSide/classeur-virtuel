<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Création   : 02.05.2016
// But    : Extraction CSV des cas particuliers
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}



//Si formulaire exécuté, création du fichier CSV
if(!empty($_POST['type']))
{
    $startDate=$_POST['start-date'];
    $endDate = $_POST['end-date'];
    $type = $_POST['type'];
    $department = $_POST['department'];

    //inclusion du code permettant l'extraction vers CSV
    if ($type == 99)
        include_once('../include/export-all-csv.inc.php');
    else
        include_once('../include/csv.inc.php');
}


// Obtient la liste des départements
include_once("../include/mysql.inc.php");
$db = new dbIfc();
$departments = $db->GetDepartment();
unset($db);





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

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Extraction des données</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


            <!-- /.row -->
            <div class="row">
                <div class="col-xs-12">
                    <!-- Formulaire extraction -->
                    <form role="form" method="POST" name="formulaire">

                        <div class="form-group">
                            <label for="date-range">Plage d'exportation</label>
                            <div class="input-group" id="date-range">
                                <span class="input-group-addon">Du</span>
                                <input type="text" class="form-control date-picker" name="start-date" />
                                <span class="input-group-addon">au</span>
                                <input type="text" class="form-control date-picker" name="end-date" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="export-type">Type de rapport</label>
                            <select id="export-type" name="type" class="form-control">
                                <option value='1' selected='selected'>Absences</option>
                                <option value='2'>Absences excusées</option>
                                <option value='3'>Absences non excusées</option>
                                <option value='4'>Arrivées tardives</option>
                                <option value='5'>Mises à la porte</option>
                                <option value='6'>Oubli d'affaires de Gym</option>
                                <option value='7'>Santé (parti infirmerie)</option>
                                <option value='99'>Tout exporter (ZIP)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="export-department">Département</label>
                            <select id="export-department" name="department" class="form-control">
                                <option value="*" selected='selected'>Tous les départements</option>
                                <?php foreach($departments as $expDepartment): ?>
                                    <option value="<?= $expDepartment['id_departement'] ?>"><?= $expDepartment['dep_nom'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button value="submit" id="singlebutton" name="singlebutton" class="btn btn-success">Extraire</button>

                    </form>
                    <!-- Fin de : Formulaire extraction -->


                    <!-- Initialisation du datePicker -->
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var datePickers = jQuery(".date-picker");
                            var today = new Date();
                            var year = today.getFullYear();
                            var month = today.getMonth() + 1;  // Janvier est 0
                            var day = today.getDate();
                            if (month < 10) month = "0" + month;
                            if (day < 10) day = "0" + day;
                            datePickers.val(year + "-" + month + "-" + day);
                            datePickers.datepicker({
                                format: "yyyy-mm-dd",
                                language: "fr",
                                calendarWeeks: true,
                                todayHighlight: true
                            });
                        });

                    </script>

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

    <!-- DatePicker + locale -->
    <script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.fr-CH.min.js"></script>

</body>

</html>
