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
// Auteur : Toi-même tu sais ...
// Raison : PARCE QUE CA MARCHAIT PAS (string avec spécial char)
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





           if (isset($_FILES['avatar']) )
           {

               if (isset($_FILES['avatar']) AND $_FILES['avatar']['error'] == 0)
               {
                   // Testons si le fichier n'est pas trop gros
                   if ($_FILES['avatar']['size'] <= 1000000)
                   {
                       // Testons si l'extension est autorisée
                       $infosfichier = pathinfo($_FILES['avatar']['name']);
                       $extension_upload = $infosfichier['type'];
                       $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                       if (in_array($extension_upload, $extensions_autorisees))
                       {
                           // On peut valider le fichier et le stocker définitivement
                           move_uploaded_file($_FILES['avatar']['tmp_name'], 'images/utilisateurs' . basename($_FILES['avatar']['name']));
                           echo "L'envoi a bien été effectué !";
                       }
                   }
               }

               /*         function upload($index,$destination,$maxsize=FALSE,$extensions=FALSE)
                        {
                            //Test1: fichier correctement uploadé
                            if (!isset($_FILES[$index]) OR $_FILES[$index]['error'] > 0) return FALSE;
                            //Test2: taille limite
                            if ($maxsize !== FALSE AND $_FILES[$index]['size'] > $maxsize) return FALSE;
                            //Test3: extension
                            $ext = substr(strrchr($_FILES[$index]['name'],'.'),1);
                            if ($extensions !== FALSE AND !in_array($ext,$extensions)) return FALSE;
                            //Déplacement
                            return move_uploaded_file($_FILES[$index]['tmp_name'],$destination);
                        }

         //EXEMPLES
                        $upload1 = upload('icone','uploads/monicone1',15360, array('png','gif','jpg','jpeg') );
                        $upload2 = upload('mon_fichier','uploads/file112',1048576, FALSE );

                        if ($upload1) "Upload de l'icone réussi!<br />";
                        if ($upload2) "Upload du fichier réussi!<br />";
                     /*   $dossier = '/images/utilisateurs/1';
                        $fichier = basename($_FILES['avatar']['name']);
                        $taille_maxi = 100000;
                        $taille = filesize($_FILES['avatar']['tmp_name']);
                        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
                        $extension = strrchr($_FILES['avatar']['name'], '.');
         //Début des vérifications de sécurité...
                        if (!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                        {
                            $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg';
                        }
                        if ($taille > $taille_maxi) {
                            $erreur = 'Le fichier est trop gros...';
                        }
                        if (!isset($erreur)) //S'il n'y a pas d'erreur, on upload
                        {
                            //On formate le nom du fichier ici...
                            $fichier = strtr($fichier,
                                'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
                                'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                            $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
                            {
                                echo 'Upload effectué avec succès !';
                            } else //Sinon (la fonction renvoie FALSE).
                            {
                                echo 'Echec de l\'upload !';
                            }
                        } else {
                            echo $erreur;
                        }
         */

           }

          if($_POST['ele_npa']==""){$_POST['ele_npa']=0;}
          if($_POST['ele_datedenaissance']==""){$_POST['ele_datedenaissance']=00-00-0000;}
          if($_POST['ele_debutdeformation']==""){$_POST['ele_debutdeformation']=00-00-0000;}
          if($_POST['ele_findeformation']==""){$_POST['ele_findeformation']=00-00-0000;}

           //
           if (isset($_FILES['rep_nom']) ) {

               $repins = "INSERT INTO `t_representantlegal` SET 
                          rep_nom = :rep_nom,
                          rep_prenom = :rep_prenom,
                          rep_politesse = :rep_politesse,
                          rep_Rue = :rep_Rue,
                          rep_npa = :rep_npa,
                          rep_localite = :rep_localite,
                          rep_tel1 = :rep_tel1,
                          rep_tel2 = :rep_tel2,
                          rep_numeromobile = :rep_numeromobile";
               $params_repins = array(
                        ':rep_nom' => $_POST['rep_nom'],
                        ':rep_prenom' => $_POST['rep_prenom'],
                        ':rep_politesse' => $_POST['rep_politesse'],
                        ':rep_Rue' => $_POST['rep_Rue'],
                        ':rep_npa' => $_POST['rep_npa'],
                        ':rep_localite' => $_POST['rep_localite'],
                        ':rep_tel1' => $_POST['rep_tel1'],
                        ':rep_tel2' =>  $_POST['rep_tel2'],
                        ':rep_numeromobile' => $_POST['rep_numeromobile']

               );

               $req=$bdd->prepare($repins);
               $req->execute($params_repins);


               $requetelastrep=$bdd->prepare("select max(id_representantlegal)+1 as 'max' from t_representantlegal");
               $requetelastrep->execute();
               $lastrep=$requetelastrep->fetchAll();
               $lastrepr= $lastrepr[0]['max'];
           }
           else
           {
               $lastrepr=0;
           }

