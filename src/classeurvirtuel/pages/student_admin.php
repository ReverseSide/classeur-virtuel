<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Alessandro Sipala
// Date dernière modification   : 02.05.2016
// But    : Page administrateur permet la modification et l'ajout des informations des élèves
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

//inclusion de la classe d'interaction avec la base de données

include '../include/bdd.php';

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}
    // vérifie qu' un formulaire d'ajout a été complété
   if(isset($_GET['ajout'])){
       // ajout d'un nouvel élève
       if($_GET['ajout']=="eleve"){
          if($_POST['ele_npa']==""){$_POST['ele_npa']=0;}
          if($_POST['ele_datedenaissance']==""){$_POST['ele_datedenaissance']=0000-00-00;}
          if($_POST['ele_debutdeformation']==""){$_POST['ele_debutdeformation']=0000-00-00;}
          if($_POST['ele_findeformation']==""){$_POST['ele_findeformation']=0000-00-00;}
          $eleve="INSERT INTO t_eleve (ele_nom, ele_prenom, ele_politesse, id_codebarre, ele_datedenaissance, ele_numerodecontrat, ele_debutdeformation, ele_findeformation, ele_majeur, ele_Rue, ele_npa, ele_localite, ele_canton, ele_numeromobile, ele_mail, ele_desavantage, ele_dispenseecg, ele_dispensebt, ele_dispensesport, ele_derogation, ele_statut, idx_classe, idx_entreprise ) values ('".$_POST['ele_nom']."', '".$_POST['ele_prenom']."', '".$_POST['politesse']."', '".$_POST['id_codebarre']."', ".$_POST['ele_datedenaissance'].", '".$_POST['ele_numerodecontrat']."', ".$_POST['ele_debutdeformation'].", ".$_POST['ele_findeformation'].", '".$_POST['choixAge']."', '".$_POST['ele_rue']."', ".$_POST['ele_npa'].", '".$_POST['ele_localite']."', '".$_POST['ele_canton']."', '".$_POST['ele_numeromobile']."', '".$_POST['ele_mail']."', '".$_POST['ele_desa']."', '".$_POST['ele_disp']."', '".$_POST['ele_dispbt']."', '".$_POST['ele_disp_sport']."', '".$_POST['ele_derog']."', '".$_POST['ele_statut']."', ".$_POST['choixListe'].", ".$_POST['choixEntreprise'].")";
          $req=$bdd->prepare($eleve);
          $req->execute();
           
          $sport="INSERT INTO t_sporthiver (id_eleve, codeBarre) select id_eleve, id_codebarre from t_eleve where id_codebarre='".$_POST['id_codebarre']."'";
          $req1=$bdd->prepare($sport);
          $req1->execute();
          
       }
       // ajout d'un nouveau representant
       if($_GET['ajout']=="rep"){
           if($_POST['rep_npa']==""){$_POST['rep_npa']=0;}
           $representant1="insert into t_representantlegal (rep_nom, rep_prenom, rep_politesse, rep_Rue, rep_npa, rep_localite, rep_tel1, rep_tel2, rep_numeromobile) values ('".$_POST['rep_nom']."', '".$_POST['rep_prenom']."', '".$_POST['politesse']."', '".$_POST['rep_rue']."', ".$_POST['rep_npa'].", '".$_POST['rep_loca']."', '".$_POST['rept-tel1']."', '".$_POST['rep_tel2']."', '".$_POST['rep_mobile']."')";
           $req=$bdd->prepare($representant1);
           $req->execute();
           $sql1='Update t_eleve set idx_representantlegal=(SELECT max(id_representantlegal) from t_representantlegal) where id_codebarre='.$_GET['stu'].'';
           $req1=$bdd->prepare($sql1);
           $req1->execute();
           
       }
       // ajout d'un nouveau maitre d'apprentissage
       if($_GET['ajout']=="maitre"){
           
           $maitre="INSERT INTO t_maitredapprentissage (mai_nom, mai_prenom, mai_tel1, mai_tel2, mai_mobile, idx_entreprise) values ('".$_POST['mai_nom']."', '".$_POST['mai_prenom']."', '".$_POST['mai_tel1']."', '".$_POST['mai_tel2']."', '".$_POST['mai_mobile']."', ".$_POST['choixEntreprise'].")";
           $req=$bdd->prepare($maitre);
           $req->execute();
       }
       
       if($_GET['ajout']=="ent"){
           $ent="INSERT INTO t_entreprise (ent_nom, ent_rue, ent_npa, ent_localite, ent_canton, ent_tel1, ent_tel2, ent_mail) values('".$_POST['ent_nom']."', '".$_POST['ent_rue']."', ".$_POST['ent_npa'].", '".$_POST['ent_localite']."', '".$_POST['ent_canton']."', '".$_POST['ent_tel1']."', '".$_POST['ent_tel2']."', '".$_POST['ent_mail']."')";
           $req=$bdd->prepare($ent);
           $req->execute();
       }
       
   }

   if(isset($_GET['eleve'])){
       if($_POST['desa']=="-"){$_POST['desa']="";}
       if($_POST['disp']=="-"){$_POST['disp']="";}
       if($_POST['disp_bt']=="-"){$_POST['disb_bt']="";}
       if($_POST['disp_sport']=="-"){$_POST['disp_sport']="";}
       if($_POST['derog']=="-"){$_POST['derog']="";}
       if($_POST['statut']=="-"){$_POST['statut']="";}
        
       if(isset($_POST['prenom']) AND isset($_POST['nom'])){
           
            $sql='Update t_eleve set  ele_prenom="'.$_POST['prenom'].'", ele_nom="'.$_POST['nom'].'", ele_numeromobile="'.$_POST['tel'].'", ele_desavantage="'.$_POST['desa'].'", ele_dispenseecg="'.$_POST['disp'].'", ele_dispensebt="'.$_POST['disp_bt'].'", ele_dispensesport="'.$_POST['disp_sport'].'", ele_derogation="'.$_POST['derog'].'", ele_statut="'.$_POST['statut'].'" where id_codebarre='.$_GET['stu'].'';
            $req=$bdd->prepare($sql);
            $req->execute();
       }  
    
        if(isset($_POST['choixListe'])){
            
            $sql="update t_eleve set idx_classe =(select id_classe from t_classe where cla_nom='".$_POST['choixListe']."') where id_codebarre='".$_GET['stu']."'";
            $change=$bdd->prepare($sql);
            $change->execute();
        }
    }
    if(isset($_GET['eleve_info'])){
        
        if($_POST['rue']=="-"){$_POST['rue']="";}
        if($_POST['npa']=="-"){$_POST['npa']="";}
        if($_POST['localite']=="-"){$_POST['localite']="";}
        if($_POST['mail']=="-"){$_POST['mail']="";}
        if($_POST['num_mobile']=="-"){$_POST['num_mobile']="";}
        if($_POST['naissance']=="-"){$_POST['naissance']="";}
        
        $sql='Update t_eleve set  ele_Rue="'.$_POST['rue'].'", ele_npa="'.$_POST['npa'].'", ele_localite="'.$_POST['localite'].'", ele_mail="'.$_POST['mail'].'", ele_numeromobile="'.$_POST['num_mobile'].'", ele_datedenaissance="'.$_POST['naissance'].'" where id_codebarre='.$_GET['stu'].'';
        $req=$bdd->prepare($sql);
        $req->execute();
    }

    if(isset($_GET['entreprise'])){
        
        if($_POST['choixEnt']!=""){
            
            $sql="Update t_eleve set idx_entreprise=".$_POST['choixEnt']." where id_codebarre='".$_GET['stu']."'";
            $req=$bdd->prepare($sql);
            $req->execute();
        }else{
            
            $sql='Update t_entreprise set ent_rue="'.$_POST['ent_rue'].'", ent_npa="'.$_POST['ent_npa'].'", ent_localite="'.$_POST['ent_localite'].'", ent_mail="'.$_POST['ent_mail'].'", ent_tel1="'.$_POST['ent_tel1'].'", ent_tel2="'.$_POST['ent_tel2'].'" where id_entreprise='.$_GET['nom'].'';
            $req=$bdd->prepare($sql);
            $req->execute();
        }
       
    }

    if(isset($_GET['maitre'])){
        if($_POST['mai_nom']=="-"){$_POST['mai_nom']="";}
        if($_POST['mai_prenom']=="-"){$_POST['mai_prenom']="";}
        if($_POST['mai_tel1']=="-"){$_POST['mai_tel1']="0";}elseif($_POST['mai_tel1']==""){$_POST['mai_tel1']="0";}
        if($_POST['mai_tel2']=="-"){$_POST['mai_tel2']="0";}elseif($_POST['mai_tel2']==""){$_POST['mai_tel2']="0";}
        if($_POST['mai_mobile']=="-"){$_POST['mai_mobile']="0";}elseif($_POST['mai_mobile']==""){$_POST['mai_mobile']="0";}
        
        if(isset($_POST['choixMai'])){
            $sql="Update t_eleve set idx_maitredapprentissage='".$_POST['choixMai']."' where id_codebarre='".$_GET['stu']."'";
            $req=$bdd->prepare($sql);
            $req->execute();
        }elseif(empty($_POST['mai_nom']) AND empty($_POST['maiprenom'])){
             $sql='Update t_eleve set idx_maitredapprentissage=0 where id_codebarre='.$_GET['stu'].'';
             $req=$bdd->prepare($sql);
             $req->execute();
        }
        $sql1="UPDATE t_maitredapprentissage set mai_tel1='".$_POST['mai_tel1']."', mai_tel2='".$_POST['mai_tel2']."', mai_mobile='".$_POST['mai_mobile']."' where id_maitredapprentissage=(select idx_maitredapprentissage from t_eleve where id_codebarre='".$_GET['stu']."') ";
        $req1=$bdd->prepare($sql1);
        $req1->execute(); 
    }

    if(isset($_GET['representant'])){
        if(isset($_POST['choixRep'])){
            $sql='update t_eleve set idx_representantlegal='.$_POST['choixRep'].' where id_codebarre="'.$_GET['stu'].'"';
            $req=$bdd->prepare($sql);
            $req->execute();
        }else{
                if($_POST['rep_nom']=="-"){$_POST['rep_nom']="";}
                if($_POST['rep_prenom']=="-"){$_POST['rep_prenom']="";}
                if($_POST['rep_rue']=="-"){$_POST['rep_rue']="";}
                if($_POST['rep_npa']=="-"){$_POST['rep_npa']="";}
                if($_POST['rep_localite']=="-"){$_POST['rep_localite']="";}
                if($_POST['rep_tel1']=="-"){$_POST['rep_tel1']="0";}
                if($_POST['rep_tel2']=="-"){$_POST['rep_tel2']="0";}

                $sql='Update t_representantlegal set  rep_nom="'.$_POST['rep_nom'].'",rep_prenom="'.$_POST['rep_prenom'].'", rep_Rue="'.$_POST['rep_rue'].'", rep_tel2="'.$_POST['rep_tel2'].'", rep_localite="'.$_POST['rep_loca'].'", rep_npa='.$_POST['rep_npa'].', rep_tel1="'.$_POST['rep_tel1'].'" where id_representantlegal=(select idx_representantlegal from t_eleve where id_codebarre='.$_GET['stu'].')';
                $req=$bdd->prepare($sql);
                $req->execute();
              }
    }

