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
$sport = "";
$id1=@$_GET['id1'];
$bus_modif=@$_POST['bus_modif'];
$page=@$_GET['page'];
$gare="no";

// recuperation du bus choisi apres un refresh
if (isset($_GET['car'])) {
    $sport = $_GET['car'];
}

 echo"<input type='hidden' id='page' value='".$page."'>";
 
          

// recupere quel bus a été choisi
if (isset($_POST['Football'])) {
    $sport = "Football";
} elseif (isset($_POST['Beach-volley'])) {
    $sport = "Beach-volley";
} elseif (isset($_POST['Unihockey'])) {
    $sport = "Unihockey";
} elseif (isset($_POST['Danse'])) {
    $sport = "Danse";
} elseif (isset($_POST['Entreprise'])) {
    $sport = "Entreprise";
}


//en utilisant la fonction date(D) ca nous retourne les trois première lettre du jour en anglais alors je fais ce switch pour écrire en francais et utiliser dans pour les requetes
switch ($day) {
    case 'Mon':
        $day = "lundi";
        break;
    case 'Tue':
        $day = "mardi";
        break;
    case 'Wed':
        $day = "mercredi";
        break;
    case 'Thu':
        $day = "jeudi";
        break;
    case 'Fri':
        $day = "vendredi";
        break;
    case 'Sat':
        $day = "samedi";
        break;
    case 'Son':
        $day = "dimanche";
        break;
}

//inscription de l'heure d'arrivé de l'élève
if ($_GET['in'] == "oui"){

//heure d'arrivee
if (isset($_POST['arrivee'])) {
    $req = $bdd->prepare('update t_sportete set arrivee="' . $date . '" where id_eleve=' . $id . '');
    $req->execute();
    }
	
//heure de depart
	if (isset($_POST['depart'])) {
    $req = $bdd->prepare('update t_sportete set depart="' . $date . '" where id_eleve=' . $id . '');
    $req->execute();
    }
}


// requete selection des élèves inscrit et qui participe le jour même
if (isset($_POST['check']) AND $code != "") {

    $sql = 'SELECT t_eleve.*, t_sportete.*, t_classe.* FROM t_eleve, t_sportete, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_codebarre = ' . $code . ') AND (t_eleve.id_eleve = t_sportete.id_eleve) LIMIT 1;';
    $requete = $bdd->prepare($sql);
    $requete->execute();
    $donnees = $requete->fetchAll();
}

//si le user veut changer de bus on reset la variable $sport
if (isset($_POST['change'])) {

    $sport = "";
}

if(isset($_POST['reini'])){
    $reini=$bdd->prepare('update t_sportete set arrivee="0000-00-00 00:00:00", depart="0000-00-00 00:00:00"  where id_eleve="'.$id1.'"');
    $reini->execute();
}
    


// selection des élèves du jour inscrit mais pas encore présentés
    
        $sql1 = 'SELECT t_eleve.*, t_sportete.*, t_classe.* FROM t_eleve, t_sportete, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sportete.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (t_sportete.choixSport!="entreprise") AND (t_sportete.arrivee="0000-00-00 00:00:00")';
        $requete1 = $bdd->prepare($sql1);
        $requete1->execute();
        $eleve_presence = $requete1->fetchAll();


//slelection des élèves du jours inscrit et présentés


    $sql2 = 'SELECT t_eleve.*, t_sportete.*, t_classe.* FROM t_eleve, t_sportete, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sportete.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (choixSport!="Entreprise")AND (arrivee!="0000-00-00 00:00:00")AND (depart="0000-00-00 00:00:00") ORDER BY t_sportete.arrivee DESC';
    $requete2 = $bdd->prepare($sql2);
    $requete2->execute();
    $eleve_car = $requete2->fetchAll();