//	   $eleve="INSeRT INTO t_eleve (ele_nom, ele_prenom, ele_politesse, id_codebarre, ele_datedenaissance, ele_numerodecontrat, ele_debutdeformation, ele_findeformation, ele_majeur, ele_Rue, ele_npa, ele_localite, ele_canton, ele_numeromobile, ele_mail, ele_desavantage, ele_dispenseecg, ele_dispensebt, ele_dispensesport, ele_derogation, ele_statut, idx_classe, idx_entreprise ) values ('".$_POST['ele_nom']."', '".$_POST['ele_prenom']."', '".$_POST['politesse']."', '".$_POST['id_codebarre']."', ".$_POST['ele_datedenaissance'].", '".$_POST['ele_numerodecontrat']."', ".$_POST['ele_debutdeformation'].", ".$_POST['ele_findeformation'].", '".$_POST['choixAge']."', '".$_POST['ele_rue']."', ".$_POST['ele_npa'].", '".$_POST['ele_localite']."', '".$_POST['ele_canton']."', '".$_POST['ele_numeromobile']."', '".$_POST['ele_mail']."', '".$_POST['ele_desa']."', '".$_POST['ele_disp']."', '".$_POST['ele_dispbt']."', '".$_POST['ele_disp_sport']."', '".$_POST['ele_derog']."', '".$_POST['ele_statut']."', ".$_POST['choixListe'].", ".$_POST['choixEntreprise'].")";

$eleve = "INSERT INTO `t_eleve` SET
          id_codebarre = :id_codebarre,
          ele_nom = :ele_nom,
          ele_prenom = :ele_prenom,
          ele_politesse = :ele_politesse,
          ele_datedenaissance = :ele_datedenaissance,
          ele_numerodecontrat = :ele_numerodecontrat,
          ele_debutdeformation = :ele_debutdeformation,
          ele_findeformation = :ele_findeformation,
          ele_majeur = :ele_majeur,
          ele_rue = :ele_rue,
          ele_npa = :ele_npa,
          ele_localite = :ele_localite,
          ele_canton = :ele_canton,
          ele_numeromobile = :ele_numeromobile,
          ele_mail = :ele_mail,
          ele_desavantage = :ele_desavantage,
          ele_dispenseecg = :ele_dispenseecg,
          ele_dispensebt = :ele_dispensebt,
          ele_dispensesport = :ele_dispensesport,
          ele_derogation = :ele_derogation,
          ele_statut = :ele_statut,
          idx_classe = :idx_classe,
          idx_entreprise = :idx_entreprise,
          idx_maitredapprentissage = :idx_maitredapprentissage,
          idx_representantlegal = :idx_representantlegal";