include_once('../include/mysql.inc.php');
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

    $ent_id=$entry['id_entreprise'];
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


$requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
$requete->execute();
$classe=$requete->fetchAll();

$requete1=$bdd->prepare('SELECT * FROM t_entreprise ORDER BY ent_nom ASC');
$requete1->execute();
$Allentreprise=$requete1->fetchAll();

$sqlm="SELECT * FROM t_maitredapprentissage where idx_entreprise=(SELECT id_entreprise from t_entreprise where ent_nom='".$ent_nom."' LIMIT 1)";
$requete2=$bdd->prepare($sqlm);
$requete2->execute();
$maitre=$requete2->fetchAll();

$requete3=$bdd->prepare('SELECT * FROM t_representantlegal ORDER BY rep_nom ASC');
$requete3->execute();
$representant=$requete3->fetchAll();
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
                            <form method="POST" action="?eleve=oui&stu=<?php echo $_GET['stu']; ?>">
                                    <tr>
                                        <td style="font-weight: bold">Prénom Nom:</td>
                                        <td><input type="text" name="prenom" value='<?php echo "$ele_prenom"; ?>'/>&nbsp;&nbsp;<input type="text" name="nom" value='<?php echo "$ele_nom"; ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Classe:</td>
                                        <td>
                                            <?php if($cla_nom==""){echo "-";}else{echo "$cla_nom";} ?>
                                            <select class="dropdown form-control" name="choixListe">
                                                <option value="<?php echo $cla_nom; ?>"></option>
                                                <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['cla_nom'].'">'.$classe[$a]['cla_nom'].'</option>'; ?> 
                                            </select> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Téléphone:</td>
                                        <td><input type="text" name="tel" value='<?php if($ele_telephone=="" || $ele_telephone==0){echo "-";}else{echo "$ele_telephone";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Désavantage:</td>
                                        <td><input type="text" name="desa" value='<?php if($ele_desavantage==""){echo "-";}else{echo "$ele_desavantage";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Dispense ECG:</td>
                                        <td><input type="text" name="disp" value='<?php if($ele_dispenseecg==""){echo "-";}else{echo "$ele_dispenseecg";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Dispense BT:</td>
                                        <td><input type="text" name="disp_bt" value='<?php if($ele_dispensebt==""){echo "-";}else{echo "$ele_dispensebt";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Dispense Sport:</td>
                                        <td><input type="text" name="disp_sport" value='<?php if($ele_dispensesport==""){echo "-";}else{echo "$ele_dispensesport";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Dérogation:</td>
                                        <td><input type="text" name="derog" value='<?php if($ele_derogation==""){echo "-";}else{echo "$ele_derogation";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Elève statut:</td>
                                        <td><input type="text" name="statut" value='<?php if($ele_statut==""){echo "-";}else{echo "$ele_statut";} ?>'></td>
                                    </tr>
                                    <!-- Fin de : Informations de base -->
                                    <tr>
                                        <td><input type="submit" class="btn btn-primary btn-sm" value="editer"></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajout_eleve">
                                                Ajouter un élève
                                            </button>
                                        </td>
                                    </tr>
                            </form>        
                                
                        </table>

                        <!-- Informations eleve -->
                        <button class="accordion">Plus...</button>
                        <div class="panel">
                            <table class="table table-striped">
                                <form method="post" action="?eleve_info=oui&stu=<?php echo $_GET['stu']; ?>">
                                    <tr>
                                        <td style="font-weight: bold">Rue:</td>
                                        <td><input type="text" name="rue" value='<?php if($ele_rue==""){echo "-";}else{echo "$ele_rue";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">NPA, Localité:</td>
                                        <td><input type="text" name="npa" value='<?php echo "$ele_npa" ?>'>&nbsp;&nbsp;<input type="text" name="localite" value='<?php echo "$ele_localite" ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Mail:</td>
                                        <td><input type="text" name="mail" value='<?php if($ele_mail==""){echo "-";}else{echo $ele_mail;} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Numéro mobile:</td>
                                        <td><input type="text" name="num_mobile" value='<?php if($ele_numeromobile==""){echo "-";}else{echo "$ele_numeromobile";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Date de naissance:</td>
                                        <td><input type="text" name="naissance" value='<?php if($ele_datedenaissance==""){echo "-";}else{echo "$ele_datedenaissance $ele_majeur";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td><input type="submit" class="btn btn-primary btn-sm" value="editer"></td>
                                    </tr>
                                </form>
                            </table>
                        </div>
                        <!-- Fin de : Informations eleve -->

                        <!-- Informations Employeur -->
                        <button class="accordion">Entreprise</button>
                        <div class="panel">
                            <table class="table table-striped">
                                <form method="POST" action="?entreprise=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $ent_id; ?>">
                                <tr>
                                    <td style="font-weight: bold">Raison sociale:</td>
                                    <td>
                                            <?php if($ent_nom==""){echo "-";}else{echo "$ent_nom";} ?>
                                            <select class="dropdown form-control" name="choixEnt">
                                                <option value="<?php $ent_nom; ?>"></option>
                                                <?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
                                            </select> 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Rue:</td>
                                    <td><input type="text" name="ent_rue" value='<?php if($ent_rue==""){echo "-";}else{echo "$ent_rue";} ?>'></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">NPA, Localité:</td>
                                    <td><input type="text" name="ent_npa" value='<?php echo "$ent_npa" ?>'>&nbsp;&nbsp;<input type="text" name="ent_localite" value='<?php echo "$ent_localite" ?>'></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Mail:</td>
                                    <td><?php if($ent_mail==""){?>
                                        <input type="text" name="ent_mail" value="-">
                                        <?php } else{ ?>
                                        <input type="text" name="ent_mail" value="<?php echo $ent_mail;?>">
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel1:</td>
                                    <td><input type="text" name="ent_tel1" value='<?php if($ent_tel1==""){echo "-";}else{echo "$ent_tel1";} ?>'></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel2:</td>
                                    <td><input type="text" name="ent_tel2" value='<?php if($ent_tel2==""){echo "-";}else{echo "$ent_tel2";} ?>'></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" class="btn btn-primary btn-sm" value="editer" float="right"></td>
                                    <td><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajout_ent">
                                                Ajouter une entreprise
                                        </button>
                                    </td>
                                </tr>
                                </form>
                            </table>
                        </div>
                        <!-- Fin de : Informations Employeur -->

                        <!-- Informations Maître d'apprentissage -->
                        <button class="accordion">Maître d'apprentissage</button>
                        <div class="panel">
                            <table class="table table-striped">
                                <form method="POST" action="?maitre=oui&stu=<?php echo $_GET['stu']; ?>">
                                     <?php if(isset($mai_nom)){ ?>
                                    <tr>
                                        <td style="font-weight: bold">Nom, prenom:</td>
                                        <td><input type="text" name="mai_nom" value='<?php echo "$mai_nom" ?>'>&nbsp;&nbsp;<input type="text" name="mai_prenom" value='<?php echo "$mai_prenom" ?>'></td>
                                    </tr> <?php }else{ ?>
                                    <tr>
                                        <td style="font-weight: bold">Nom, prenom:</td>
                                        <td>
                                            <select class="dropdown form-control" name="choixMai">
                                            <option></option>
                                            <?php for($a=0;$a<count($maitre);$a++)echo'<option value="'.$maitre[$a]['id_maitredapprentissage'].'">'.$maitre[$a]['mai_nom'].' '.$maitre[$a]['mai_prenom'].'</option>'; ?> 
                                            </select>
                                        </td> 
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold">Tel1:</td>
                                        <td><input type="text" name="mai_tel1" value='<?php if($mai_tel1=="0" or $mai_tel1==""){echo "-";}else{echo "$mai_tel1";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Tel2:</td>
                                        <td><input type="text" name="mai_tel2" value='<?php if($mai_tel2=="0" or $mai_tel2==""){echo "-";}else{echo "$mai_tel2";} ?>'></td>
                                    </tr>
                                    <tr>
                                    <td style="font-weight: bold">Mobile:</td>
                                        <td><input type="text" name="mai_mobile" value='<?php if($mai_mobile=="0" or $mai_mobile==""){echo "-";}else{echo "$mai_mobile";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td><input type="submit" class="btn btn-primary btn-sm" value="editer" float="right"></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajout_maitre">
                                                Ajouter maître d'apprentissage
                                            </button>
                                        </td>
                                    </tr>
                                </form>
                            </table>
                        </div>
                        <!-- Fin de : Informations Maître d'apprentissage -->

                        <!-- Informations Représentant légal -->
                        <button class="accordion">Représentant légal</button>
                        <div class="panel">
                            <table class="table table-striped">
                                <form method="post" action="?representant=oui&stu=<?php echo $_GET['stu']; ?>">
                                   <?php if($rep_prenom!=""){ ?>
                                    <tr>
                                        <td style="font-weight: bold">Nom, prenom:</td>
                                        <td><input type="text" name="rep_nom" value='<?php echo "$rep_nom" ?>'>&nbsp;&nbsp;<input type="text" name="rep_prenom" value='<?php echo "$rep_prenom" ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Rue:</td>
                                        <td><input type="text" name="rep_rue" value='<?php if($rep_rue==""){echo "-";}else{echo "$rep_rue";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">NPA, Localité:</td>
                                        <td><input type="text" name="rep_npa" value='<?php echo "$rep_npa" ?>'>&nbsp;&nbsp;<input type="text" name="rep_loca" value='<?php echo "$rep_localite" ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Tel1</td>
                                        <td><input type="text" name="rep_tel1" value='<?php if($rep_tel1=="0"){echo "-";}else{echo "$rep_tel1";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Tel2</td>
                                        <td><input type="text" name="rep_tel2" value='<?php if($rep_tel2=="0"){echo "-";}else{echo "$rep_tel2";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Mobile:</td>
                                        <td><input type="text" name="rep_mobil" value='<?php if($rep_mobile=="0"){echo "-";}else{echo "$rep_mobile";} ?>'></td>
                                    </tr>
                                    <tr>
                                        <td><input type="submit" class="btn btn-primary btn-sm" value="editer" float="right"></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
                                                Ajouter un représentant
                                            </button>
                                        </td>
                                    </tr>
                                   <?php }else{ ?>
                                    <tr>
                                        <td style="font-weight: bold">Nom, prenom:</td>
                                        <td>
                                            <select class="dropdown form-control" name="choixRep">
                                            <option></option>
                                            <?php for($a=0;$a<count($representant);$a++)echo'<option value="'.$representant[$a]['id_representantlegal'].'">'.$representant[$a]['rep_nom'].' '.$representant[$a]['rep_prenom'].'</option>'; ?> 
                                            </select>
                                        </td>
                                        <td><input type="submit" class="btn btn-primary btn-sm" value="editer" float="right"></td>
                                    </tr>
                                    <tr>
                                         <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
                                                Ajouter un représentant
                                            </button>
                                        </td>
                                    </tr>
                                  <?php } ?>
                                </form>
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

                            </form>
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

                            </form>
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
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->                                
           <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Ajouter un représentant</h4>
                  </div>
                  <div class="modal-body">
                      <form name="rep" method="post" action="?ajout=rep&stu=<?php echo $_GET['stu']; ?>">
                    <table class="table table-striped">
                        
                            <tr>
                                <td style="font-weight: bold">Nom, prenom:</td>
                                <td><input type="text" name="rep_nom" />&nbsp;&nbsp;<input type="text" name="rep_prenom" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="dropdown form-control" name="politesse">
                                       <option value="Monsieur">Monsieur</option>
                                       <option value="Madame">Madame</option>
                                    </select>
                                </td>
                            </tr>
                             <tr>
                                 <td style="font-weight: bold">Rue:</td>
                                 <td><input type="text" name="rep_rue" /></td>
                             </tr>
                             <tr>
                                 <td style="font-weight: bold">NPA, Localité:</td>
                                 <td><input type="text" name="rep_npa" />&nbsp;&nbsp;<input type="text" name="rep_loca" /></td>
                             </tr>
                             <tr>
                                 <td style="font-weight: bold">Tel1</td>
                                 <td><input type="text" name="rep_tel1" /></td>
                             </tr>
                             <tr>
                                 <td style="font-weight: bold">Tel2</td>
                                 <td><input type="text" name="rep_tel2" /></td>
                             </tr>
                             <tr>
                                 <td style="font-weight: bold">Mobile:</td>
                                 <td><input type="text" name="rep_mobile" /></td>
                             </tr>
                             <tr>
                                 <td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter"/></td>
                             </tr>
                        
                    </table>
                            </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                  </div>
                </div>
              </div>
           </div> 
                                
           <div class="modal fade" id="ajout_eleve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
             <div class="modal-dialog" role="document">
               <div class="modal-content">
                 <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                   </button>
                   <h4 class="modal-title" id="myModalLabel">Ajouter un élève</h4>
                 </div>
                 <div class="modal-body">
                   <table class="table table-striped">
                       <form method="post" action="?ajout=eleve&stu=<?php echo $_GET['stu']; ?>">
                           <tr>
                               <td style="font-weight: bold">Prénom Nom:</td>
                               <td><input type="text" name="ele_prenom">&nbsp;&nbsp;<input type="text" name="ele_nom">&nbsp;&nbsp;<select class="dropdown form-control" name="politesse">
                                       <option value="Monsieur">Monsieur</option>
                                       <option value="Madame">Madame</option>
                                   </select></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Code barre</td>
                               <td><input type="text" name="id_codebarre"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Classe:</td>
                               <td>
                                   <select class="dropdown form-control" name="choixListe">
                                       <option></option>
                                       <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?> 
                                   </select> 
                               </td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Entreprise:</td>
                               <td>
                                   <select class="dropdown form-control" name="choixEntreprise">
                                       <option></option>
                                       <?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
                                   </select> 
                               </td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Téléphone:</td>
                               <td><input type="text" name="ele_numeromobile"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Désavantage:</td>
                               <td><input type="text" name="ele_desa"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Dispense ECG:</td>
                               <td><input type="text" name="ele_disp"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Dispense BT:</td>
                               <td><input type="text" name="ele_disp_bt"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Dispense Sport:</td>
                               <td><input type="text" name="ele_disp_sport"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Dérogation:</td>
                               <td><input type="text" name="ele_derog"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Elève statut:</td>
                               <td><input type="text" name="ele_statut"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Rue:</td>
                               <td><input type="text" name="ele_rue"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">NPA, Localité:</td>
                                   <td><input type="text" name="ele_npa">&nbsp;&nbsp;<input type="text" name="ele_localite"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Canton:</td>
                                   <td><input type="text" name="ele_canton"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Mail:</td>
                                   <td><input type="text" name="ele_mail"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Date de naissance:</td>
                                   <td><input type="date" name="ele_datedenaissance"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Majeurs / mineur</td>
                                   <td>
                                   <select class="dropdown form-control" name="choixAge">
                                       <option value="Majeur-e">Majeur-e</option>
                                       <option value="Mineur-e">Mineur-e</option>
                                   </select>
                                   </td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Numéro de contrat:</td>
                                   <td><input type="text" name="ele_numerodecontrat"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Debut de formation:</td>
                                   <td><input type="date" name="ele_debutdeformation"></td>
                               </tr>
                               <tr>
                                   <td style="font-weight: bold">Fin de formation:</td>
                                   <td><input type="date" name="ele_findeformation"></td>
                               </tr>
                           <tr>
                               <td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter"></td>
                           </tr>
                    </form>        
                   </table>
                 </div>
                 <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                 </div>
               </div>
             </div>
          </div>
                                
          <div class="modal fade" id="ajout_maitre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Ajouter un maître d'apprentissage</h4>
                  </div>
                  <div class="modal-body">
                    <table class="table table-striped">
                        <form method="POST" action="?ajout=maitre&stu=<?php echo $_GET['stu']; ?>">
                           <tr>
                               <td style="font-weight: bold">Nom, prenom:</td>
                               <td><input type="text" name="mai_nom">&nbsp;&nbsp;<input type="text" name="mai_prenom"></td>
                           </tr> 
                           <tr>
                               <td style="font-weight: bold">Tel1:</td>
                               <td><input type="text" name="mai_tel1"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Tel2:</td>
                               <td><input type="text" name="mai_tel2"></td>
                           </tr>
                           <tr>
                           <td style="font-weight: bold">Mobile:</td>
                               <td><input type="text" name="mai_mobile"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Entreprise</td>
                               <td><select class="dropdown form-control" name="choixEntreprise">
                                    <option></option>
                                    <?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
                               </select></td>
                           </tr>
                           <tr>
                               <td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter" float="right"></td>
                           </tr>
                        </form>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                  </div>
                </div>
              </div>
           </div>
                        
           <div class="modal fade" id="ajout_ent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Ajouter une entreprise</h4>
                  </div>
                  <div class="modal-body">
                    <table class="table table-striped">
                        <form method="POST" action="?ajout=ent&stu=<?php echo $_GET['stu']; ?>">
                           <tr>
                               <td style="font-weight: bold">Nom entreprise</td>
                               <td><input type="text" name="ent_nom"></td>
                           </tr> 
                           <tr>
                               <td style="font-weight: bold">Rue:</td>
                               <td><input type="text" name="ent_rue"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">NPA, Localité:</td>
                               <td><input type="text" name="ent_npa">&nbsp;&nbsp;<input type="text" name="ent_localite"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Canton:</td>
                               <td><input type="text" name="ent_canton"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Mail:</td>
                               <td><input type="text" name="ent_mail"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Tel1</td>
                               <td><input type="text" name="ent_tel1"></td>
                           </tr>
                            <tr>
                               <td style="font-weight: bold">Tel2</td>
                               <td><input type="text" name="ent_tel2"></td>
                           </tr>
                           <tr>
                               <td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter" float="right"></td>
                           </tr>
                        </form>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                  </div>
                </div>
              </div>
           </div>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
        <!-- Code JS Affichage des tableaux Absences - Arrivées tardives - Mises à la porte -->
        <script>
            $('#myModal').on('shown.bs.modal', function () {
              $('#myInput').focus()
            })
        </script>
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
