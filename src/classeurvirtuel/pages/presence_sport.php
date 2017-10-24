<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location:login.php");
}

include '../include/bdd.php';
// récuperation des valeurs pour les requetes 
$code = @$_POST['scan'];
$id = @$_GET['id'];
$day = date("D");
$date = date("Y-m-d H:i:s");
$car = "";
$id1=@$_GET['id1'];
$bus_modif=@$_POST['bus_modif'];
$page=@$_GET['page'];
$gare="no";

// recuperation du bus choisi apres un refresh
if (isset($_GET['car'])) {
    $car = $_GET['car'];
}

 echo"<input type='hidden' id='page' value='".$page."'>";
          

// recupere quel bus a été choisi
if (isset($_POST['L1'])) {
    $car = "L1";
} elseif (isset($_POST['L2'])) {
    $car = "L2";
} elseif (isset($_POST['L3'])) {
    $car = "L3";
} elseif (isset($_POST['L4'])) {
    $car = "L4";
} elseif (isset($_POST['L5'])) {
    $car = "L5";
} elseif (isset($_POST['L6'])) {
    $car = "L6";
} elseif (isset($_POST['L7'])) {
    $car = "L7";
} elseif (isset($_POST['L8'])) {
    $car = "L8";
} elseif (isset($_POST['A1'])) {
    $car = "A1";
} elseif (isset($_POST['A2'])) {
    $car = "A2";
} elseif (isset($_POST['A3'])) {
    $car = "A3";
} elseif (isset($_POST['Leysin'])) {
    $car = "Leysin";
} else {
}


//en utilisant la fonction date(D) ca nous retourne les trois première lettre du jour en anglais alors je fais ce switch pour écrire en francais et utiliser dans pour les requetes
switch ($day) {
    case Mon:
        $day = "lundi";
        break;
    case Tue:
        $day = "mardi";
        break;
    case Wed:
        $day = "mercredi";
        break;
    case Thu:
        $day = "jeudi";
        break;
    case Fri:
        $day = "vendredi";
        break;
    case Sat:
        $day = "samedi";
        break;
    case Son:
        $day = "dimanche";
        break;
}

//inscription de l'heure d'arrivé de l'élève
if ($_GET['in'] == "oui"){

if (isset($_POST['arrivee'])) {
    $req = $bdd->prepare('update t_sporthiver set arrivee="' . $date . '", num_car="' . $car . '" where id_eleve=' . $id . '');
    $req->execute();
    }
}


// requete selection des élèves inscrit et qui participe le jour même
if (isset($_POST['check']) AND $code != "") {

    $sql = 'SELECT t_eleve.*, t_sporthiver.*, t_classe.* FROM t_eleve, t_sporthiver, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_codebarre = ' . $code . ') AND (t_eleve.id_eleve = t_sporthiver.id_eleve);';
    $requete = $bdd->prepare($sql);
    $requete->execute();
    $donnees = $requete->fetchAll();
}

//si le user veut changer de bus on reset la variable $car
if (isset($_POST['change'])) {

    $car = "";
}

if(isset($_POST['reini'])){
    $reini=$bdd->prepare('update t_sporthiver set arrivee="0000-00-00 00:00:00", num_car="" where id_eleve="'.$id1.'"');
    $reini->execute();
}

if(isset($_POST['modif']) and $bus_modif!=""){
    $modif=$bdd->prepare('update t_sporthiver set num_car="'.$bus_modif.'" where id_eleve="'.$id1.'"');
    $modif->execute();
    
}

// selection des élèves du jour inscrit mais pas encore présentés
if(@$_POST['lausanne']=="Lausanne"){
    
        $sql1 = 'SELECT t_eleve.*, t_sporthiver.*, t_classe.* FROM t_eleve, t_sporthiver, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sporthiver.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (num_car ="") AND (t_sporthiver.choixSport!="entreprise") AND (t_sporthiver.lieuxDepart="lausanne")';
        $requete1 = $bdd->prepare($sql1);
        $requete1->execute();
        $eleve_presence = $requete1->fetchAll();
        $gare="lausanne";
}

if(@$_POST['aigle']=="Aigle"){
    
        $sql1 = 'SELECT t_eleve.*, t_sporthiver.*, t_classe.* FROM t_eleve, t_sporthiver, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sporthiver.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (num_car ="") AND (t_sporthiver.choixSport!="entreprise") AND (t_sporthiver.lieuxDepart="aigle")';
        $requete1 = $bdd->prepare($sql1);
        $requete1->execute();
        $eleve_presence = $requete1->fetchAll();
        $gare="aigle";
}

