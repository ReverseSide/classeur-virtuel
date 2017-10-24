<?php

session_start();
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}
    include '../include/bdd.php';
   
    $requete=$bdd->prepare('SELECT cla_nom FROM t_classe WHERE cla_type!="Terminales" ORDER BY cla_nom ASC');
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
		
            /*Lors de la validation d'un formulaire que ce soit une inscription ou une modification la requete va être envoyer en fonction des choix du user*/
             if(isset($click)){
                 
						//modif été 
                        
                        if(isset($choixSport)){
                        
                                if($choixSport!="Entreprise"){
                                    $req_inscription=$bdd->prepare('update t_sportete set choixSport="'.$choixSport.'" where id_eleve='.$id.'');
                                    $req_inscription->execute();
                                    $req1=$bdd->prepare('update t_eleve set ele_numeromobile="'.$tel.'" where id_eleve='.$id.'');
                                    $req1->execute();
                                }
                        
                 
                                if($choixSport=="Entreprise"){
                                            $req_inscription=$bdd->prepare('update t_sportete set choixSport="'.$choixSport.'" where id_eleve='.$id.'');
                                            $req_inscription->execute();
                                            $req1=$bdd->prepare('update t_eleve set ele_numeromobile="'.$tel.'" where id_eleve='.$id.'');
                                            $req1->execute();
                                        }
                        }
                 
                 /*Permet de désinscrir un élève inscrit*/
                if(isset($_POST['reinitialiser'])){
                
                    $req_reini=$bdd->prepare('update t_sportete set choixSport=NULL where id_eleve='.$id.'');
                    $req_reini->execute();
                    $_POST['reinitialiser']="";
                 }
                 
                $click="";
            }

        
           
            
          
            /*les différentes requetes de selection en fonction des onglets.(exemple uniquement les inscrit ou les non inscrit)*/
           
            if(isset($choix)){
                $requete1=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sportete ON t_eleve.id_eleve = t_sportete.id_eleve WHERE t_sportete.choixSport IS NULL AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") order by ele_nom ASC;');
                $requete1->execute();
                $eleve=$requete1->fetchAll();
                
                $requete2=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sportete ON t_eleve.id_eleve = t_sportete.id_eleve WHERE t_sportete.choixSport !="" AND t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") ORDER BY ele_nom ASC;');
                $requete2->execute();
                $eleve_modif=$requete2->fetchAll();
                
                
                $requete4=$bdd->prepare('SELECT * FROM t_eleve INNER join t_sportete ON t_eleve.id_eleve = t_sportete.id_eleve WHERE t_eleve.idx_classe=(select id_classe from t_classe where cla_nom="'.$choix.'") ORDER BY ele_nom ASC;');
                $requete4->execute();
                $eleve_recap=$requete4->fetchAll();
				
				//get le jours de sport de la classe séléctionée pour proposer la Danse ou non
				$requeteDay=$bdd->prepare('SELECT cla_joursSport FROM t_classe WHERE cla_nom="'.$choix.'"');
                $requeteDay->execute();
				$y=$requeteDay->fetch();
							
				
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
                    <h1 class="page-header">Inscription semaine sportive d'été</h1>
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
                                                                                    <label><INPUT type= "radio" name="choixSport" value="Football"/> Football</label>   <br><br>
                                                                                    <label><INPUT type= "radio" name="choixSport" value="Beach-volley"/> Beach-volley </label> <br><br>
                                                                                    <label><INPUT type= "radio" name="choixSport" value="Unihockey"/> Unihockey</label> <br><br>
<?php if ($y['cla_joursSport'] == "Lundi" || $y['cla_joursSport'] == "Mardi" || $y['cla_joursSport'] == "Mercredi" || $y['cla_joursSport'] == "lundi" || $y['cla_joursSport'] == "mardi" || $y['cla_joursSport'] == "mercredi") echo '<label><INPUT type= "radio" name="choixSport" value="Danse"/> Danse</label> <br><br>'; ?>
																					<label><INPUT type= "radio" name="choixSport" value="Entreprise"/> Entreprise</label>
                                                                                </div> 
																				
                                                                               <div id="droite">
                                                                               
                                                                               </div><br><br><br><br><br><br><br><br><br><br><br>
                                                                                   
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
                                                                                        <?php $req_recup=$bdd->prepare('SELECT * from t_sportete where id_eleve="'.$eleve_modif[$a]['id_eleve'].'"');
                                                                                              $req_recup->execute();
                                                                                              $eleve_choix=$req_recup->fetchAll();?> 
                                                                                                    
                                                                                                  <div id="gauche">
                                                                                                    <p>Choix d'activité</p>
                                                                                                   <label><INPUT type="radio" name="choixSport" value="Football" <?php if($eleve_modif[$a]['choixSport']=="Football"){ ?> checked="checked" /><?php } else{?> /> <?php }?>  Football</label> <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="Beach-volley"<?php if($eleve_modif[$a]['choixSport']=="Beach-volley"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Beach-volley</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br><br>
                                                                                                    <label><INPUT type= "radio" name="choixSport" value="Unihockey"<?php if($eleve_modif[$a]['choixSport']=="Unihockey"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Unihockey</label> <br><br>
																		<?php if($y['cla_joursSport'] == "Lundi" || $y['cla_joursSport'] == "Mardi" || $y['cla_joursSport'] == "Mercredi" || $y['cla_joursSport'] == "lundi" || $y['cla_joursSport'] == "mardi" || $y['cla_joursSport'] == "mercredi") {
																			echo '<label><INPUT type= "radio" name="choixSport" value="Danse"'; 
																			if($eleve_modif[$a]['choixSport']=="Danse"){ echo ' checked="checked" />';  }
																			else{ echo' /> '; }
																			echo ' Danse</label> <br/><br/>';
																			} ?>
                                                                                                    
																									<label><INPUT type= "radio" name="choixSport" value="Entreprise"<?php if($eleve_modif[$a]['choixSport']=="Entreprise"){ ?> checked="checked" /><?php } else{?> /> <?php }?> Entreprise</label>
                                                                                                   </div>
                                                                                                   
                                                                                                  <div id="droite">
																								  
                                                                                                   </div><br><br><br><br><br><br><br><br><br><br><br>
																								   
																								   
                                                                                                    <input class="btn btn-primary" type="submit" name="modifier" value="modifier" onclick="page('modif')"/>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                                    <input class="btn btn-primary" type="submit" name="reinitialiser" value="reinitialiser" onclick="page('modif')"/> 
                                                                                                </form>
                                                                                

                                                                                                        <br><br>
                                                                             </div>
                                                                <?php } } else { echo "Personne n'est encore inscrit";}  } ?>
                                        </div>
                                        
                                        <div class="tab-pane" id="recap">
                                          
                                            
                                            <table id="table_id" class="display">
                                                        <thead>
                                                            <tr>
                                                                <th>Elève</th>
                                                                <th></th>
                                                                <th>Téléphone</th>
                                                                <th>Choix d'activité</th>
																<th>Heure d'arrivée</th>
																<th>Heure de départ</th>
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
                                                           
                                                            
                                                            
                                                            ?>
                                                            
                                                                </td>
                                                                <td><?php echo $eleve_recap[$a]['ele_prenom']." ".$eleve_recap[$a]['ele_nom']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['ele_numeromobile']; ?></td>
                                                                <td><?php echo $eleve_recap[$a]['choixSport']; ?></td>
																<td><?php echo $eleve_recap[$a]['depart']; ?></td>
																<td><?php echo $eleve_recap[$a]['arrivee']; ?></td>
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
                                        message: 'Classe: <?php echo $choix; ?>&nbsp;&nbsp;&nbsp;&nbsp;',
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