$params = array(
   ':id_codebarre' => $_POST['id_codebarre'],
   ':ele_nom' => $_POST['ele_nom'],
   ':ele_prenom' => $_POST['ele_prenom'],
   ':ele_politesse' => $_POST['ele_politesse'],
   ':ele_datedenaissance' => date('Y-m-d',strtotime($_POST['ele_datedenaissance'])),
   ':ele_numerodecontrat' => $_POST['ele_numerodecontrat'],
   ':ele_debutdeformation' => date('Y-m-d',strtotime($_POST['ele_debutdeformation'])),
   ':ele_findeformation' => date('Y-m-d',strtotime($_POST['ele_findeformation'])),
   ':ele_majeur' => '',
   ':ele_rue' => $_POST['ele_rue'],
   ':ele_npa' => $_POST['ele_npa'],
   ':ele_localite' => $_POST['ele_localite'],
   ':ele_canton' => $_POST['ele_canton'],
   ':ele_numeromobile' =>  $_POST['ele_numeromobile'],
   ':ele_mail' => $_POST['ele_mail'],
   ':ele_desavantage' => $_POST['ele_desavantage'],
   ':ele_dispenseecg' => '',
   ':ele_dispensebt' => '',
   ':ele_dispensesport' => '',
   ':ele_derogation' => $_POST['ele_derogation'],
   ':ele_statut' => $_POST['ele_statut'],
   ':idx_classe' => $_POST['idx_classe'],
   ':idx_entreprise' => $_POST['idx_entreprise'],
   ':idx_maitredapprentissage' => $_POST['idx_maitredapprentissage'],
   ':idx_representantlegal' => $lastrepr);

          $req_eleve=$bdd->prepare($eleve);
          $req_eleve->execute($params);

        //  $sport="INSERT INTO t_sporthiver (id_eleve, codeBarre) select id_eleve, id_codebarre from t_eleve where id_codebarre='".$_POST['id_codebarre']."'";
       //   $req1=$bdd->prepare($sport);
       //   $req1->execute();
          
       }
       // ajout d'un nouveau representant

       // ajout d'un nouveau maitre d'apprentissage
       if($_GET['ajout']=="maitre"){
           
           $maitre="INSERT INTO t_maitredapprentissage SET 
                    mai_nom = :mai_nom,
                    mai_prenom = :mai_prenom,
                    mai_tel1 = :mai_tel1,
                    mai_tel2 = :mai_tel2, 
                    mai_mobile = :mai_mobile, 
                    idx_entreprise = :idx_entreprise";
                     
           $params_maitre = array(
                ':mai_nom' => $_POST['mai_nom'],
                ':mai_prenom' => $_POST['mai_prenom'],
                ':mai_tel1' => $_POST['mai_tel1'],
                ':mai_tel2' => $_POST['mai_tel2'],
                ':mai_mobile' => $_POST['mai_mobile'],
                ':idx_entreprise' => $_POST['choixEntreprise']);

           $req_maitres=$bdd->prepare($maitre);
           $req_maitres->execute($params_maitre);
       }
       
       if($_GET['ajout']=="ent"){

           $ent="INSERT INTO t_entreprise SET
                ent_nom = :ent_nom, 
                ent_rue = :ent_rue, 
                ent_npa = :ent_npa, 
                ent_localite = :ent_localite, 
                ent_canton = :ent_canton, 
                ent_tel1 = :ent_tell, 
                ent_tel2 = :ent_tel2, 
                ent_mail = :ent_mail";


           $params_ent = array(
                   ':ent_nom' => $_POST['ent_nom'] ,
                   ':ent_rue' => $_POST['ent_rue'] ,
                   ':ent_npa' => $_POST['ent_npa'],
                   ':ent_localite' => $_POST['ent_localite'] ,
                   ':ent_canton' => $_POST['ent_canton'] ,
                   ':ent_tell' => $_POST['ent_tel1'] ,
                   ':ent_tel2' => $_POST['ent_tel2'] ,
                   ':ent_mail' => $_POST['ent_mail'] );

           try {
           $req_ent=$bdd->prepare($ent);
           $req_ent->execute($params_ent);

           }
           catch(Exception $e) {
               trigger_error($e->getMessage(), E_USER_ERROR);
           }
       }
       
   }

   if(isset($_GET['eleve'])){
       if($_POST['desa']=="-"){$_POST['desa']="";}
       if($_POST['disp']=="-"){$_POST['disp']="";}
       if($_POST['disp_bt']=="-"){$_POST['disb_bt']="";}
       if($_POST['disp_sport']=="-"){$_POST['disp_sport']="";}
       if($_POST['derog']=="-"){$_POST['derog']="";}
       if($_POST['statut']=="-"){$_POST['statut']="";}

   }
    if(isset($_GET['eleve_info'])){
        
        if($_POST['rue']=="-"){$_POST['rue']="";}
        if($_POST['npa']=="-"){$_POST['npa']="";}
        if($_POST['localite']=="-"){$_POST['localite']="";}
        if($_POST['mail']=="-"){$_POST['mail']="";}
        if($_POST['num_mobile']=="-"){$_POST['num_mobile']="";}
        if($_POST['naissance']=="-"){$_POST['naissance']="";}
        //Insert into eleve
      //  $sql='Update t_eleve set  ele_Rue="'.$_POST['rue'].'", ele_npa="'.$_POST['npa'].'", ele_localite="'.$_POST['localite'].'", ele_mail="'.$_POST['mail'].'", ele_numeromobile="'.$_POST['num_mobile'].'", ele_datedenaissance="'.$_POST['naissance'].'" where id_codebarre='.$_GET['stu'].'';
        $req=$bdd->prepare($sql);
        $req->execute();
    }



    if(isset($_GET['maitre'])) {
        if ($_POST['mai_nom'] == "-") {
            $_POST['mai_nom'] = "";
        }
        if ($_POST['mai_prenom'] == "-") {
            $_POST['mai_prenom'] = "";
        }
        if ($_POST['mai_tel1'] == "-") {
            $_POST['mai_tel1'] = "0";
        } elseif ($_POST['mai_tel1'] == "") {
            $_POST['mai_tel1'] = "0";
        }
        if ($_POST['mai_tel2'] == "-") {
            $_POST['mai_tel2'] = "0";
        } elseif ($_POST['mai_tel2'] == "") {
            $_POST['mai_tel2'] = "0";
        }
        if ($_POST['mai_mobile'] == "-") {
            $_POST['mai_mobile'] = "0";
        } elseif ($_POST['mai_mobile'] == "") {
            $_POST['mai_mobile'] = "0";
        }


        if ($_POST['rep_nom'] == "-") {
            $_POST['rep_nom'] = "";
        }
        if ($_POST['rep_prenom'] == "-") {
            $_POST['rep_prenom'] = "";
        }
        if ($_POST['rep_rue'] == "-") {
            $_POST['rep_rue'] = "";
        }
        if ($_POST['rep_npa'] == "-") {
            $_POST['rep_npa'] = "";
        }
        if ($_POST['rep_localite'] == "-") {
            $_POST['rep_localite'] = "";
        }
        if ($_POST['rep_tel1'] == "-") {
            $_POST['rep_tel1'] = "0";
        }
        if ($_POST['rep_tel2'] == "-") {
            $_POST['rep_tel2'] = "0";
        }


    }