if(@$_POST['leysin']=="Leysin"){
    
        $sql1 = 'SELECT t_eleve.*, t_sporthiver.*, t_classe.* FROM t_eleve, t_sporthiver, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sporthiver.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (num_car ="") AND (t_sporthiver.choixSport!="entreprise") AND (t_sporthiver.lieuxDepart="leysin")';
        $requete1 = $bdd->prepare($sql1);
        $requete1->execute();
        $eleve_presence = $requete1->fetchAll();
        $gare="leysin";
}

//slelection des élèves du jours inscrit et présentés
if (isset($car)) {

    $sql2 = 'SELECT t_eleve.*, t_sporthiver.*, t_classe.* FROM t_eleve, t_sporthiver, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sporthiver.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (num_car ="' . $car . '") ORDER BY t_sporthiver.arrivee DESC';
    $requete2 = $bdd->prepare($sql2);
    $requete2->execute();
    $eleve_car = $requete2->fetchAll();

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
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="assets/css/inscription.css">
    
    <!--------------------------------------------------------------------------------------------->
    
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">

    
    <!----------------------------------------------------------------------------------------->

     
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

</head>

<body>

<div id="wrapper">
    <?php include("../include/menu.php"); ?>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Scan présence</h1>
            </div>
        </div>
        <div class="row">

            <div class="menuInscription">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#student-container" aria-controls="student-container" role="tab" data-toggle="tab">ScanCheck</a></li>
                    <li role="presentation"><a href="#presence" aria-controls="paie" role="tab" data-toggle="tab">Personne non scannée</a></li>
                    <li role="presentation"><a href="#modification" aria-controls="paie" role="tab" data-toggle="tab">Modification</a></li>
                    <?php if($_SESSION['user_name']=="Fernandez David"){?> <li role="presentation"><a href="#extraction" aria-controls="paie" role="tab" data-toggle="tab">Extraction</a></li> <?php } ?>
                    
                </ul>
            </div>

            <div class="tab-content">

                <div class="tab-pane active" id="student-container">
                    <!-- formulaire de choix du bus -->
                    <?php if (empty($car)) { ?>

                        <div id="car">

                            <h1>Choisissez votre bus</h1> <br><br>
                            <form method="post">
                                <input type="submit" class="btn btn-primary btn-lg" name="L1" value="L1">
                                <input type="submit" class="btn btn-primary btn-lg" name="L2" value="L2">
                                <input type="submit" class="btn btn-primary btn-lg" name="L3" value="L3">
                                <input type="submit" class="btn btn-primary btn-lg" name="L4" value="L4">
                                <input type="submit" class="btn btn-primary btn-lg" name="L5" value="L5">
                                <input type="submit" class="btn btn-primary btn-lg" name="L6" value="L6">
                                <input type="submit" class="btn btn-primary btn-lg" name="L7" value="L7">
                                <input type="submit" class="btn btn-primary btn-lg" name="L8" value="L8"><br><br>
                                <input type="submit" class="btn btn-primary btn-lg" name="A1" value="A1">
                                <input type="submit" class="btn btn-primary btn-lg" name="A2" value="A2">
                                <input type="submit" class="btn btn-primary btn-lg" name="A3" value="A3"><br><br>
                                <input type="submit" class="btn btn-primary btn-lg" name="Leysin" value="Leysin">
                            </form>
                        </div>

                    <?php } elseif (isset($car)) { ?>

                        <!-- Une fois un bus choisi il affiche la possibilité de scanner  -->


                        <div id="scancheck">
                            <form method="post" name="scan_form" action="?car=<?php echo $car; ?>&#eleve">
                                <h2>Scanner la carte de l'élève</h2><br>
                                <input type="text" id="scan" name="scan" autofocus="autofocus">
                                <input type="submit" class="btn btn-info" name="check" value="check">&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="submit" class="btn btn-info" name="change" value="Changer de bus">

                            </form>
                        </div>
                        <div id="eleve">

                            <?php
                            if ($code != "") {
                                for ($a = 0; $a < count($donnees); $a++) { ?>
                                    <br><br>
                                    <form method="POST" action="?in=oui&id=<?php echo $donnees[$a]['id_eleve']; ?>&car=<?php echo $car; ?>&#table_id_car_wrapper&page=scanCheck">
                                        <?php
                                        //trouve la photo de l'utilisateur dans le dossier images/utilisateurs et l'afficher
                                        $filename = "images/utilisateurs/$code.jpg";
                                        $filename2 = "images/utilisateurs/$code.JPG";

                                        if (file_exists($filename)) {
                                            echo "<img alt='Alain Dupré' src='images/utilisateurs/$code.jpg' width='108' height='144'>";
                                        } else {
                                            if (file_exists($filename2)) {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$code.JPG' width='108' height='144'>";
                                            } else {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";
                                            }
                                        }
                                        ?>

                                        &nbsp;&nbsp;
                                        <?php echo $donnees[$a]['ele_prenom'] . ' ' . $donnees[$a]['ele_nom'] . ', ' . $donnees[$a]['cla_nom'].', '.strtolower($donnees[$a]['cla_joursSport']); ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                        <?php
                                        if(strtolower($donnees[$a]['cla_joursSport'])==$day){
                                            if ($donnees[$a]['arrivee'] == '0000-00-00 00:00:00') { ?>
                                                <input type="submit" class="btn btn-success" name="arrivee"
                                                       value="Confirmer arrivée"/>
                                                <?php
                                            } elseif ($donnees[$a]['arrivee'] != '0000-00-00 00:00:00') {
                                                echo "Déjà enregistré";
                                            }
                                        } else{
                                            ?> <button type="button" class="btn btn-danger" disabled><?php echo $donnees[$a]['cla_joursSport'];?></button><?php 
                                        }
                                        ?>
                                        <br><br>
                                        <div class="" id="info">
                                            <button type="button" class="btn btn-primary"><?php echo $donnees[$a]['choixSport']; ?></button>
                                            <?php if($donnees[$a]['materiel']=="oui"){ ?><button type="button" class="btn btn-success">A besoin de matériel</button><?php } ?>
                                            <?php if($donnees[$a]['coursESS']=="oui"){ ?><button type="button" class="btn btn-warning">Participe a un cours</button><?php } ?>
                                        </div>
                                    </form>
                                <?php }
                            } else {
                            } ?>

                            <br><br>
                            <table id="table_id_car" class="display">
                                <thead>
                                <h3>Elèves inscrit dans le bus <?php echo $car; ?></h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php if(count($eleve_car)<=1){ ?>
                                    <h4><?php echo count($eleve_car).' '; ?>élève présent </h4><?php }else{?> <h4><?php echo count($eleve_car).' '; ?>élèves présent</h4><?php }?>
                                <tr>
                                    <th>Elève</th>
                                    <th>Choix d'activité</th>
                                    <th>A besoin de matériel</th>
                                    <th>Veut participer a un cours</th>
                                    <th>Nom classe</th>
                                    <th>numéro de téléphone</th>
                                    <th>Codebarre</th>
                        <!--            <th>Lieux de départ</th>
                                    <th>Numéro de car</th> -->
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                for ($a = 0; $a < count($eleve_car); $a++) { ?>

                                    <tr>
                                        <td>


                                            <?php
                                            //trouve la photo de l'utilisateur dans le dossier images/utilisateurs et l'afficher
                                            $id_codebarre = $eleve_car[$a]['id_codebarre'];
                                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                                            $filename2 = "images/utilisateurs/$id_codebarre.JPG";


                                            if (file_exists($filename)) {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                            } else {
                                                if (file_exists($filename2)) {
                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";

                                                } else {
                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";
                                                }

                                            }

                                            ?>
                                            <?php echo $eleve_car[$a]['ele_prenom'] . " " . $eleve_car[$a]['ele_nom']; ?>
                                        </td>
                                        <td><?php echo $eleve_car[$a]['choixSport']; ?></td>
                                        <td><?php echo $eleve_car[$a]['materiel']; ?></td>
                                        <td><?php echo $eleve_car[$a]['coursESS']; ?></td>
                                        <td><?php echo $eleve_car[$a]['cla_nom']; ?></td>
                                        <td><?php echo $eleve_car[$a]['ele_numeromobile']; ?></td>
                                        <td><?php echo $eleve_car[$a]['id_codebarre']; ?></td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>

                        </div>
                    <?php } ?>

                </div>
                <div class="tab-pane" id="presence">
                    <!-- tableau affichant tous les éèves inscrit mais non présentés -->
                    <div id="gare" class="col-md-3 col-md-offset-4">
                        <h1>Choisissez votre gare</h1>
                        <form method="post" action="?page=nonScan&car=<?php echo $car; ?>">
                            <br><br>
                            <input type="submit" class="btn btn-primary btn-lg" name="lausanne" value="Lausanne">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="submit" class="btn btn-primary btn-lg" name="aigle" value="Aigle">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="submit" class="btn btn-primary btn-lg" name="leysin" value="Leysin">
                        </form>
                    </div>
                    <?php if($gare!="no"){ ?>
                    
                        <table id="table_id" class="display">
                            <thead>
                            <tr>

                                <th>Elève</th>
                                <th>Lieux départ</th>
                                <th>Choix d'activité</th>
                                <th>numéro de téléphone</th>
                                <th>Nom classe</th>
                                <th>Codebarre</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            for ($a = 0; $a < count($eleve_presence); $a++) { ?>

                                <tr>
                                    <td>


                                        <?php
                                        $id_codebarre = $eleve_presence[$a]['id_codebarre'];
                                        $filename = "images/utilisateurs/$id_codebarre.jpg";
                                        $filename2 = "images/utilisateurs/$id_codebarre.JPG";


                                        if (file_exists($filename)) {
                                            echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                        } else {
                                            if (file_exists($filename2)) {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";

                                            } else {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";
                                            }

                                        }

                                        ?>
                                        <?php echo $eleve_presence[$a]['ele_prenom'] . " " . $eleve_presence[$a]['ele_nom']; ?>
                                    </td>
                                    <td><?php echo $eleve_presence[$a]['lieuxDepart']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['choixSport']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['ele_numeromobile']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['cla_nom']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['id_codebarre']; ?></td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    <?php } ?>
                </div>
                <div class="tab-pane" id="modification">
                    <?php for ($a = 0; $a < count($eleve_car); $a++) { ?>
                        <form method="post" action="?id1=<?php echo $eleve_car[$a]['id_eleve']; ?>&car=<?php echo $car; ?>&page=modif">
                            <h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?php
                                            $id_codebarre = $eleve_car[$a]['id_codebarre'];
                                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                                            $filename2 = "images/utilisateurs/$id_codebarre.JPG";


                                            if (file_exists($filename)) {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                            } else {
                                                if (file_exists($filename2)) {
                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";

                                                } else {
                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";
                                                }

                                            }
                                            echo ' '.$eleve_car[$a]['ele_prenom'].' '.$eleve_car[$a]['ele_nom'];
                                        ?>
                                    </div>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                     <div class="col-md-8"><br><br>
                                        scanné dans le bus: <?php echo ' '.$eleve_car[$a]['num_car'];?>
                                    <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                                        <select name="bus_modif" id="bus_modif">
                                               <option value=""></option>
                                               <option value="L1">L1</option>
                                               <option value="L2">L2</option>
                                               <option value="L3">L3</option>
                                               <option value="L4">L4</option>
                                               <option value="L5">L5</option>
                                               <option value="L6">L6</option>
                                               <option value="L7">L7</option>
                                               <option value="L8">L8</option>
                                               <option value="A1">A1</option>
                                               <option value="A2">A2</option>
                                               <option value="A3">A3</option>
                                       </select>
                                        <input type="submit" class="btn btn-info" name="modif" value="Modifier">&nbsp;&nbsp;&nbsp;
                                        <input type="submit" class="btn btn-info" name="reini" value="Réinitialiser">
                                    </div>
                                </div>
                            </h4>
                        </form>
                    <?php } ?>
                </div>
                <div class="tab-pane" id="extraction">
                    <div id="extra" class="col-md-5 col-md-offset-3">
                        <br><h1>Choisissez vos informations</h1><br>
                        
                        <form method="post" action="extraction_car.php">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Jour:
                            <select name="jour_extra">
                                <option><?php echo $day; ?></option>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Bus:
                            <select name="bus_extra" id="bus_extra">
                                                   <option value=""></option>
                                                   <option value="L1">L1</option>
                                                   <option value="L2">L2</option>
                                                   <option value="L3">L3</option>
                                                   <option value="L4">L4</option>
                                                   <option value="L5">L5</option>
                                                   <option value="L6">L6</option>
                                                   <option value="L7">L7</option>
                                                   <option value="L8">L8</option>
                                                   <option value="A1">A1</option>
                                                   <option value="A2">A2</option>
                                                   <option value="A3">A3</option>
                                           </select>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             <input type="submit" value="Envoyer">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        //<body onload="document.forms['scan_form'].elements['scan'].focus()">
         $('#table_id_car').dataTable({
                        
                        paging: false,
                        bFilter: false,
                        "bSort" : false,
                        //"aaSorting" : [[]],
                        dom: 'Bfrtip',
                        buttons: [
                                    {
                                        text: 'Imprimer',
                                        extend: 'print',
                                    }
                                ]
         });      
   
        $('#table_id').dataTable({
            paging: false,
            
        });

        $('#scan').focus();
        
         var page=document.getElementById('page').value;
            
                //document.write(page);
        
                if(page=="scanCheck"){
                    $('.nav-tabs a[href="#table_id_car"]').tab('show');
                }
        
                if(page=="nonScan"){
                    $('.nav-tabs a[href="#presence"]').tab('show');
                }
            
                if(page=="modif"){
                    $('.nav-tabs a[href="#modification"]').tab('show');
                }
    });
    
    

</script>
</body>

</html>