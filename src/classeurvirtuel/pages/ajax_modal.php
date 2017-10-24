
<html lang="en">


<BODY>
<form  id="subscribe-email-form" method="post" action="gestionheure.php?modifiercours">
<table class="table table-striped table-bordered">



    <?php
              
        require_once '../include/bdd.php';
              
        $query = "SELECT * FROM t_sdh where id_sdh='".$_REQUEST['id']."'";
    $stmt = $bdd->prepare( $query );
    $stmt->execute();
		
		
		
		
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        
		$query2 = "SELECT * FROM t_professeur where id_professeur='".$row['idx_professeur']."'";
        $query2 = $bdd->prepare($query2);
        $query2->execute();
		$monprofesseur=$query2->fetchAll();
		
		
		$query2 = "SELECT * FROM t_professeur order by pro_nom asc";
        $query2 = $bdd->prepare($query2);
        $query2->execute();
		$professeur=$query2->fetchAll();
		
		 $requete=$bdd->prepare("SELECT * FROM t_classe where id_classe='".$row['idx_classe']."'");
		$requete->execute();
		$maclasse=$requete->fetchAll();
		
		$sql2="select * from t_horaire";
        $req2=$bdd->prepare($sql2);
        $req2->execute();
        $periode=$req2->fetchAll();
		
		 $requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
	$requete->execute();
	$classe=$requete->fetchAll();
	
	
	
			 $requete=$bdd->prepare('SELECT * FROM t_salle ORDER BY sal_nom ASC');
	$requete->execute();
	$salle=$requete->fetchAll();
	
	
	 $requete=$bdd->prepare("SELECT * FROM t_salle  where id_salle='".$row['idx_salle']."'");
	$requete->execute();
	$masalle=$requete->fetchAll();
		?>



            <table class="table table-striped">


			 <tr>
  <td>

                            Cours code: </td><td>
                             <input type="text" name="mmatcode" id="mmatcode" value="<?php echo  $row['cou_matcode'] ;?>"> </td> </tr><tr><td>

                             Libellé: </td><td>
                             <input type="text" name="mmatlibelle" id="mmatlibelle"  value="<?php echo  $row['cou_matlibelle'] ;?>"> </td> </tr><tr><td>

                             Jour: </td><td>
                             <select class="dropdown form-control" name="mjour" id="mjour">
                                 <option selected='selected' value="<?php echo $row['cou_jour'] ;?>"><?php echo $row['cou_jour'] ;?></option>
								 <option value="lundi">Lundi</option>
                                 <option value="mardi">Mardi</option>
                                 <option value="mercredi">Mercredi</option>
                                 <option value="jeudi">Jeudi</option>
                                 <option value="vendredi">Vendredi</option>
                                 <option value="samedi">Samedi</option>
                             </select> </td> </tr>

<input type='hidden' name='eventid' value ='<?php echo $_REQUEST['id']; ?>'/>
                            <tr><td>

                                         Alternance: </td><td>
                                         <select class="dropdown form-control" name="malternance" id="malternance">
                                            <option selected='selected' value="<?php echo $row['cou_alternance'] ;?>"><?php echo $row['cou_alternance'] ;?></option>
										<option value="H">H</option>
                                 <option value="S.I">S.I</option>
                                 <option value="S.P">S.P</option>
                                 <option value="S1">S1</option>
                                 <option value="S2">S2</option>
                                  <option value="S1p">S1p</option>
                                 <option value="S2p">S2p</option>
								  <option value="S1i">S1i</option>
                                 <option value="S2i">S2i</option>

                                         </select></td> </tr><tr><td>

                                         Durée :</td>  <td>
                                         <select class="dropdown form-control" name="mduree" id="mduree">
                                            <option value="  <?php echo $row['cou_duree'] ;?>">  <?php echo $row['cou_duree'] ;?></option>
											 <option value="1">1</option>
                                             <option value="2">2</option>


 <option value="3">3</option>
								 <option value="4">4</option>

                                         </select></td> </tr><tr><td>

										 
                                         Période début:</td>  <td>
                                         <select class="dropdown form-control" name="mperiode">
										  <option value="  <?php echo $row['cou_periode'] ;?>">  <?php echo $row['cou_periode'].' - '. $row['cou_heuredebut'] ;?></option>
										 <?php for($a=0;$a<count($periode);$a++)echo'<option value="'.$periode[$a]['numeroDePeriode'].'">'.$periode[$a]['numeroDePeriode'].' - '. $periode[$a]['debutDePeriode'].'</option>'; ?>
                                          



                                         </select></td> </tr>
										 <tr><td>

                                                     Classe:  </td><td>



                                                         <select  id="mclasse" name="mclasse" placeholder="idm_classe">
                                                             <optgroup label="Classe">
                                                                 <option value=" <?php echo $row['idx_classe'] ;?>" > <?php echo $maclasse[0]['cla_nom'] ;?></option>
                                                                 <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?>
                                                         </select></td> </tr><tr><td>
                                                    Professeur: </td><td>
                                                         <select class="dropdown form-control" id="mprofesseur" name="mprofesseur">
                                                             <optgroup label="Professeur">
                                                                 <option value='<?php echo $row['idx_professeur'] ;?>'><?php  if (isset($monprofesseur[0]['pro_nom'])) echo $monprofesseur[0]['pro_nom'] .' '.$monprofesseur[0]['pro_prenom'];?></option>
                                                                 <?php for($a=0;$a<count($professeur);$a++)echo'<option value="'.$professeur[$a]['id_professeur'].'">'.$professeur[$a]['pro_nom'].' '.$professeur[$a]['pro_prenom'].'</option>'; ?>
                                                         </select></td> </tr><tr><td>
                                                   Salle:</td><td>
                                                         <select class="dropdown form-control" id="msalle" name="msalle">
                                                             <optgroup label="Salle de classe">
                                                                 <option value ='<?php echo $row['idx_salle'] ;?>'><?php if (isset($masalle[0]['sal_nom'])) {echo ( $masalle[0]['sal_nom']) ;}?></option>
                                                                 <?php for($a=0;$a<count($salle);$a++)echo'<option value="'.$salle[$a]['id_salle'].'">'.$salle[$a]['sal_nom'].'</option>'; ?>
                                                         </select></td> </tr>
 <tr>        <td colspan="2" align="center">

     </td></tr>

        <?php
      }
   ?>


</table>

        <input  type="submit" class="btn btn-primary btn-sm" value="Enregistrer" float="right"/>
     
    </form>


</body>
</html>