<?php

    include 'bdd.php';
    $requete=$bdd->prepare('SELECT cla_nom FROM t_classe ORDER BY cla_nom ASC');
	$requete->execute();
	$classe=$requete->fetchAll();
    $choix=@$_POST['choixListe'];
    $eleve=0;
    $sport=@$_GET['sport'];
    $classe1=@$_GET['classe'];
    $inscription=0;
    $choixSport=@$_POST['choixSport'];
    $materiel=@$_POST['cbox1'];
    $cours=@$_POST['cbox2'];
    $id=@$_GET['eleve'];
    $click=@$_GET['click'];
    $paiement=@$_GET['paiement'];
    
    
   
                if(empty($choix) AND isset($classe1)){
                    $choix=$classe1;
                }
           

        
             if(isset($click)){
                
                if(isset($cours)){$cours="oui";}else{$cours="non";}
                if(isset($materiel)){$materiel="oui";}else{$materiel="non";}
                 
                if($choixSport!="entreprise"){
                    $req_inscription=$bdd->prepare('update nlhj_cepm.t_sporthiver set choixSport="'.$choixSport.'", coursESS="'.$cours.'", materiel="'.$materiel.'", paiement="non" where id_eleve='.$id.'');
                    $req_inscription->execute();
                }else{
                    $req_inscription=$bdd->prepare('update nlhj_cepm.t_sporthiver set choixSport="'.$choixSport.'", coursESS="'.$cours.'", materiel="'.$materiel.'", paiement="spe" where id_eleve='.$id.'');
                    $req_inscription->execute();
                }
                $click="";
                
              
                
            
            }
            
            if(isset($paiement)){
                    
                
                
                $req_paiement=$bdd->prepare('update nlhj_cepm.t_sporthiver set paiement="oui" where id_eleve='.$id.'');
                $req_paiement->execute();
                $paiement="";
                
                
            }
            
          

           
            if(isset($choix)){
                $requete1=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_sporthiver.choixSport IS NULL AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'");');
                $requete1->execute();
                $eleve=$requete1->fetchAll();

                
                $requete2=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_sporthiver.choixSport !="" AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'");');
                $requete2->execute();
                $eleve_modif=$requete2->fetchAll();
                
    
                
                $requete3=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sporthiver ON t_eleve.id_eleve = t_sporthiver.id_eleve WHERE t_sporthiver.paiement="non" AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'");');
                $requete3->execute();
                $eleve_paiement=$requete3->fetchAll();
            } 
            
           

  
           
            
?>
<script>
    
      $('#myTabs a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
      }) 
      
      $('#myTabs a[href="#profile"]').tab('show') // Select tab by name
      $('#myTabs a:first').tab('show') // Select first tab
      $('#myTabs a:last').tab('show') // Select last tab
      $('#myTabs li:eq(2) a').tab('show') // Select third tab (0-indexed)
      
      
      
      $('#someTab').tab('show')
    

</script>