include_once('../include/mysql.inc.php');
//Va cherhcer les informations de l'élève
$bd=new dbIfc();
//$tabStudent=$bd->GetStudent($_GET['stu']);
unset($bd);


$alreadydone=false;
//Traitement des informations de l'élève



$requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
$requete->execute();
$classe=$requete->fetchAll();

$requete1=$bdd->prepare('SELECT * FROM t_entreprise ORDER BY ent_nom ASC');
$requete1->execute();
$Allentreprise=$requete1->fetchAll();

$sqlm="SELECT * FROM t_maitredapprentissage";
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
                   
                    <!-- Fin  de : Titre -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <p><?php
                            //Affichage de la photo
//                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                  //   $filename2 = "images/utilisateurs/.JPG";




                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";





                            ?>
                        </p>



                  
						
						
						
						
						
						
						
						
						
						
						
						</div>
<div class="jquery-script-clear"></div>
</div>
</div>
 <h1 class="page-header">Nouvel Elève</h1>
<div class="content">
<div class="idealsteps-container">
<nav class="idealsteps-nav"></nav>
<form action="?ajout=eleve" method="POST" enctype="multipart/form-data" class="idealforms">
<div class="idealsteps-wrap">

<!-- Step 1 -->

<section class="idealsteps-step">
<h1 >Informations Administratives</h1>
<div class="field">
 <label class="main">Civilité:</label>
  
  
                                            <select class="dropdown form-control" name="ele_politesse">
                                                <option value="Monsieur">Monsieur</option>
                                                <option value="Madame">Madame</option>
                                            </select> 
 </div>
