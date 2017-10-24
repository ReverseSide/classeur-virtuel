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

// Récupération de la date du jour
$dateToDisplay = time();
if (isset($_GET['timestamp']))
    $dateToDisplay = $_GET['timestamp'];

//Création du tableau contenant l'horaire de la journée
$bd=new dbIfc();
$tabStudent=$bd->GetMyClass($_SESSION['class']);
$tabSchedule=$bd->GetSchedule($_SESSION['class'], $dateToDisplay);
$classInfo = $bd->GetClassInfo($_SESSION['class']);
$dbDate = strftime("%Y-%m-%d", $dateToDisplay);

// Tri par ordre alphabétique des élèves par leur nom
usort($tabStudent, function($a, $b)
{
    $compNom = strcmp($a['ele_nom'], $b['ele_nom']);
    if ($compNom !== 0)
        return $compNom;
    // Si les noms sont identiques, trier par prénom
    return strcmp($a['ele_prenom'], $b['ele_prenom']);
});

//Pertistage des cas particuliers dans la BDD (plus nécessaire depuis que c'est fait par l'api)
/*if(!empty($_GET['id'])&& !empty($_GET['period']) && !empty($_GET['status']))
{
    $trait_ideleve=$_GET['id'];
    $trait_period=$_GET['period'];
    $trait_status=$_GET['status'];
    $trait_class=$_SESSION['class'];
    $trait_codebarre=$_GET['codebarre'];
    $trait_idcours=$_GET['cours'];
    $blnInsert=$bd->InsertParticuliar($trait_ideleve,$trait_period,$trait_status, $trait_class, $trait_idcours, $trait_codebarre);
}*/
unset($bd);