<html>
  <head>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>  
        <link rel="stylesheet" href="inscription.css">
        <link rel="stylesheet" href="script.js">
      
        	<style>
			.input_hidden {
				position: absolute;
				left: -9999px;
			}

			.selected {
				background-color: #ccc;
			}

			#sport label {
				display: inline-block;
				cursor: pointer;
			}

			#sport label img {
				padding: 3px;
			}
			.flotte {
				overflow: hidden;
				padding-left: 27%;
			}
			.flotte .pic {
				float: left;
				margin-left: -140px;
			}
		</style>
  </head>

  <body>
      
            <div class="entete">Intranet CEPM </div>
         
         
            <div class="choixClasse" name="recherche">
                       <div class="title-container"><h2>Faire une recherche</h2></div> 
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
                     <li role="presentation"><a href="#modif" aria-controls="paie" role="tab" data-toggle="tab">Modification</a></li>
                     <li role="presentation"><a href="#paie" aria-controls="paie" role="tab" data-toggle="tab">Paiement</a></li>
                    </ul>
                </div>
                                
                        
                                        
                                
                        
                                    <div class="tab-content">    
                                         <div class="tab-pane active" id="student-container">
                                               <?php  
                                                        if(isset($choix)){
                                                            for($a=0; $a < count($eleve); $a++){ ?>
                                                                <div class="student">
                                                                    <img class="photo" src="C:\Users\alessandro.sipala\Desktop\Utilisateurs\<?php echo$eleve[$a]['id_codebarre'];?>.jpg">&nbsp;&nbsp;
                                                                    <?php echo$eleve[$a]['ele_prenom'].' '.$eleve[$a]['ele_nom']; ?><br><br>
                                                                           <form method="post" action="?click=oui&classe=<?php echo $choix; ?>&eleve=<?php echo $eleve[$a]['id_eleve']; ?>">
                                                                                <label><INPUT type= "radio" name="choixSport" value="ski"/> Ski</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     <br><br>
                                                                                <label><INPUT type= "radio" name="choixSport" value="snow"/> Snowboard</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br><br>

                                                                                <label><INPUT type= "radio" name="choixSport" value="raquette"/> Raquette</label> <br><br>
                                                                                <label><INPUT type= "radio" name="choixSport" value="entreprise"/> Entreprise</label> <br><br><br>

                                                                                <label> <input type="checkbox" name="cbox1" id="cbox1" value="materiel"/> A son propre matériel</label> <br><br>
                                                                                <label><input type="checkbox" name="cbox2" id="cbox2" value="ess"/> Veut participer a un cours ESS (école suisse de ski)</label> <br><br>
                                                                               
                                                                                <input type="submit" value="inscrire"/>  

                                                                            </form>
                                                                    
                                                                                    <br><br>
                                                                   </div>
                                                                    <?php }   } ?>
                                                                
                                        </div>
                                            
                                          
                                                        
               
                                         <div class="tab-pane" id="modif">
                                             
                                                   <?php    if(isset($choix)){
                                                                if(isset($eleve_modif)){
                                                                        for($a=0; $a < count($eleve_modif); $a++){ ?>
                                                                             <div class="student_modif">
                                                                                        <img class="photo" src="C:\Users\alessandro.sipala\Desktop\Utilisateurs\<?php echo$eleve_modif[$a]['id_codebarre'];?>.jpg">&nbsp;&nbsp;
                                                                                        <?php echo$eleve_modif[$a]['ele_prenom'].' '.$eleve_modif[$a]['ele_nom'];?><br><br>
                                                                                        <?php $req_recup=$bdd->prepare('SELECT * from t_sporthiver where id_eleve="'.$eleve_modif[$a]['id_eleve'].'"');
                                                                                              $req_recup->execute();
                                                                                              $eleve_choix=$req_recup->fetchAll();?> 
                                                                                 
                                                                                               <form method="post" action="?click=oui&classe=<?php echo $choix; ?>&eleve=<?php echo $eleve_modif[$a]['id_eleve']; ?>">
                                                                                                    <label><INPUT type="radio" name="choixSport" value="ski" <?php if($eleve_modif[$a]['choixSport']=="ski"){ ?> checked="checked" /><?php } else{?> /> <?php }?>  Ski</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="snow"<?php if($eleve_modif[$a]['choixSport']=="snow"){ ?> checked="checked" /><?php } else{?> /> <?php }?>Snowboard</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br><br>

                                                                                                    <label><INPUT type= "radio" name="choixSport" value="raquette"<?php if($eleve_modif[$a]['choixSport']=="raquette"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Raquette</label> <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="entreprise"<?php if($eleve_modif[$a]['choixSport']=="entreprise"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Entreprise</label> <br><br><br>

                                                                                                    <label> <input type="checkbox" name="cbox1" id="cbox1" value="materiel"<?php if($eleve_modif[$a]['materiel']=="oui"){ ?> checked="checked" /><?php } else{?> /> <?php }?> A son propre matériel</label> <br><br>
                                                                                                    <label><input type="checkbox" name="cbox2" id="cbox2" value="ess"<?php if($eleve_modif[$a]['coursESS']=="oui"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Veut participer a un cours ESS (école suisse de ski)</label> <br><br>

                                                                                                    <input type="submit" value="modifier"/>  

                                                                                                </form>

                                                                                                        <br><br>
                                                                             </div>
                                                                <?php } } else { echo "Personne n'est encore inscrit";}  } ?>
                                        </div>
                                        
                                        <div class="tab-pane" id="paie">
                                            <?php if(isset($choix)){
                                                            for($a=0; $a < count($eleve_paiement); $a++){ ?>
                                                                <div class="student_paie">
                                                                    <form method="POST" action="?paiement=oui&classe=<?php echo $choix; ?>&eleve=<?php echo $eleve_paiement[$a]['id_eleve']; ?>">
                                                                    <img class="photo" src="C:\Users\alessandro.sipala\Desktop\Utilisateurs\<?php echo$eleve_paiement[$a]['id_codebarre'];?>.jpg">&nbsp;&nbsp;
                                                                    <?php echo $eleve_paiement[$a]['ele_prenom'].' '.$eleve_paiement[$a]['ele_nom']; ?>
                                                                         <input type="submit" value="Payé"/> 
                                                                    </form><br><br>
                                                                </div>
                                                                      <?php     } }?>
                                                                    
                                                                        
                                                                
                                              
                                    
                                        </div>
                                    </div>
    
              </div><br><br>
              <div class="footer">CEPM <?php echo date('Y');?></div>
      
      
  </body>
     
</html>