<div class="field">
<label class="main">Nom:</label>
<input type="text" name="ele_nom" value='' title="">
 </div>
<div class="field">
<label class="main">Prénom:</label>
<input type="text" name="ele_prenom" value=''/>
 </div>
 <div class="field">
<label class="main">Numéro Contrat:</label>
<input type="text" name="ele_numerodecontrat" value=''/>
 </div>

  

<div class="field">
        <label class="main">Date de naissance:</label>
        <input name="ele_datedenaissance" type="text" placeholder="jj.mm.aaaa" class="date-picker" value=''>

        <span class="error"></span> </div>
		
  <div class="field">
        <label class="main">Date debut Formation:</label>
        <input name="ele_debutdeformation" type="text" placeholder="jj.mm.aaaa" class="date-picker" value=''>
        <span class="error"></span> </div>
  <div class="field">
        <label class="main">Date Fin Formation:</label>
        <input name="ele_findeformation" type="text" placeholder="jj.mm.aaaa" class="date-picker" value=''>
        <span class="error"></span> </div>
		
		
    <div class="field">
        <label class="main">Numéro mobile:</label>
        <input type="text" name="ele_numeromobile" value=''>
    </div>
    <div class="field">
    <label class="main">Classe:</label>
  
  
                                            <select class="dropdown form-control" name="idx_classe">
                                                <option value=""></option>
                                                <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?> 
                                            </select> 
 </div>


    <div class="field">
        <label class="main">Code barre:</label>
        <input type="text" name="id_codebarre" value=''>
    </div>

<div class="field">
<label class="main">Désavantage:</label>
<input type="text" name="ele_desavantage" value=''>
 </div>
 <div class="field">
<label class="main">Dérogation:</label>
<input type="text" name="ele_derogation" value=''>
 </div>
<div class="field">
<label class="main">Photo:</label>
<input  name="avatar" type="file"  value=''>
    <input type="hidden" name="MAX_FILE_SIZE" value="12345" />

</div>
 <div class="field">
<label class="main">Statut Eleve:</label>
<input type="text" name="ele_statut" value=''>
 </div>
 
 <div class="field">
<label class="main">Rue:</label>
<input type="text" name="ele_rue" value=''>
 </div>
 
 <div class="field">
<label class="main">NPA:</label>
<input type="text" name="ele_npa" value=''>&nbsp;&nbsp;
 </div>
 
 <div class="field">
<label class="main">Localité:</label>
 <input type="text" name="ele_localite" value=''>
 </div>
 <div class="field">
<label class="main">Canton:</label>
 <input type="text" name="ele_canton" value=''>
 </div>
 
 <div class="field">
 <label class="main">E-Mail:</label>
<input type="text" name="ele_mail" value=''>
<span class="error"></span> </div>





 

 
 
 
<div class="field buttons">
<label class="main">&nbsp;</label>
<button type="button" class="next">Suivant &raquo;</button>
</div>
</section>

<!-- Step 2 -->

<section class="idealsteps-step">
<div class="field" >



 <div class="field">      
	  <label class="main">Raison sociale:</label>
                                    
                                     
                                        
                                            <select class="dropdown form-control" name="idx_entreprise">
                                                <option value=""></option>
                                                <?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
                                            </select> 
                                </div>
    <div class="field">

    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#ajout_ent">
                                                Ajouter une entreprise
                                        </button>
                                  </div>
<div class="field buttons">
<label class="main">&nbsp;</label>
<button type="button" class="Précédent">&laquo; Précédent</button>
<button type="button" class="next">Suivant &raquo;</button>
</div>
</section>

<!-- Step 3 -->

<section class="idealsteps-step">
    <h1>Maître d'apprentissage</h1>
 <div class="field">
   <label class="main">Nom et prénom:</label>

                                            <select class="dropdown form-control" name="idx_maitredapprentissage">
                                            <option></option>
                                            <?php for($a=0;$a<count($maitre);$a++)echo'<option value="'.$maitre[$a]['id_maitredapprentissage'].'">'.$maitre[$a]['mai_nom'].' '.$maitre[$a]['mai_prenom'].'</option>'; ?> 
                                            </select>
                                       

									 	</div>

 <div class="field">
                                   
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajout_maitre">
                                                Ajouter maître d'apprentissage
                                            </button>
                                       	</div>
    <div class="field buttons">
        <label class="main">&nbsp;</label>
        <button type="button" class="prev">&laquo; Précédent</button>
        <button type="button" class="next">Suivant &raquo;</button>
    </div>
