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

//Contenu mail
$titremail="CONVOCATION AUX ARRÊTS POUR DES ABSENCES AUX COURS";
$corpsmail="<p>Madame, Monsieur<br><br>
                                Vos diverses absences aux cours, nous obligent à vous convoquer pour des heures d'arrêts<br>
                                Il se peut aussi que vous n'avez pas fourni les documents justifiant vos différentes absences, ou alors, ceux-ci n'ont pas été acceptés, et vous ont été retournés<br>
                                Ainsi, vous êtes convoqué-e le ,<br><br>
                                <strong>Date: </strong><br>
                                <strong>De: </strong><br>
                                <strong>À: </strong><br>
                                <strong>Nombre de périodes: </strong><br>
                                <strong>Où: </strong><br>
                                <strong>Dates des absences: <br><br>
                                Vous effectuerez un travail personnel amené par vos soins (ou prescris par votre enseignant-e) <br><br>
                                Nous insistons sur le fait que votre présence est obligatoire. Vous serez convoqué-e au Conseil de discipline si vous ne vous présentez pas à ce rendez-vous.<br><br>
                                Veuillez recevoir nos meilleures salutations.<br><br>
                                Votre doyen-ne de votre département du CEPV
                            </p>";

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
		header("Location:login.php");
}
    // vérifie qu' un formulaire d'ajout a été complété
