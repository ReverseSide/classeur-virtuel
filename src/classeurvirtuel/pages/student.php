<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Date dernière modification   : 02.05.2016
// But    : Page d'affichage des détails d'un élève
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}


//Va cherhcer les informations de l'élève
$bd=new dbIfc();
$tabStudent=$bd->GetStudent($_GET['stu']);
unset($bd);


$alreadydone=false;
//Traitement des informations de l'élève
foreach($tabStudent as $entry)
{
    $cla_nom=$entry['cla_nom'];

    $id_codebarre=$entry['id_codebarre'];
    $ele_nom=$entry['ele_nom'];
    $ele_prenom=$entry['ele_prenom'];
    $ele_telephone=$entry['ele_numeromobile'];
    $ele_desavantage=$entry['ele_desavantage'];
    $ele_dispenseecg=$entry['ele_dispenseecg'];
    $ele_dispensebt=$entry['ele_dispensebt'];
    $ele_dispensesport=$entry['ele_dispensesport'];
    $ele_derogation=$entry['ele_derogation'];
    $ele_statut=$entry['ele_statut'];
    $idx_replegal=$entry['idx_representantlegal'];

    $ele_npa=$entry['ele_npa'];
    $ele_rue=$entry['ele_rue'];
    $ele_localite=$entry['ele_localite'];
    $ele_canton=$entry['ele_canton'];
    $ele_majeur=$entry['ele_majeur'];
    $ele_datedenaissance=$entry['ele_datedenaissance'];
    $ele_numeromobile=$entry['ele_numeromobile'];
    $ele_mail=$entry['ele_mail'];


    $ent_nom=$entry['ent_nom'];
    $ent_rue=$entry['ent_rue'];
    $ent_npa=$entry['ent_npa'];
    $ent_localite=$entry['ent_localite'];
    $ent_mail=$entry['ent_mail'];
    $ent_tel1=$entry['ent_tel1'];
    $ent_tel2=$entry['ent_tel2'];

    $mai_nom=$entry['mai_nom'];
    $mai_prenom=$entry['mai_prenom'];
    $mai_tel1=$entry['mai_tel1'];
    $mai_tel2=$entry['mai_tel2'];
    $mai_mobile=$entry['mai_mobile'];

    $rep_nom=$entry['rep_nom'];
    $rep_prenom=$entry['rep_prenom'];
    $rep_rue=$entry['rep_Rue'];
    $rep_npa=$entry['rep_npa'];
    $rep_localite=$entry['rep_localite'];
    $rep_tel1=$entry['rep_tel1'];
    $rep_tel2=$entry['rep_tel2'];
    $rep_mobile=$entry['rep_numeromobile'];

    //Si pas de representant legal
    if($idx_replegal==0)
    {
        $rep_nom="-";
        $rep_prenom="";
        $rep_rue="-";
        $rep_npa="-";
        $rep_localite="";
        $rep_tel1="-";
        $rep_tel2="-";
        $rep_mobile="-";
    }
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
                    <h1 class="page-header"><?php echo "$cla_nom - $ele_nom $ele_prenom" ?></h1>
                    <!-- Fin  de : Titre -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <p><?php
                            //Affichage de la photo
                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                            $filename2 = "images/utilisateurs/$id_codebarre.JPG";



                            if (file_exists($filename)) {
                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                            } else {
                                if(file_exists($filename2))
                                {
                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";
                                }
                                else
                                {
                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";
                                }

                            }


                            ?>
                        </p>



                        <!-- Informations de base -->
                        <table class="table table-striped">

                            <tr>
                                <td style="font-weight: bold">Prénom Nom:</td>
                                <td><?php echo "$ele_prenom $ele_nom"; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Classe:</td>
                                <td><?php if($cla_nom==""){echo "-";}else{echo "$cla_nom";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Téléphone:</td>
                                <td><?php if($ele_telephone=="" || $ele_telephone==0){echo "-";}else{echo "$ele_telephone";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Désavantage:</td>
                                <td><?php if($ele_desavantage==""){echo "-";}else{echo "$ele_desavantage";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Dispense ECG:</td>
                                <td><?php if($ele_dispenseecg==""){echo "-";}else{echo "$ele_dispenseecg";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Dispense BT:</td>
                                <td><?php if($ele_dispensebt==""){echo "-";}else{echo "$ele_dispensebt";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Dispense Sport:</td>
                                <td><?php if($ele_dispensesport==""){echo "-";}else{echo "$ele_dispensesport";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Dérogation:</td>
                                <td><?php if($ele_derogation==""){echo "-";}else{echo "$ele_derogation";} ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Elève statut:</td>
                                <td><?php if($ele_statut==""){echo "-";}else{echo "$ele_statut";} ?></td>
                            </tr>
                            <!-- Fin de : Informations de base -->



                        </table>

                        <!-- Informations eleve -->
                        <button class="accordion">Plus...</button>
                        <div class="panel">
                            <table class="table table-striped">

                                <tr>
                                    <td style="font-weight: bold">Rue:</td>
                                    <td><?php if($ele_rue==""){echo "-";}else{echo "$ele_rue";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">NPA, Localité:</td>
                                    <td><?php echo "$ele_npa $ele_localite" ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Mail:</td>
                                    <td><?php if($ele_mail==""){echo "-";}else{echo "<a href='mailto:$ele_mail'>$ele_mail</a>";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Numéro mobile:</td>
                                    <td><?php if($ele_numeromobile==""){echo "-";}else{echo "$ele_numeromobile";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Date de naissance:</td>
                                    <td><?php if($ele_datedenaissance==""){echo "-";}else{echo "$ele_datedenaissance $ele_majeur";} ?></td>
                                </tr>
                            </table>
                        </div>
                        <!-- Fin de : Informations eleve -->

                        <!-- Informations Employeur -->
                        <button class="accordion">Entreprise</button>
                        <div class="panel">
                            <table class="table table-striped">

                                <tr>
                                    <td style="font-weight: bold">Raison sociale:</td>
                                    <td><?php if($ent_nom==""){echo "-";}else{echo "$ent_nom";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Rue:</td>
                                    <td><?php if($ent_rue==""){echo "-";}else{echo "$ent_rue";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">NPA, Localité:</td>
                                    <td><?php echo "$ent_npa $ent_localite" ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Mail:</td>
                                    <td><?php if($ent_mail==""){echo "-";}else{echo "<a href='mailto:$ent_mail'>$ent_mail</a>";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel1:</td>
                                    <td><?php if($ent_tel1==""){echo "-";}else{echo "$ent_tel1";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel2:</td>
                                    <td><?php if($ent_tel2==""){echo "-";}else{echo "$ent_tel2";} ?></td>
                                </tr>
                            </table>
                        </div>
                        <!-- Fin de : Informations Employeur -->

                        <!-- Informations Maître d'apprentissage -->
                        <button class="accordion">Maître d'apprentissage</button>
                        <div class="panel">
                            <table class="table table-striped">

                                <tr>
                                    <td style="font-weight: bold">Nom, prenom:</td>
                                    <td><?php echo "$mai_nom $mai_prenom" ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel1:</td>
                                    <td><?php if($mai_tel1==""){echo "-";}else{echo "$mai_tel1";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel2:</td>
                                    <td><?php if($mai_tel2==""){echo "-";}else{echo "$mai_tel2";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Mobile:</td>
                                    <td><?php if($mai_mobile==""){echo "-";}else{echo "$mai_mobile";} ?></td>
                                </tr>
                            </table>
                        </div>
                        <!-- Fin de : Informations Maître d'apprentissage -->

                        <!-- Informations Représentant légal -->
                        <button class="accordion">Représentant légal</button>
                        <div class="panel">
                            <table class="table table-striped">

                                <tr>
                                    <td style="font-weight: bold">Nom, prenom:</td>
                                    <td><?php echo "$rep_nom $rep_prenom" ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Rue:</td>
                                    <td><?php if($rep_rue==""){echo "-";}else{echo "$rep_rue";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">NPA, Localité:</td>
                                    <td><?php echo "$rep_npa $rep_localite" ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel1</td>
                                    <td><?php if($rep_tel1==""){echo "-";}else{echo "$rep_tel1";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel2</td>
                                    <td><?php if($rep_tel2==""){echo "-";}else{echo "$rep_tel2";} ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Mobile:</td>
                                    <td><?php if($rep_mobile==""){echo "-";}else{echo "$rep_mobile";} ?></td>
                                </tr>
                            </table>
                        </div>
                        <!-- Fin de : Informations Représentant légal -->

                    </div>
                    <div class="col-md-6">
                        <a class="btn btn-info" role="button" onclick="toggle_visibility('miss');">Absences</a>
                        <a class="btn btn-warning" role="button" onclick="toggle_visibility('late');">Arrivées tardives</a>
                        <a class="btn btn-danger" role="button" onclick="toggle_visibility('door');">Mises à la porte</a>
                        <a class="btn btn-success" role="button" onclick="toggle_visibility('notice')">Remarques</a>
                        <a class="btn btn-default" role="button" onclick="toggle_visibility('stats');
                            ComputePresenceRatio('e', '<?= addslashes($_GET['stu']) ?>', document.getElementById('presence-ratio-placeholder'));">Statistiques</a>


                        <?php

                        //Récupère les absences, arrivées tardives et les mises à la porte
                        $bd=new dbIfc();
                        $tabStudentDoor=$bd->GetStudentDoor($_GET['stu']);
                        $tabStudentLate=$bd->GetStudentLate($_GET['stu']);
                        $tabStudentMed = $bd->GetStudentGym($_GET['stu']);
                        $tabStudentGym = $bd->GetStudentGym($_GET['stu']);
                        $tabMissings=$bd->GetStudentMissing($_GET['stu']);
                        $tabNotices=$bd->GetStudentNotices($_GET['stu']);
                        unset($bd);

                        ?>

                        <!-- Tableau des Absences -->
                        <div id="miss" hidden>
                            <form>
                                <br>

                                <button type="button" onclick="UpdateStudentMissings(CollectMissings())" class="btn btn-secondary" style="text-align: right;">Mettre à jour</button>
                                <div class="col-sm-10">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Cours</th>
                                                <th>P</th>
                                                <th>Excusé</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($tabMissings as $entry) : ?>

                                                <?php
                                                $excuse = strtolower($entry['abs_excuse']) === "oui";
                                                ?>

                                                <tr id="miss-<?= $entry['id_absence'] ?>">

                                                    <?php if (empty($entry['abs_date'])) : ?>
                                                        <th style="text-align: right">--</th>
                                                    <?php else: ?>
                                                        <th><?= $entry['abs_date'] ?></th>
                                                    <?php endif; ?>
                                                    <td><?= $entry['cou_matlibelle'] ?></td>
                                                    <td><?= $entry['abs_periode'] ?></td>
                                                    <td><input type="checkbox" name="check" <?php if ($excuse) { echo "checked='checked'"; } ?>></td>
                                                    <td><input type="text" name="comment" value="<?= $entry['abs_commentaire'] ?>"></td>

                                                </tr>
                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                    <script>
                                        function CollectMissings()
                                        {
                                            var rows = jQuery("#miss").find("tbody").find("tr");
                                            var values = [];
                                            rows.each(function(id, element)
                                            {
                                                checked = "Non";
                                                if (element.querySelector("input[name=check]").checked)
                                                    checked = "Oui";

                                                values.push({
                                                    id: element.id.substr(element.id.indexOf("-") + 1),
                                                    checked: checked,
                                                    comment: element.querySelector("input[name=comment]").value.trim()
                                                });
                                            });
                                            return values;
                                        }
                                    </script>
                                </div>

                            <form>
                        </div>
                        <!-- Fin de : Tableau absence-->

                        <!-- Tableau Arrivée tardive -->
                        <div id="late" hidden>
                            <form>
                                <br>

                                <button type="button" onclick="UpdateStudentLates(CollectLates())" class="btn btn-secondary" style="text-align: right;">Mettre à jour</button>
                                <div class="col-sm-10">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Début du cours</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tabStudentLate as $entry) : ?>
                                                <tr id="late-<?= $entry['id_tardive'] ?>">
                                                    <th><?= $entry['tar_date'] ?></th>
                                                    <td><?= $entry['cou_heuredebut'] ?></td>
                                                    <td><input type="text" name="comment" value="<?= $entry['tar_commentaire'] ?>"></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <script>
                                        function CollectLates()
                                        {
                                            var rows = jQuery("#late").find("tbody").find("tr");
                                            var values = [];
                                            rows.each(function(id, element)
                                            {
                                                values.push({
                                                    id: element.id.substr(element.id.indexOf("-") + 1),
                                                    comment: element.querySelector("input[name=comment]").value.trim()
                                                });
                                            });
                                            return values;
                                        }
                                    </script>
                                </div>

                            <form>
                        </div>
                        <!-- Fin de : Formulaire Arrivée tardive-->

                        <!-- Tableau Mise à la porte -->
                        <div id="door" hidden>
                            <form>
                                <br>

                                <button type="button" onclick="UpdateStudentDoors(CollectDoors())" class="btn btn-secondary" style="text-align: right;">Mettre à jour</button>
                                <div class="col-sm-10">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Professeur</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tabStudentDoor as $entry) : ?>
                                                <tr id="door-<?= $entry['id_porte'] ?>">
                                                    <th><?= $entry['por_date'] ?></th>
                                                    <td><?= $entry['pro_nomprenom'] ?></td>
                                                    <td><input type="text" name="comment" value="<?= $entry['por_commentaire'] ?>"></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <script>
                                        function CollectDoors()
                                        {
                                            var rows = jQuery("#door").find("tbody").find("tr");
                                            var values = [];
                                            rows.each(function(id, element)
                                            {
                                                values.push({
                                                    id: element.id.substr(element.id.indexOf("-") + 1),
                                                    comment: element.querySelector("input[name=comment]").value.trim()
                                                });
                                            });
                                            return values;
                                        }
                                    </script>
                                </div>

                            <form>
                        </div>
                        <!-- Fin de : Formulaire Mise à la porte-->

                        <!-- Tableau Remarques -->
                        <div id="notice" hidden>
                            <form>
                                <br>

                                <button type="button" class="btn btn-secondary" onclick="UpdateStudentNotices('<?= $id_codebarre ?>', CollectNotices())" style="text-align: right;">Mettre à jour</button>
                                <div class="col-sm-10">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date Debut</th>
                                                <th>Date Fin</th>
                                                <th>Remarque</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach($tabNotices as $entry): ?>

                                            <tr>
                                                <td><input type="text" class="date-picker start-date" value="<?= $entry['rem_datedebut'] ?>" /></td>
                                                <td><input type="text" class="date-picker end-date" value="<?= $entry['rem_datefin'] ?>" /></td>
                                                <td><input type="text" class="remarque-message" name="comment" value="<?= $entry['rem_message'] ?>"></td>
                                            </tr>

                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-success" onclick="AddNoticeRow()">Ajouter</button>
                                    <script>
                                        function AddNoticeRow()
                                        {
                                            var tableBody = jQuery("#notice").find("tbody");
                                            tableBody.append(jQuery('<tr>')
                                                .append(jQuery('<td>')
                                                    .append(jQuery('<input>').attr('type', 'text').attr('class', 'date-picker start-date')))
                                                .append(jQuery('<td>')
                                                    .append(jQuery('<input>').attr('type', 'text').attr('class', 'date-picker end-date')))
                                                .append(jQuery('<td>')
                                                    .append(jQuery('<input>').attr('type', 'text').attr('name', 'comment').attr('class', 'remarque-message')))
                                            );
                                            RefreshDatePickers();
                                        }
                                        function CollectNotices()
                                        {
                                            var rows = jQuery("#notice").find("tbody").find("tr");
                                            var values = [];
                                            rows.each(function(id, element)
                                            {
                                                var row = {};
                                                row.startDate = element.querySelector("input.start-date").value;
                                                row.endDate = element.querySelector("input.end-date").value;
                                                row.message = element.querySelector("input.remarque-message").value.trim();
                                                if (row.message.length > 0)
                                                {
                                                    values.push(row);
                                                }
                                            });
                                            return values;
                                        }
                                    </script>
                                </div>

                                <form>
                        </div>
                        <!-- Fin de : Formulaire Remarques-->

                        <!-- Tableau Statistiques -->
                        <div id="stats" hidden>
                            <br>
                            <div class="col-sm-10">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>Nombre de cours absent</td>
                                            <td width="150px"><?= count($tabMissings) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Nombre d'arrivées tardives</td>
                                            <td><?= count($tabStudentLate) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Nombre de mises à la porte</td>
                                            <td><?= count($tabStudentDoor) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Nombre de visites au service santé</td>
                                            <td><?= count($tabStudentMed) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Nombre d'oublis SPORT</td>
                                            <td><?= count($tabStudentGym) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Taux de présence</td>
                                            <td>
                                                <span id="presence-ratio-placeholder"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Fin de : Formulaire Statistiques -->

                    </div><!--Fin Colonne--><br>

                    </div>
                </div>
            </div>
        <!-- Code JS Affichage des tableaux Absences - Arrivées tardives - Mises à la porte -->
        <script type="text/javascript">

            function toggle_visibility(id) {
                var e = document.getElementById(id);

                document.getElementById('miss').style.display = 'none';
                document.getElementById('late').style.display = 'none';
                document.getElementById('door').style.display = 'none';
                document.getElementById('notice').style.display = 'none';
                document.getElementById('stats').style.display = 'none';

                if(e.style.display == 'block')
                    e.style.display = 'none';
                else
                    e.style.display = 'block';


            }
            var acc = document.getElementsByClassName("accordion");
            var i;

            for (i = 0; i < acc.length; i++) {
                acc[i].onclick = function(){
                    this.classList.toggle("active");
                    this.nextElementSibling.classList.toggle("show");
                }
            }

        </script>

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