//slelection des élèves du jours inscrit 


    $sql3 = 'SELECT t_eleve.*, t_sportete.*, t_classe.* FROM t_eleve, t_sportete, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sportete.id_eleve) AND (cla_joursSport="' . $day . '") AND (choixSport!="") AND (choixSport!="Entreprise") ORDER BY t_sportete.arrivee DESC';
    $requete3 = $bdd->prepare($sql3);
    $requete3->execute();
    $eleve_modif = $requete3->fetchAll();




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
                </ul>
            </div>

            <div class="tab-content">

                <div class="tab-pane active" id="student-container">
                    <!-- formulaire de choix du bus -->


                        <!-- Une fois un sport choisi il affiche la possibilité de scanner  -->


                        <div id="scancheck">
                            <form method="post" name="scan_form" action="?car=<?php echo $sport; ?>&#eleve">
                                <h2>Scanner la carte de l'élève</h2><br>
                                <input type="text" id="scan" name="scan" autofocus="autofocus">
                                <input type="submit" class="btn btn-info" name="check" value="check">&nbsp;&nbsp;&nbsp;&nbsp;

                            </form>
                        </div>
                        <div id="eleve">

                            <?php
                            if ($code != "") {
                                for ($a = 0; $a < count($donnees); $a++) { ?>
                                    <br><br>
                                    <form method="POST" action="?in=oui&id=<?php echo $donnees[$a]['id_eleve']; ?>&car=<?php echo $sport; ?>&#table_id_car_wrapper&page=scanCheck">
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
                                            if (($donnees[$a]['arrivee'] == '0000-00-00 00:00:00') && ($donnees[$a]['depart'] == '0000-00-00 00:00:00')) { ?>
                                                <input type="submit" class="btn btn-success" name="arrivee"
                                                       value="Confirmer arrivée"/>
                                                <?php
                                            } elseif (($donnees[$a]['arrivee'] != '0000-00-00 00:00:00') && ($donnees[$a]['depart'] == '0000-00-00 00:00:00')) {   
												echo '</br></br><h4><span class="label label-success">Arrivé/scanné à : ' . $donnees[$a]['arrivee'] . '</span></h4></br>
												<input type="submit" class="btn btn-success" name="depart"
                                                       value="Confirmer départ"/>';
                                            }elseif (($donnees[$a]['arrivee'] != '0000-00-00 00:00:00') && ($donnees[$a]['depart'] != '0000-00-00 00:00:00')){
												echo '</br></br><h4><span class="label label-success">Arrivé/scanné à : ' . $donnees[$a]['arrivee'] . '</span></h4></br>
													<h4><span class="label label-info">Départ/scanné à : ' . $donnees[$a]['depart'] . '</span></h4>';
											}
                                        } else{
                                            ?> <button type="button" class="btn btn-danger" disabled><?php echo $donnees[$a]['cla_joursSport'];?></button><?php 
                                        }
                                        ?>
                                        <br><br>

                                    </form>
                                <?php }
                            } else {
                            } ?>

                            <br><br>
                            <table id="table_id_car" class="display">
                                <thead>
                                <?php if(count($eleve_car)<=1){ ?>
								
                                    <h4><?php echo count($eleve_car).' '; ?>élève présent </h4><?php }else{?> <h4><?php $nbStudent=count($eleve_car); $nbStudent+=1; echo $nbStudent.' '; ?>élèves présent</h4><?php }?>
                                <tr>
                                    <th>Elève</th>
                                    <th>Choix d'activité</th>
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
                                        <td><?php echo $eleve_car[$a]['cla_nom']; ?></td>
                                        <td><?php echo $eleve_car[$a]['ele_numeromobile']; ?></td>
                                        <td><?php echo $eleve_car[$a]['id_codebarre']; ?></td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>

                        </div>
                    

                </div>
				<!---->
                <div class="tab-pane" id="presence">
                    <!-- tableau affichant tous les éèves inscrit mais non présentés -->
                        <table id="table_id" class="display">
                            <thead>
                            <tr>

                                <th>Elève</th>
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
                                    <td><?php echo $eleve_presence[$a]['choixSport']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['ele_numeromobile']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['cla_nom']; ?></td>
                                    <td><?php echo $eleve_presence[$a]['id_codebarre']; ?></td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                </div>
                <div class="tab-pane" id="modification">
                    <?php for ($a = 0; $a < count($eleve_modif); $a++) { ?>
                        <form method="post" action="?id1=<?php echo $eleve_modif[$a]['id_eleve']; ?>&car=<?php echo $sport; ?>&page=modif">
                            <h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?php
                                            $id_codebarre = $eleve_modif[$a]['id_codebarre'];
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
                                            echo ' '.$eleve_modif[$a]['ele_prenom'].' '.$eleve_modif[$a]['ele_nom'];
                                        ?>
                                    </div>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                     <div class="col-md-8"><br><br>
                                    <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

                                        <input type="submit" class="btn btn-info" name="reini" value="Réinitialiser">
                                    </div>
                                </div>
                            </h4>
                        </form>
                    <?php } ?>
                </div>
				<!---->
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