if(isset($_GET['dpt'])){
		$departement = $_GET['dpt'];
}
if(isset($_GET['excuset'])=='Oui'){
		$sql=" update t_absence set abs_excuse ='Oui' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
}
if(isset($_GET['traitet'])=='Oui'){
		$sql=" update t_absence set abs_traite='0' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
}
if(isset($_GET['excuse'])=='Oui'){
		$sql=" update t_absence set abs_excuse='Oui' where id_absence='".$_GET['idabs']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
}
if(isset($_GET['Traite'])=='Oui'){
		$sql=" update t_absence set abs_traite='0' where id_absence='".$_GET['idabs']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
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



$requete5=$bdd->prepare("SELECT count(id_absence)as 'nbrperiode',id_eleve,ele_nom,ele_prenom,cla_nom,abs_date,abs_excuse,ele_mail,idx_entreprise FROM t_absence,t_eleve,t_sdh,t_classe,t_horaire  WHERE t_absence.idx_eleve=t_eleve.id_eleve AND t_absence.idx_cours=t_sdh.id_sdh AND t_absence.idx_classe=t_classe.id_classe  AND t_sdh.cou_periode=t_horaire.id_horaire  AND abs_traite='1'  and t_absence.idx_classe in (select id_classe from t_classe where t_classe.idx_departement='".$_GET['dpt'] ."') group by abs_date,idx_eleve ORDER BY abs_date desc ");
$requete5->execute();
$nbperiode=$requete5->fetchAll();


$requete4=$bdd->prepare("SELECT *	 FROM t_absence,t_eleve,t_sdh,t_classe,t_horaire  WHERE t_absence.idx_eleve=t_eleve.id_eleve AND t_absence.idx_cours=t_sdh.id_sdh AND t_absence.idx_classe=t_classe.id_classe AND t_sdh.cou_periode=t_horaire.id_horaire  AND (abs_traite='0' OR abs_traite IS NULL)  and t_absence.idx_classe in (select id_classe from t_classe where t_classe.idx_departement='".$_GET['dpt'] ."') ORDER BY abs_date desc");
$requete4->execute();
$absence=$requete4->fetchAll();


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
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">



    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
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
        
			<section class="content" style="min-height: 538px;">
        
<div class="col-xs-12">
    <div class="col-lg-8 col-sm-8 col-xs-12 no-padding edusecArLangCss"><h3 class="box-title"><i class="fa fa-th-list"></i> Absences traitées</h3></div>
    <div class="col-lg-4 col-sm-4 col-xs-12 no-padding" style="padding-top: 20px !important;">
        <div class="col-xs-5 right-padding">
           <!-- <a data-toggle="modal" data-target="#modalenvoiMail" class="btn btn-block btn-primary" onclick='validate()'  href="">E-Mail</a>     -->   </div>
         <div class="col-xs-7 right-padding">
	  <a class="btn btn-block btn-primary"  href="absences_trt.php?dpt= <?php echo $_GET['dpt'];?>">Retour</a>    </div>
</div>
</div>
<div class="col-xs-12" style="padding-top: 10px;">
    <div class="box">
        <div class="box-body table-responsive">
            <div class="batches-index">
	<div id="batch-id">	    <style>
.glyphicon-remove-circle {
  color : #C9302C;
}
.glyphicon-ok-circle {
  color : #449D44;
}
</style>

 <div class="box">
		      <table class="table table-striped table-bordered table-hover" id="myData">
        <thead>
          <tr>
          <th>Nom complet</th>
            <th>Classe</th>
            <th>Nbr Périodes</th>
			<th>Détails</th>
            <th>Date</th>
            <th>Excuse</th>
			<th>Statut</th>
			 			
          </tr>

        </thead>

        <tbody>

<?php
foreach ($nbperiode as $rs) {
		$req=$bdd->prepare("select ent_mail from t_entreprise where id_entreprise ='".$rs['idx_entreprise'] ."'");
		$req->execute();
		$entreprise=$req->fetchAll();

		$current = strtotime(date("Y-m-d"));
		$dateabs= strtotime( $rs['abs_date']);

		$datediff = $dateabs  - $current;
		$difference = floor($datediff/(60*60*24));
		if($difference==0)
		{
				$rs['dateabs']  = "Aujourd'hui";
		} else if($difference == -1) {
				$rs['dateabs']  = 'Hier';
		}  

		$requete4=$bdd->prepare("SELECT *, DATE_FORMAT(abs_date,'%d/%l/%Y') AS 'dateabs' FROM t_absence,t_eleve,t_sdh,t_classe,t_horaire  WHERE t_absence.idx_eleve=t_eleve.id_eleve AND t_absence.idx_cours=t_sdh.id_sdh AND t_absence.idx_classe=t_classe.id_classe AND t_sdh.cou_periode=t_horaire.id_horaire  AND (abs_traite='0' OR abs_traite IS NULL) and t_absence.idx_eleve='". $rs['id_eleve']."' and t_absence.abs_date='". $rs['abs_date']."' AND t_absence.abs_traite='1' ORDER BY abs_date,ele_nom ASC");
		$requete4->execute();
		$absence=$requete4->fetchAll();
		$i=1;			
		$coursmanque="";	
		foreach ($absence as $rs2) {
				$coursmanque= $coursmanque . "Période : ". $i ."  |  Cours: ". $rs2['cou_matlibelle'] . "\n"  ;
				$i=$i+1;
				$coursmanque= $coursmanque . "-------------------- \n Remarque: ".$rs2['abs_commentaire'];

				if (( $rs2['abs_mailc']=="0") || ($rs2['abs_mailc']=="")) {
						$mailconv="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='Convocation non envoyée'><img src='assets/img/convnok.png' ></small>";
				} else {
						$mailconv="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='Convocation envoyée'><img src='assets/img/convok.png' ></small>";
				}
					
				if (( $rs2['abs_mailp']=="0") || ($rs2['abs_mailp']=="")) {
						$mailpatron="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='E-Mail non envoyé au patron'><img src='assets/img/patnok.png' ></small>";
				} else {
						$mailpatron="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='E-Mail envoyé au patron'><img src='assets/img/patok.png' ></small>";
				}
		}
		
		echo "		<tr> <td>"  . $rs['ele_nom'] ." ". $rs['ele_prenom'] ."</td><td>" . $rs['cla_nom'] ."</td><td>" . $rs['nbrperiode'] ."</td><td> <small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='".$coursmanque."'><i class='glyphicon glyphicon-eye-open'></i> </small> </td> <td><span style= 'display: none;'>".str_replace('-','',$rs['abs_date'])."</span>" .date("d/m/Y", strtotime($rs['abs_date']))."</small></td><td>"; 
		if ( @$entreprise[0]['ent_mail']!=""){  
				$mail_ent=" | Entreprise: ".$entreprise[0]['ent_mail'];
		} else {
				$mail_ent=" | Entreprise: ";
		}
		if ( $rs['abs_excuse']=="Non"){       
				echo"	<a class='btn btn-block btn-warning'  href='absences_trt.php?excuset=Oui&eleve=".$rs['id_eleve']."&date=".$rs['abs_date']."&dpt=".$departement."' >Non-excusé</a></a></td>";
		} else {
				echo"	<a class='btn btn-block btn-success'  href='absences_trt.php?excuset=Oui&eleve=".$rs['id_eleve']."&date=".$rs['abs_date']."&dpt=".$departement."' >Excusé</a></a></td>";
		}

		echo"<td style='width: 89px;  margin: auto;border-collapse: separate;'><a width: '100px' type='submit' class='btn btn-primary'   href='absences_trt.php?traitet=Oui&eleve=".$rs['id_eleve']."&date=".$rs['abs_date']."&dpt=".$departement."'  style='text-align: right;'>A traiter</a></td></tr>";
	}
    echo"    </tbody></table>";
	
?>





 <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js" async></script>
	 <!-- Include all compiled plugins (below), or include individual files as needed -->
    
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
 

	 <script>
		 $('#myData').dataTable({
        "order": [[ 5, "desc" ]]
    });

		  function validate(){
			
			var datea = "";
			var nom = "";
			var ideleve = "";
			var ent = "";
		    var mail = "";
			var sList = "";
			var mails = "mailto:";
		 
		$('input[type=checkbox]').each(function () {
			 if (this.checked) {
			list= $(this).val();
			sList += "" + $(this).val() + "<br><br>";  // + (this.checked ? "checked" : "not checked") + ")"
 	 
			var arr = list.split("| Mail:");
  
			mails += arr[1].replace(" ", ",");
			
			if (entreprise == null)
			{
			
			
			}
			else
			{
			 
				var entreprise = list.split(" | Entreprise: ");
			entreprise= entreprise[1].split("| Mail:");
  
			ent += entreprise[0].replace(" ", ",");
			}
		
			// nom is ok 
			
			var nomeleve = list.split(" | Nom: "); 
			nomeleve= nomeleve[1].split("| Classe:");
			nom += nomeleve[0].replace(" ", ",");
			// id is ok 
			var ide = list.split("| Nom:");
			ide= ide[0].split("id:");
			ideleve += ide[1].replace(" ", ",");
			
		
				// date is ok 
				var dte = list.split(" | Date:");
				dte= dte[1].split("| Entreprise:");
				datea += dte[0].replace(" ", ",");
			
			
			 }
			});
			//mails.substring(1,mail.length);
			mail= mails.replace("mailto:,", "");
			mails= mails.replace("mailto:,", "mailto:");

			
		//	alert(mails);
				console.log (sList);
				 //altert(sList);
					 document.getElementById('destination').innerHTML =sList ;
					 		
					document.getElementById("txtlocal").href =mails;
						
					if (ent == "")
			{
			
			document.getElementById("txtlocalentr").href =mails;
			 
			}
			else
			{
				//alert("s2");	
					document.getElementById("txtlocalentr").href =mails+','+ent;
					document.getElementById("hiddenentreprise").value =ent;
					
			}
			
					document.getElementById("hiddenmails").value =mail;
					document.getElementById("hiddenreste").value =sList;
					
				
					document.getElementById("hiddendate").value =datea;
				
					document.getElementById("hiddennom").value =nom;
					document.getElementById("hiddenid").value =ideleve;
   
		
				// document.getElementById('txtlocal').innerHTML =mails ;
				// tu accèdes ici à chaque checkbox cochée avec check[i]
					
	
		 }
		
		 
		 
		 
		 
		 
		 
		 
		 
function validate2(){
var checkboxes = document.getElementById("my_form").getElementsByTagName("ckbabsence");
 
for (var i = 0, iMax = checkboxes.length; i < iMax; ++i) {
	
   var check = checkboxes[i];
   if (check.type == "checkbox" && check.checked) {
      // tu accèdes ici à chaque checkbox cochée avec check[i]
	 
   }
}}
		 </script>


<!--</tr><tr id="w0-filters" class="filters">
<td>&nbsp;</td><td><input type="text" class="form-control" name="BatchesSearch[batch_name]"></td>
<td><input type="text" class="form-control" name="BatchesSearch[batch_alias]"></td>
<td>
<select class="form-control" name="BatchesSearch[batch_course_id]">
<option value=""></option>
<option value="1">MCA</option>
<option value="2">BCA</option>
<option value="3">M.Sc.IT</option>
<option value="4">B.Sc.IT</option>
<option value="5">MBA</option>
</select>
</td>
<td><input type="text" id="start_date" class="form-control hasDatepicker" name="BatchesSearch[start_date]">
</td>

    <td><input type="text" id="end_date" class="form-control hasDatepicker" name="BatchesSearch[end_date]">
    </td>
<td><input type="text" id="end_date" class="form-control hasDatepicker" name="BatchesSearch[end_date]">
</td>
<td><select class="form-control" name="BatchesSearch[is_status]">
<option value=""></option>
<option value="1">InActive</option>
<option value="0">Active</option>
</select></td>
    <td><select class="form-control" name="BatchesSearch[is_status]">
            <option value=""></option>
            <option value="1">InActive</option>
            <option value="0">Active</option>
        </select></td></tr>-->



</div>	</div>	</div>
      </div>
    </div>
</div>
    </section>
		
		
		
		
		
		
		
		
		 </div>
		             <!-- Modal add convocation -->
                         <div class="modal fade" id="modalenvoiMail" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Absences : Mailing</h4>
        </div>
		<br>
										
								&nbsp;&nbsp;&nbsp;	<a class="btn btn-primary" href=""  id="txtlocal"> Local </a>
								&nbsp;&nbsp;&nbsp;	<a class="btn btn-primary" href=""  id="txtlocalentr"> Local (Copie Entreprise)</a>
									<br>
									<br>
        <div class="modal-body">
          
		  
		  <form name="frmAddConvocation" id="frmAddConvocation" action="./chkForm/chkmailabsence.php" method="POST">							
							<label for="copiemail">Formulaire d'envoi</label>

							<div class="row">
                            <div class="col-xs-8">
                           <div class="form-group">
                                                        <label for="copiemail">Titre :</label>
                                                        <input id="txttitre" name="txttitre"  class="form-control" value="<?php echo $titremail ?>">
																						
						<input type="hidden" name='hiddenmails'  id="hiddenmails" value="" class="form-control">
						<input type="hidden"  name ='hiddenreste' id="hiddenreste" value="" class="form-control">
						<input type="hidden"  name ='hiddenentreprise' id="hiddenentreprise" value="" class="form-control">
						<input type="hidden"  name ='hiddennom' id="hiddennom" value="" class="form-control">
						<input type="hidden"  name ='hiddenid' id="hiddenid" value="" class="form-control">
						<input type="hidden"  name ='hiddendate' id="hiddendate" value="" class="form-control">
  
                         </div>
                        </div>
						</div>
						<div class="row">
						<div class="col-xs-8">
									 
                                      
						<label for="texte" >Corps du message : </label>
						<textarea width="700" height="700" id="txtcorps" name="txtcorps" rows="60*60*24" ><?php echo $corpsmail ?></textarea>
						<br /><br />
					</div>
					</div>
								
		  
										<div class="row">
                                                <div class="col-xs-12">
												<label for="copiemail">Déstinataires :</label>
                                                    <div id="destination" class="absencedest">
                                                        <label for="copiemail">Déstinataires :</label>
                                                        <input type="text" name="bookId" id="bookId" value=""/>
                                                    </div>
													<input type='hidden' name="cache" value=""/>
                                                </div>
											</div>
		  
		  
		  <div class="row">
                                                <div class="col-xs-10">
                                                    <div class="form-group">
													 <label for="cbox3">
													Envoyer copie au patron
													</label>     <input type='checkbox' name='ckbcopiepatron' value='<?php if (isset($rs['id_eleve'])) echo $rs['id_eleve'].$rs['abs_date'] ;?>."'>
                                                   <label for="cbox2">
												Traiter les cas
													</label>     <input type='checkbox' name='ckbtraiter' value='<?php if (isset($rs['id_eleve'])) echo $rs['id_eleve'].$rs['abs_date'] ;?>."'>
                                                       <input type="submit" class="btn btn-danger" value="Envoyer" name="frmConSubmit" />
                                                    </div>
                                                </div>
											</div>
											

                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group text-right">
                                                        
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                                        <input type="hidden" value='' name="stuBarcode" id="stuBarcode" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

		  
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>
   </div>
                        <!-- fin Modal add stage -->

		
		</body>
		
		
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
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			
			mode : "textareas",
			 width : "640",
  height : "300"
		});
	</script>
    <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js" async></script>
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
	<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>

</body>

</html>