</section>
    <!--  Step 4 -->

    <section class="idealsteps-step">
        <h1>Représentant Légal</h1>
        <div class="field">
		<label class="main">Civilité:</label>
               <select class="dropdown form-control" name="rep_politesse">
                                                <option value="Monsieur">Monsieur</option>
                                                <option value="Madame">Madame</option>
                                            </select> 
     
            </div>

            <div class="field">
                <label class="main">Nom:</label>
               <input type="text" name="rep_nom">
            </div>
            <div class="field">
                <label class="main">Prénom:</label>
                <input type="text" name="rep_prenom">
            </div>
                    <div class="field">
                <label class="main">Rue:</label>
                <input type="text" name="rep_Rue">
            </div>

            <div class="field">
                <label class="main">NPA:</label>
                <input type="text" name="rep_npa">
            </div>
            <div class="field">
                <label class="main">Localité:</label>
                <input type="text" name="rep_localite">
            </div>

            <div class="field">
                <label class="main">Tel1:</label>
                <input type="text" name="rep_tel1">
            </div>
            <div class="field">
                <label class="main">Tel2:</label>
                <input type="text" name="rep_tel2">
            </div>

            <div class="field">
                <label class="main">Mobile:</label>
                <input type="text" name="rep_numeromobile">
            </div>
        <div class="field buttons">
            <label class="main">&nbsp;</label>
            <button type="button" class="prev">&laquo; Prev</button>
        </div>
    </section>
    <input href='?ajout=eleve' type="submit" value ='Enregistrer'/>

    </form>

</div>
</div>
        </div>
<span id="invalid"></span>
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
                                <form method="POST" action="?ajout=maitre">
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
                                <form method="POST" enctype="multipart/form-data" action="?ajout=ent">
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
            <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    <!-- Code JS Affichage des tableaux Absences - Arrivées tardives - Mises à la porte -->
        <script>
            $('#myModal').on('shown.bs.modal', function () {
              $('#myInput').focus()
            })

        </script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#myForm :input").prop("disabled", true);
            });
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
                    format: "dd.mm.yyyy",
                    language: "fr",
                    calendarWeeks: true,
                    todayHighlight: true
                });
                $(".date-picker").datetimepicker({format: 'dd.mm.yyyy'});

            }
            document.addEventListener("DOMContentLoaded", function() {
                RefreshDatePickers();
            });

        </script>

        <!-- CSS Tableau en accordéon (Entreprises - Maître d'apprentissage  - Représentant légal) -->
  

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


<link rel="stylesheet" href="http://necolas.github.io/normalize.css/2.1.3/normalize.css">
<link rel="stylesheet" href="css/jquery.idealforms.css">

<script src="js/out/jquery.idealforms.min.js"></script>
<script>

    $('form.idealforms').idealforms({

      silentLoad: false,

      rules: {
        'username': 'required username ajax',
        'email': 'required email',
        'password': 'required pass',
        'confirmpass': 'required equalto:password',
        'date': 'required date',
        'picture': 'required extension:jpg:png',
        'website': 'url',
        'hobbies[]': 'minoption:2 maxoption:3',
        'phone': 'required phone',
        'zip': 'required zip',
        'options': 'select:default',
      },

      errors: {
        'username': {
          ajaxError: 'Username not available'
        }
      },


    });



    $('form.idealforms').find('input, select, textarea').on('change keyup', function() {
      $('#invalid').hide();
    });

    $('form.idealforms').idealforms('addRules', {
      'comments': 'required minmax:50:200'
    });

    $('.prev').click(function(){
      $('.prev').show();
      $('form.idealforms').idealforms('prevStep');
    });
    $('.next').click(function(){
      $('.next').show();
      $('form.idealforms').idealforms('nextStep');
    });

  </script>
</body>

</html>