$ddate = $dbDate;
$date = new DateTime($ddate);
$week = $date->format("W");
if($week < 10){
    $week = substr($week, -1);
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
                    <h1 class="page-header">
                        <span>Gestion des présences</span>
                        <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true'
                               title="Niveau: <?= $classInfo['cla_niveau'] ?><br>Année: <?= $classInfo['cla_type'] ?><br>
                               Jour(s) de cours: <?= $classInfo['cla_jourdecours'] ?><br>Type: <?= $classInfo['typ_nom'] ?><br>
                               Département: <?= $classInfo['dep_nom'] ?><br>Nb élèves: <?= count($tabStudent) ?>">
                            <?= $classInfo['cla_nom'] ?>
                            <i class="fa fa-info-circle"></i>
                        </small>
                        <span class="toolbar-right">
                            <a class="btn btn-info" href="coches.php?idclasse=<?= $_SESSION['class'] ?>" role="button">Coches</a>
                            <a class="btn btn-info" href="trombinoscope.php?idclasse=<?= $_SESSION['class'] ?>" role="button">Trombinoscope</a>
                            <a class="btn btn-info" href="suivi_cours.php?idclasse=<?= $_SESSION['class'] ?>&timestamp=<?= $dateToDisplay ?>" role="button">Suivi</a>
                            <a class="btn btn-info" href="liste_classe.php?idclasse=<?= $_SESSION['class'] ?>&timestamp=<?= $dateToDisplay ?>" role="button">Liste de Classe</a>
                        </span>
                    </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- Définition du format temporel -->
            <div style="display: inline-block; vertical-align: middle; width: 440px; margin-bottom: 10px;">
                <?php
                setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
                $str_TextDate = utf8_encode(strftime("%A %d %B %Y", $dateToDisplay));
                $str_DbDate = strftime('%Y-%m-%d', $dateToDisplay);
				
                ?>
				
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">S<?php echo $week;?></span>
                    <input type="text" class="form-control" value="<?= $str_TextDate ?>" disabled />
                    <span class="input-group-btn">
                        <?php
                            $queryString = "?idclasse=". $_SESSION['class'] ."&timestamp=". $dateToDisplay;
                        ?>
                        <a class="btn btn-default" href="../api/get-prev-active-day.php<?= $queryString ?>">&lt;</a>
                        <button id="day-change" class="btn btn-default">Changer de jour</button>
                        <a class="btn btn-default" href="../api/get-next-active-day.php<?= $queryString ?>">&gt;</a>
                    </span>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var datePicker = jQuery("#day-change");
                        datePicker.datepicker({
                            format: "yyyy-mm-dd",
                            language: "fr",
                            calendarWeeks: true,
                            todayBtn: "linked"
                        }).on("changeDate", function(evt) {
                            if (evt.format() === "<?= $str_DbDate ?>") return;

                            var timestamp = new Date(evt.format()).getTime() / 1000;
                            var decodedUrl = DecodeUrlParams(window.location.href);
                            decodedUrl.params.timestamp = timestamp;
                            window.location = EncodeUrlParams(decodedUrl);
                        });
                        datePicker.datepicker("update", "<?= $str_DbDate ?>");
                    });
                </script>
            </div>
            <div style="display: inline-block; vertical-align: middle; margin-bottom: 10px; margin-left: 20px;">
                Taux de présence:
                <button class="btn btn-info btn-xs" onclick="ComputePresenceRatio('c', <?= $classInfo['id_classe'] ?>, this)">Calculer</button>
            </div>
            <div class="col-xs-8 col-sm-5 pull-right">
                    <div class='scrollable-table'>
                        <table class='table table-striped table-header-rotated'>
                            <thead>
                            <tr>
                                <td style="background-color: #00BE67">P
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Présent">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #00BFA5">ED
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Excusé dans les délais">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #12BBF0">A
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Absence">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #2196F3">C
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Congé">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #FFF033">G
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Oubli des affaires de Gym">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #D50000">EH
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Excusé hors délai">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #F44336">TE
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Absence lors d'un Test">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #F87373">MP
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Mise à la porte">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #EA94FF">S
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Service santé">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #CCCCCC">E
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Excusé">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                                <td style="background-color: #D4AC0D">T
                                    <small style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" data-html='true' title="Arrivée tardive">
                                        <i class="fa fa-info-circle"></i>
                                    </small>
                                </td>
                            </tr>
                            <tr>

                            </tr>
                    </table>

                    </div>
            </div>
            <br><br>
            <div id="timeline-1">
                <div class="row">
                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 keep-visible" style="z-index: 120;background-color: rgba(255, 255, 255, 0.8);box-shadow: 0 0 15px 3px rgba(255,255,255,0.8);margin-left:1px !important;"">
                        <div style="float: right">
                            <!-- Création de la grille d'horaire -->
                            <div class="scrollable-table">
                                <table class="table table-striped table-header-rotated">
                                    <thead>
                                    <tr>

                                        <th>Périodes</th>
                                        <?php
                                        $nb=1;
                                        $blnnocourse=true;
                                        foreach($tabSchedule as $entry)
                                        {
                                            $cou_matlibelle=$entry['cou_matlibelle'];
                                            $cou_matcode = $entry['cou_matcode'];
                                            $id_classe = $_SESSION['class'];
                                            echo "<th class='rotate-45'><a> <!--href='comments.php?idclasse=$id_classe&matcode=$cou_matcode' --><span>$nb. $cou_matlibelle</span></a></th>";
                                            $nb++;
                                        }
                                        ?>

                                    </tr>
                                    </thead>
                                    <tbody>
									<tr>
                                        <th class="row-header">Heure</th>
                                        <?php
                                        $db = new dbIfc();
                                        foreach($tabSchedule as $entry)
                                        {
                                            $heure=$entry['cou_heuredebut'];
											$formheure=date("H:i ", strtotime($heure));
                                            echo "<td>$formheure</td>";
                                        }
                                        unset($db);
                                        ?>
                                    </tr>
                                    <tr>
                                        <th class="row-header">Valider</th>
                                        <?php
                                        $db = new dbIfc();
                                        $activated="";
                                        foreach($tabSchedule as $entry)
                                        {
                                            $nombredecours=$entry['cou_duree'];
                                            $it=0;
                                            while ($nombredecours>$it)
                                            {
                                                $script = "ToggleColumn(this, \"". $entry['id_sdh'] ."\", \"$dbDate\", \".table-header-rotated\");";
                                                $isPresent = $db->IsProfPresent($_SESSION['user_id'], $entry['id_sdh'], $dbDate);
                                                $checked = "";
                                                if ($isPresent)
                                                {
                                                    $checked = " checked='checked'";
                                                }

                                                    //Check si le prof qui a verrouillé est celui qui consulte
                                                    $wichProf = $db->WichProfPresent($_SESSION['user_id'], $entry['id_sdh'], $dbDate);
                                                    foreach ($wichProf as $prof) {

                                                        if ($_SESSION['user_id'] != $prof['idx_professeur']) {
                                                            if ( isset($_SESSION['administration']) && $_SESSION['administration']==true) {

                                                            }else{
                                                            $activated = "disabled";
                                                            }
                                                            $checked = " checked='checked'";
                                                        }
                                                    }

                                                echo "<td><input onclick='$script' type='checkbox'$checked $activated/></td>";
                                                $it++;
                                                $activated="";
                                            }
                                        }
                                        unset($db);
                                        ?>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Fin de : Création de la grille d'horaire -->





                        </div>
                    </div>
                    <!-- Affichage de la liste des élèves -->
                    <div class="col-xs-12 col-sm-10 col-sm-offset-1" style="margin-bottom: 40px;margin-left:1px !important;">

                            <div class="timeline-container">

                                <?php
                                //Récupération de la liste de classe (une seule classe)
                                $bd=new dbIfc();
                                $tabLates=$bd->GetClassLate($_SESSION['class'], $dateToDisplay);
                                $tabDoors=$bd->GetClassDoor($_SESSION['class'], $dateToDisplay);
                                $tabMissings=$bd->GetClassMissing($_SESSION['class'], $dateToDisplay);
                                $tabMissings_te=$bd->GetClassMissing_te($_SESSION['class'], $dateToDisplay);
                                $tabConge=$bd->GetClassConge($_SESSION['class'], $dateToDisplay);
                                $tabGym=$bd->GetClassOubliGym($_SESSION['class'], $dateToDisplay);
                                $tabSante=$bd->GetClassSante($_SESSION['class'], $dateToDisplay);
                                $tabNotices=$bd->GetClassNotices($_SESSION['class'], $dateToDisplay);
                                unset($bd);
                                ?>

                                <!-- Affiche le nom de la classe -->
                                <div class='timeline-label'>
                                    <span class='label label-primary arrowed-in-right label-lg'>
                                        <b><?= $classInfo['cla_nom'] ?></b>
                                    </span>
                                </div>

                                <!-- Affiche les élèves -->
                                <div class='timeline-items'>

                                <?php
                                $currentNumber = 0;
                                    foreach($tabStudent as $entry)
                                    {
                                        $currentNumber++;
                                        $ele_nom=$entry['ele_nom'];
                                        $ele_prenom=$entry['ele_prenom'];
                                        $ele_codebarre=$entry['id_codebarre'];
                                        $cla_nom=$entry['cla_nom'];
                                        $id_eleve=$entry['id_eleve'];
										$naissance=$entry['ele_datedenaissance'];

                                        echo "<div class='timeline-item clearfix'>
                                                <div class='timeline-info'><span style='font-size=25px;' >$currentNumber </span>";

                                        //Affichage de la photo
                                        $filename = "images/utilisateurs/$ele_codebarre.jpg";
                                        $filename2 = "images/utilisateurs/$ele_codebarre.JPG";
										
										// date aujourd'hui
                                        $date = new DateTime();
                                        // date - 18 ans
                                        $date_18 = $date->sub(new DateInterval('P18Y'));

                                        // si $_POST['date_naissance'] est au format date par exemple = 2001-12-25
                                        $date_naissance = new DateTime($naissance);
										
										if($date_naissance >= $date_18)
                                        {
												if (file_exists($filename)) {
												echo "<img class='student-thumbnail timeline-badge warning' alt='Alain Dupré' src='images/utilisateurs/$ele_codebarre.jpg'>";
											} else {
												if(file_exists($filename2))
												{
													echo "<img class='student-thumbnail timeline-badge warning' alt='Alain Dupré' src='images/utilisateurs/$ele_codebarre.JPG'>";
												}
												else
												{
													echo "<img class='student-thumbnail timeline-badge warning' alt='Alain Dupré' src='images/utilisateurs/usermale.png'>";
												}
											}
										}
										else
										{
											if (file_exists($filename)) {
												echo "<img class='student-thumbnail' alt='Alain Dupré' src='images/utilisateurs/$ele_codebarre.jpg'>";
											} else {
												if(file_exists($filename2))
												{
													echo "<img class='student-thumbnail' alt='Alain Dupré' src='images/utilisateurs/$ele_codebarre.JPG'>";
												}
												else
												{
													echo "<img class='student-thumbnail' alt='Alain Dupré' src='images/utilisateurs/usermale.png'>";
												}
											}
										}

                                        

                                        if($_SESSION['login']=="Fernandez"){
                                            echo "
                                                </div>

                                                <div class='widget-box transparent'>
                                                    <div class='widget-body'>
                                                        <div class='widget-main'>
                                                            <a href='student_dtl.php?stu=".$ele_codebarre."'>$ele_prenom $ele_nom</a>";
                                        }else{
                                             echo "
                                                </div>

                                                <div class='widget-box transparent'>
                                                    <div class='widget-body'>
                                                        <div class='widget-main'>
                                                            <a href='student_dtl.php?stu=".$ele_codebarre."'>$ele_prenom $ele_nom</a>";
                                        }

                                        // Affichage des dispenses
                                        $dispenses = "";
                                        $derogation = $entry['ele_derogation'];
                                        $desavantage = $entry['ele_desavantage'];
                                        $remarques = "";
                                        //$statut = $entry['ele_statut'];
                                        if ($entry['ele_dispenseecg']) $dispenses .= " + ECG";
                                        if ($entry['ele_dispensebt']) $dispenses .= " + BT";
                                        if ($entry['ele_dispensesport']) $dispenses .= " + SPORT";
                                        if (strlen($dispenses) > 0) $dispenses = substr($dispenses, 3);
                                        foreach ($tabNotices as $notice)
                                        {
                                            if ($notice['idx_codebarre'] === $ele_codebarre)
                                            {
                                                $remarques .= "<br/><br/>". $notice['rem_message'];
                                            }
                                        }
                                        if (strlen($remarques) > 0)
                                        {
                                            $tooltip = "Remarques: ". addslashes($remarques);
                                            echo "<i class='fa fa-exclamation-circle' style='color:#FF4311;cursor:pointer;' title='$tooltip' data-toggle='tooltip' data-html='true'></i>";
                                        }
                                        if (strlen($dispenses) > 0)
                                        {
											echo "<span class='label label-info'>$dispenses</span>";
                                        }
                                        if (strlen($derogation) > 0)
                                        {
                                            echo "<span class='label label-default'>$derogation</span>";
                                        }
                                        if (strlen($desavantage) > 0)
                                        {
                                            echo "<span class='label label-warning'>$desavantage</span>";
                                        }
                                        //if (strlen($statut) > 0)
                                        //{
                                        //    echo "<span class='label label-success'>$statut</span>";
                                        //}

                                        echo "
                                                            <div style='position: absolute; top: 2px; right: 0'>

                                                                <div class='scrollable-table'>
                                                                    <table class='table table-striped table-header-rotated'>
                                                                        <thead>
                                                                        <tr>
                                                                            <!-- First column header is not rotated -->

                                                                            <!-- Following headers are rotated -->

                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr>";
                                        //Affichage de la ligne du tableau d'absences pour chacun des élèves
                                        $toshow="";
                                        $periode=0;
                                        foreach($tabSchedule as $entryschedule)
                                        {
                                            $idcours=$entryschedule['id_sdh'];
                                            $periode++;
                                            $color = "#00BE67";
                                            $stateCode = "p";
                                            foreach ($tabGym as $gym)
                                            {
                                                if($gym['idx_eleve'] == $id_eleve && $gym['oub_periode'] == $periode)
                                                {
                                                    $color = "#FFF033";
                                                    $stateCode = "g";
                                                }
                                            }
                                            foreach ($tabSante as $sante)
                                            {
                                                if($sante['idx_eleve'] == $id_eleve && $sante['san_periode'] == $periode)
                                                {
                                                    $color = "#EA94FF";
                                                    $stateCode = "s";
                                                }
                                            }
                                            foreach ($tabDoors as $entrydoors)
                                            {
                                                if($entrydoors['idx_eleve']==$id_eleve && $entrydoors['por_periode']==$periode)
                                                {
                                                    $color = "#F87373";
                                                    $stateCode = "mp";
                                                }
                                            }

                                            foreach ($tabConge as $entryconge)
                                            {
                                                if($entryconge['idx_eleve']==$id_eleve && $entryconge['abs_periode']==$periode)
                                                {
                                                    $color = "#2196F3";
                                                    $stateCode = "c";
                                                }
                                            }

                                            foreach ($tabMissings_te as $entrymissing_te)
                                            {
                                                if($entrymissing_te['idx_eleve']==$id_eleve && $entrymissing_te['abs_periode']==$periode)
                                                {

                                                        $color = "#CCCCCC";
                                                        $stateCode = "te";
                                                }
                                            }
                                            foreach ($tabMissings as $entrymissing)
                                            {
                                                if($entrymissing['idx_eleve']==$id_eleve && $entrymissing['abs_periode']==$periode)
                                                {
                                                    if ($entrymissing['abs_excuse'] === "Oui")
                                                    {
                                                        $color = "#CCCCCC";
                                                        $stateCode = "e";
                                                    }

                                                    elseif ($entrymissing['abs_excuse'] === "ED"){
                                                        $couleur = "#00BFA5";
                                                        $status = "ed";
                                                    }
                                                    elseif ($entrymissing['abs_excuse'] === "EH"){
                                                        $couleur = "#D50000";
                                                        $status = "eh";
                                                    }

                                                    else
                                                    {
                                                        $color = "#12BBF0";
                                                        $stateCode = "a";
                                                    }
                                                }
                                            }
                                            foreach($tabLates as $entrylates)
                                            {
                                                if($entrylates['idx_eleve']==$id_eleve && $entrylates['tar_periode']==$periode)
                                                {
                                                    $color = "#D4AC0D";
                                                    $stateCode = "t";
                                                }
                                            }

                                             //check if in stage
                                                $db = new dbIfc();
                                                $tabEnStage = $db->GetCurrentStage($entry["id_eleve"], date("Y-m-d", $dateToDisplay));

                                                // Check dispense
                                                $exempted = false;
                                                $matCode = $entryschedule['cou_matcode'];


                                                $requete3 = $bdd->prepare("select * from t_sdh where id_sdh in (SELECT  idx_cours FROM t_dispense WHERE  t_dispense.idx_Eleve ='" . $entry['id_codebarre'] . "' and dis_actif='1')");
                                                $requete3->execute();
                                                $Dispense = $requete3->fetchAll();


                                                foreach ($Dispense as $entry2) {
                                                    if ($entry2['cou_matcode'] == $matCode ) $exempted = true;
                                                }


                                                $onClickScript = "UpdateParticuliar(this, $id_eleve, $periode, \"$stateCode\", $ele_codebarre, $idcours, $dateToDisplay)";
                                                if ($exempted) {
                                                    $color = "#DDDDDD";
                                                    $stateCode = "d";
                                                    $onClickScript = "return false";
                                                }
											 // if en stage
                                                if(!empty($tabEnStage)){
                                                    $color = "#DDDDDD";
                                                    $stateCode = "st";
                                                    $onClickScript = "return false";
                                                }

                                            $db = new dbIfc();
                                            $locked = "";
                                            if ($db->IsProfPresent($_SESSION['user_id'], $idcours, $dbDate))
                                                $locked = " locked";
                                            //Check si le prof qui est présent est celui qui consulte
                                            $wichProf = $db->WichProfPresent($_SESSION['user_id'], $idcours, $dbDate);
                                            foreach ($wichProf as $prof)
                                            {
                                                if($_SESSION['user_id']!=$prof['idx_professeur'])
                                                {
                                                    $locked = " locked";
                                                }
                                            }

                                            unset($db);

                                            echo "<td style='background-color: $color' onclick='$onClickScript'><div class='dont-click$locked'></div>". strtoupper($stateCode) ."</td>";
                                        }

                                        echo "

                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>





                                                                </div>

                                                            <div class='space-6'></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>";
                                    }

                                    ?>






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

    <!-- DatePicker + locale -->
    <script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../bower_components/bootstrap-datepicker/js/bootstrap-datepicker.fr-CH.min.js"></script>

    <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js" async></script>

	<!-- Vue.js et script de gestion de la page -->
	<script src="https://unpkg.com/vue"></script>
	
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

