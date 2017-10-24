<?php

session_start();
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

    include '../include/bdd.php';
   
    $requete=$bdd->prepare('SELECT cla_nom FROM t_classe ORDER BY cla_nom ASC');
	$requete->execute();
	$classe=$requete->fetchAll();
    $choix=@$_POST['choixListe'];
    $eleve=0;
    $sport=@$_GET['sport'];
    $classe1=@$_GET['classe'];
    $inscription=0;
    $choixSport=@$_POST['choixSport'];
    $choixDepart=@$_POST['depart'];
    $materiel=@$_POST['cbox1'];
    $cours=@$_POST['cbox2'];
    $id=@$_GET['eleve'];
    $click=@$_GET['click'];
    $paiement=@$_GET['paiement'];
    $temp=@$_GET['page'];
    $tel=@$_POST['tel'];
    $total=0;

            
            /*la variable page sert a faire une redirection sur l'onglet ou se situait l'utilisateur avant un refresh*/
            if(isset($temp)){
                $page=@$_GET['page'];
            }else{
                $page="inscription";
            }
              
            if(isset($page)){
                echo"<input type='hidden' id='page' value='".$page."'>";
            }

                /*Cette fonction permet de garder la classe en mémoire apres un refresh*/
                if(empty($choix) AND isset($classe1)){
                    $choix=$classe1;
                }

            /*Lors de la validation d'un formulaire que ce soit une inscription ou une modification la requete va être envoxer en fonction des choix du user*/
             if(isset($click)){
                 
                 
                
                        if(isset($cours)){$cours="oui";}else{$cours="non";}
                        if(isset($materiel)){$materiel="oui";}else{$materiel="non";}
                        
                        if(isset($choixSport)){
                        
                                if($choixSport!="entreprise"){
                                    if($materiel=='oui'){$montant=40;}else{$montant=20;}
                                    if(isset($choixDepart)){
                                    $req_inscription=$bdd->prepare('update t_sporthiver set choixSport="'.$choixSport.'", coursESS="'.$cours.'", materiel="'.$materiel.'", paiement="non", lieuxDepart="'.$choixDepart.'", montant="'.$montant.'" where id_eleve='.$id.'');
                                    $req_inscription->execute();
                                    $req1=$bdd->prepare('update t_eleve set ele_numeromobile="'.$tel.'" where id_eleve='.$id.'');
                                    $req1->execute();
                                    }
                                }
                        
                 
                                if($choixSport=="entreprise"){
                                            $req_inscription=$bdd->prepare('update nlhj_cepm.t_sporthiver set choixSport="'.$choixSport.'", coursESS="'.$cours.'", materiel="'.$materiel.'", montant=0, paiement="participe pas", lieuxDepart="" where id_eleve='.$id.'');
                                            $req_inscription->execute();
                                            $req1=$bdd->prepare('update t_eleve set ele_numeromobile="'.$tel.'" where id_eleve='.$id.'');
                                            $req1->execute();
                                        }
                        }
                 
                 /*Permet de désinscrir un élève inscrit*/
                if(isset($_POST['reinitialiser'])){
                
                    $req_reini=$bdd->prepare('update t_sporthiver set choixSport=NULL, materiel=NULL, coursESS=NULL, paiement=NULL, lieuxDepart=NULL where id_eleve='.$id.'');
                    $req_reini->execute();
                    $_POST['reinitialiser']="";
                 }
                 
                $click="";
            }

            /*Met l'etat du paiement dans la bdd*/
            if(isset($paiement)){
                    
                
                if(isset($_POST['paye'])){
                    $req_paiement=$bdd->prepare('update nlhj_cepm.t_sporthiver set paiement="oui" where id_eleve='.$id.'');
                    $req_paiement->execute();
                   
                }elseif(isset($_POST['nonpaye'])){
                    $req_paiement=$bdd->prepare('update nlhj_cepm.t_sporthiver set paiement="non" where id_eleve='.$id.'');
                    $req_paiement->execute();
                }
                
                $paiement="";
                
            }

        
           
            
          
            /*les différentes requetes de selection en fonction des onglets.(exemple uniquement les inscrit ou les non inscrit)*/
           
            if(isset($choix)){
                $requete1=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_sporthiver.choixSport IS NULL AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") order by ele_nom ASC;');
                $requete1->execute();
                $eleve=$requete1->fetchAll();

                
                $requete2=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_sporthiver.choixSport !="" AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") ORDER BY ele_nom ASC;');
                $requete2->execute();
                $eleve_modif=$requete2->fetchAll();
                
    
                
                $requete3=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_sporthiver.paiement !="" AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") AND choixSport!="entreprise" ORDER BY ele_nom ASC;');
                $requete3->execute();
                $eleve_paiement=$requete3->fetchAll();
                
                
                
                $requete4=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") ORDER BY ele_nom ASC;');
                $requete4->execute();
                $eleve_recap=$requete4->fetchAll();
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
                    <h1 class="page-header">Inscription semaine sportive d'hiver</h1>
                    </div>
                
                </div>
                 <div class="row">
                     
                    <div class="col-lg-8 col-lg-offset-1 col-sm-12 col-sm-offset-0" name="recherche">
                        <center> 
                             <form method="POST" name="classe" class="selection" >
                                <div class="input-group" role="group" aria-label="...">
                                    <select class="dropdown form-control" name="choixListe">
                                        <option></option>
                                        <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['cla_nom'].'">'.$classe[$a]['cla_nom'].'</option>'; ?> 
                                    </select> 
                                    <span class="input-group-btn">
                                         <input class="btn btn-default" type="submit" name="rechercher" value="rechercher" class="boutonRecherche" >
                                    </span>
                                </div>
                             </form>
                                <?php if(isset($choix)){echo $choix;} ?>
                        </center> 
                            <div class="menuInscription">
                                <ul class="nav nav-tabs" role="tablist">
                                 <li role="presentation" class="active"><a href="#student-container" aria-controls="student-container" role="tab" data-toggle="tab">Inscription</a></li>
                                 <li role="presentation" ><a href="#modif" aria-controls="paie" role="tab" data-toggle="tab">Modification</a></li>
                                 <li role="presentation"><a href="#paie" aria-controls="paie" role="tab" data-toggle="tab">Paiement</a></li>
                                 <li role="presentation"><a href="#recap" aria-controls="paie" role="tab" data-toggle="tab">Statut</a></li>
                                </ul>
                            </div>
                                
                        
                                        
                                
                        
                                    <div class="tab-content">    
                                         <div class="tab-pane active" id="student-container">
                                             
                                               <?php  
                                                        if(isset($choix)){
                                                            for($a=0; $a < count($eleve); $a++){ ?>
                                                                <div class="student">
                                                                 <form method="post" action="?click=oui&classe=<?php echo $choix; ?>&eleve=<?php echo $eleve[$a]['id_eleve']; ?>#page-wrapper">  
                                                                    
                                                                      <?php    
                                                                                                
                                                                        $id_codebarre=$eleve[$a]['id_codebarre'];                      
                                                                        $filename = "images/utilisateurs/$id_codebarre.jpg";
                                                                        $filename2 = "images/utilisateurs/$id_codebarre.JPG";



                                                                        if (file_exists($filename)) {
                                                                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                                                                            } else {
                                                                                                if(file_exists($filename2))
                                                                                                {
                                                                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";
                                                                                                    
                                                                                                }else{
                                                                                                   echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>"; 
                                                                                                }

                                                                                            }

                                                                    
                                                                    
                                                                    
                                                                    ?>
                                                 
                                                                   &nbsp;&nbsp;
                                                                    <?php echo$eleve[$a]['ele_prenom'].' '.$eleve[$a]['ele_nom']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                     <label font-size="4">Numéro de téléphone:</label> <input type=text name="tel" value="<?php echo $eleve[$a]['ele_numeromobile'];?>">
                                                                    <br><br>
                                                                           
              
                                                                               <div id="gauche">
                                                                                    <p>Choix d'activité</p>
                                                                                    <label><INPUT type= "radio" name="choixSport" value="ski"/> Ski</label>   <br><br>
                                                                                    <label><INPUT type= "radio" name="choixSport" value="snow"/> Snowboard</label> <br><br>
                                                                                    <label><INPUT type= "radio" name="choixSport" value="raquette"/> Raquette</label> <br><br>
                                                                                    <label><INPUT type= "radio" name="choixSport" value="entreprise"/> Entreprise</label>
                                                                                </div> 
                                                                               <div id="droite">
                                                                                   <p>Lieu de départ</p>
                                                                                    <label><INPUT type= "radio" name="depart" value="lausanne"/> Gare de Lausanne</label>   <br><br>
                                                                                    <label><INPUT type= "radio" name="depart" value="aigle"/> Gare de Aigle</label> <br><br>
                                                                                    <label><INPUT type= "radio" name="depart" value="leysin"/> Leysin (formulaire de décharge)</label> <br><br>
                                                                               
                                                                               </div><br><br><br><br><br><br><br><br><br><br><br>
                                                                                   
                                                                                
                                                                               
                                                                               
                                                                               <label> <input type="checkbox" name="cbox1" id="cbox1" value="materiel"/> A besoin de matériel</label> <br><br>
                                                                                <label><input type="checkbox" name="cbox2" id="cbox2" value="ess"/> Veut participer a un cours ESS (école suisse de ski)</label> <br><br>
                                                                               
                                                                                <input class="btn btn-primary" type="submit" value="inscrire"/>  

                                                                            </form>
                                                                    
                                                                                    <br><br>
                                                                       
                                                                   </div>
                                                                    <?php  } } ?>
                                                                
                                        </div>
                                            
                                          
                                                        
               
                                         <div class="tab-pane" id="modif">
                                             
                                                   <?php    if(isset($choix)){
                                                                if(isset($eleve_modif)){
                                                                        for($a=0; $a < count($eleve_modif); $a++){ ?>
                                                                             <div class="student_modif">
                                                                                 <form method="post" action="?click=oui&classe=<?php echo $choix; ?>&eleve=<?php echo $eleve_modif[$a]['id_eleve']; ?>&page=modif">
                                                                                 
                                                                                  <?php
                                                                                                
                                                                                            $id_codebarre=$eleve_modif[$a]['id_codebarre'];                      
                                                                                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                                                                                            $filename2 = "images/utilisateurs/$id_codebarre.JPG";



                                                                                             if (file_exists($filename)) {
                                                                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                                                                            } else {
                                                                                                if(file_exists($filename2))
                                                                                                {
                                                                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";
                                                                                                    
                                                                                                }else{
                                                                                                   echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>"; 
                                                                                                }

                                                                                            }

                                                                                    ?>
                                                                                 
                                                                                       &nbsp;&nbsp;
                                                                                        <?php echo$eleve_modif[$a]['ele_prenom'].' '.$eleve_modif[$a]['ele_nom'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                         <label font-size="4">Numéro de téléphone:</label> 
                                                                                        <input type=text name="tel" value="<?php echo $eleve_modif[$a]['ele_numeromobile'];?>"><br><br>
                                                                                        <?php $req_recup=$bdd->prepare('SELECT * from t_sporthiver where id_eleve="'.$eleve_modif[$a]['id_eleve'].'"');
                                                                                              $req_recup->execute();
                                                                                              $eleve_choix=$req_recup->fetchAll();?> 
                                                                                                    
                                                                                                  <div id="gauche">
                                                                                                    <p>Choix d'activité</p>
                                                                                                   <label><INPUT type="radio" name="choixSport" value="ski" <?php if($eleve_modif[$a]['choixSport']=="ski"){ ?> checked="checked" /><?php } else{?> /> <?php }?>  Ski</label> <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="snow"<?php if($eleve_modif[$a]['choixSport']=="snow"){ ?> checked="checked" /><?php } else{?> /> <?php }?>Snowboard</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="raquette"<?php if($eleve_modif[$a]['choixSport']=="raquette"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Raquette</label> <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="entreprise"<?php if($eleve_modif[$a]['choixSport']=="entreprise"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Entreprise</label>
                                                                                                   </div>
                                                                                                   
                                                                                                  <div id="droite">
                                                                                                    <p>Lieu de départ</p>
                                                                                                    <label><INPUT type= "radio" name="depart" value="lausanne" <?php if($eleve_modif[$a]['lieuxDepart']=="lausanne"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Gare de Lausanne</label>   <br><br>
                                                                                                    <label><INPUT type= "radio" name="depart" value="aigle" <?php if($eleve_modif[$a]['lieuxDepart']=="aigle"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Gare de Aigle</label> <br><br>
                                                                                                    <label><INPUT type= "radio" name="depart" value="leysin"<?php if($eleve_modif[$a]['lieuxDepart']=="leysin"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Leysin (formulaire de décharge)</label> <br><br>

                                                                                                   </div><br><br><br><br><br><br><br><br><br><br><br>

                                                                                                    
                                                                                                   
                                                                                                   
                                                                                                    <label> <input type="checkbox" name="cbox1" id="cbox1" value="materiel"<?php if($eleve_modif[$a]['materiel']=="oui"){ ?>    checked="checked" /><?php } else{?> /> <?php }?> A besoin de matériel</label> <br><br>
                                                                                                    <label><input type="checkbox" name="cbox2" id="cbox2" value="ess"<?php if($eleve_modif[$a]['coursESS']=="oui"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Veut participer a un cours ESS (école suisse de ski)</label> <br><br>

                                                                                                    <input class="btn btn-primary" type="submit" name="modifier" value="modifier" onclick="page('modif')"/>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                                    <input class="btn btn-primary" type="submit" name="reinitialiser" value="reinitialiser" onclick="page('modif')"/> 
                                                                                                </form>
                                                                                

                                                                                                        <br><br>
                                                                             </div>
                                                                <?php } } else { echo "Personne n'est encore inscrit";}  } ?>
                                        </div>
                                        
                                        
                                        <div class="tab-pane" id="paie">
                                            <?php if(isset($choix)){ ?>
                                                <div class="col-lg-2 col-lg-offset-10 col-sm-4 col-sm-offset-8" name="montant">
                                                    <?php 
                                                        for($a=0; $a < count($eleve_paiement); $a++){
                                                            if(($eleve_paiement[$a]['choixSport']!="entreprise") AND ($eleve_paiement[$a]['paiement'])=="oui"){
                                                                                    if($eleve_paiement[$a]['materiel']=="oui"){
                                                                                        $montantp=40;}elseif($eleve_paiement[$a]['coursESS']=="oui"){ $montantp=20;}
                                                                                        else{$montantp=20;}
                                                                                 }else{$montantp=0;}
                                                            $total1=$montantp+$total1;
                                                            if($eleve_paiement[$a]['choixSport']!="entreprise"){
                                                                                    if($eleve_paiement[$a]['materiel']=="oui"){
                                                                                        $montant1=40;}elseif($eleve_paiement[$a]['coursESS']=="oui"){ $montant1=20;}
                                                                                        else{$montant1=20;}
                                                                                 }else{$montant1=0;}
                                                            $total2=$montant1+$total2;
                                                            
                                                        }
                                                    ?>
                                                    <h4><?php echo $total1.'.- /'.$total2.'.-';?></h4>
                                                </div>
                                                          <?php  for($a=0; $a < count($eleve_paiement); $a++){ ?>
                                                                <div class="student_paie">
                                                                    <form method="POST" action="?paiement=oui&classe=<?php echo $choix; ?>&eleve=<?php echo $eleve_paiement[$a]['id_eleve']; ?>&page=paie">
                                                                        
                                                                         <?php
                                                                                                
                                                                        $id_codebarre=$eleve_paiement[$a]['id_codebarre'];                      
                                                                        $filename = "images/utilisateurs/$id_codebarre.jpg";
                                                                        $filename2 = "images/utilisateurs/$id_codebarre.JPG";



                                                                        if (file_exists($filename)) {
                                                                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                                                                            } else {
                                                                                                if(file_exists($filename2))
                                                                                                {
                                                                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";
                                                                                                    
                                                                                                }else{
                                                                                                   echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>"; 
                                                                                                }

                                                                                            }
                                                                             if($eleve_paiement[$a]['choixSport']!="entreprise"){
                                                                                if($eleve_paiement[$a]['materiel']=="oui"){
                                                                                    $montant=40;}elseif($eleve_paiement[$a]['coursESS']=="oui"){ $montant=20;}
                                                                                    else{$montant=20;}
                                                                             }else{$montant=0;}

                                                                    ?>
                                                                        
                                                                    &nbsp;&nbsp;
                                                                    <?php echo $eleve_paiement[$a]['ele_prenom'].' '.$eleve_paiement[$a]['ele_nom']; ?>
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo "Montant: ".$montant.".-";?>
                                                                       <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <?php if($eleve_paiement[$a]['paiement']=="non"){ ?>   
                                                                        <input type="submit"  class="btn btn-success" name="paye" value="Payé" onclick="page('paie')"/><?php } elseif($eleve_paiement[$a]['paiement']=="oui"){ ?>
                                                                        <input type="submit"  class="btn btn-success" name="paye" value="Payé" disabled/> <?php } ?>
                                                                        
                                                                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                         
                                                                    <?php if($eleve_paiement[$a]['paiement']=="oui"){ ?>
                                                                        <input type="submit" class="btn btn-danger" name="nonpaye" value="Non payé"  onclick="page('paie')"/> <?php } elseif($eleve_paiement[$a]['paiement']=="non"){ ?>
                                                                        <input type="submit" class="btn btn-danger" name="nonpaye" value="Non payé" disabled/> <?php } ?>
                                                                    </form><br><br>
                                                                </div>
                                                                      <?php     } }?>
                                                                    
                                                                        
                                                                
                                              
                                    
                                        </div>
                                        
                                        <div class="tab-pane" id="recap">
                                            <?php   
                                                 if(isset($eleve_recap)){
                                                    for($a=0; $a < count($eleve_recap); $a++){ 
                                                        if($eleve_recap[$a]['choixSport']!=""){
                                                                                            if($eleve_recap[$a]['choixSport']!="entreprise"){
                                                                                                if($eleve_recap[$a]['materiel']=="oui"){
                                                                                                    $montant=40;
                                                                                                }else{$montant=20;}
                                                                                            }else{$montant=0;}
                                                                                        }else{$montant=0;}
                                                                                        $total=$montant+$total;
                                                        }
                                                 }
                                            ?>
                                            <div class="col-lg-2 col-lg-offset-10 col-sm-4 col-sm-offset-0" name="recherche"> Montant total: <?php echo $total.".-"; ?></div>
                                            <table id="table_id" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Elève</th>
                                                                <th></th>
                                                                <th>Téléphone</th>
                                                                <th>Choix d'activité</th>
                                                                <th>A besoin de matériel</th>
                                                                <th>Veut participer a un cours</th>
                                                                <th>Lieux de départ</th>
                                                                <th>Montant</th>
                                                                <th>Paiement</th>
                                                                <th>Code barre</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody> 
                                                              <?php    if(isset($choix)){
                                                                if(isset($eleve_recap)){
                                                                        for($a=0; $a < count($eleve_recap); $a++){ ?>
                                                            
                                                            <tr>
                                                                <td>
                                                            
                                                            
                                                                                     
                                                             <?php                          $id_codebarre=$eleve_recap[$a]['id_codebarre'];                      
                                                                                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                                                                                            $filename2 = "images/utilisateurs/$id_codebarre.JPG";



                                                                                            if (file_exists($filename)) {
                                                                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                                                                            } else {
                                                                                                if(file_exists($filename2))
                                                                                                {
                                                                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";
                                                                                                    
                                                                                                }else{
                                                                                                   echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>"; 
                                                                                                }

                                                                                            }
                                                                                        
                                                                                        if($eleve_recap[$a]['choixSport']!=""){
                                                                                            if($eleve_recap[$a]['choixSport']!="entreprise"){
                                                                                                if($eleve_recap[$a]['materiel']=="oui"){
                                                                                                    $montant=40;
                                                                                                }else{$montant=20;}
                                                                                            }else{$montant=0;}
                                                                                        }else{$montant=0;}
                                                                                        $total=$montant+$total;
                                                           
                                                            
                                                            
                                                            ?>
                                                            
                                                                </td>
                                                                <td><?php echo $eleve_recap[$a]['ele_prenom']." ".$eleve_recap[$a]['ele_nom']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['ele_numeromobile']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['choixSport']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['materiel']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['coursESS']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['lieuxDepart']; ?></td>
                                                                <td><?php echo $montant.".-" ?></td>
                                                                <td><?php echo $eleve_recap[$a]['paiement']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['id_codebarre']; ?></td>
                                                            </tr>
                                                            <?php }}} ?>
                                                           
                                                        </tbody>
                                            </table>
                                        </div>
                         </div>
                     
                    </div>
                    
                </div>

        </div>
    </div>
        
    
    <script>
    
        $(document).ready( function () {
                $('#table_id').dataTable({
                        paging: false,
                        bFilter: false,
                        bInfo: false,
                        dom: 'Bfrtip',
                         buttons: [
                                    {
                                        text: 'Imprimer',
                                        extend: 'print',
                                        orientation: 'landscape',
                                        message: 'Classe: <?php echo $choix; ?>&nbsp;&nbsp;&nbsp;&nbsp;                   Montant total: <?php echo $total; ?>',
                                    }
                                  ]                      
                }); 
               
         });

    </script>
    
    <script>
        
   

   $( document ).ready(function() {
         
       var page=document.getElementById('page').value;
            
                
                if(page=="inscription"){
                    $('.nav-tabs a[href="#student"]').tab('show');
                }
        
                if(page=="modif"){
                    $('.nav-tabs a[href="#modif"]').tab('show');
                }
            
                if(page=="paie"){
                    $('.nav-tabs a[href="#paie"]').tab('show');
                }
                
           
     });
      
      
    

    </script>

        
</body>

</html>
