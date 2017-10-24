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
$titremail="Concerne : absences aux cours professionnels";
$corpsmail="<p>Madame, Monsieur<br><br>
								Nous vous signalons que votre apprenti-e <strong>[NOM-PRENOM]</strong>  était absent(e) aux cours professionnels aux dates et périodes suivantes:<br><br>
								 <strong>[ABSENCES]</strong><br><br>
								Nous vous prions de rappeler à votre apprenti qu'il doit nous faire parvenir une excuse visée par vous-même.<br> En vous remerciant de votre précieuse collaboration, nous restons à votre disposition pour tout renseignement complémentaire.<br><br> 
								Veuillez agréer, Madame, Monsieur, nos salutations distinguées.<br><br> 
								Le secrétariat du CEPM. <br>
                                <span>cepm.secretariat@vd.ch</span>
                            </p>";


$titremailc="Absence aux cours professionnels - Départ santé";
$corpsmailc="
 <html>
      <head>
       <title></title>
      </head>
      <body>
      
     


<p>Madame, Monsieur <br><br>   
								
                                Nous vous signalons que votre apprenti(e)<strong>[NOM-PRENOM]</strong> a quitté les cours professionnels le <strong>[ABSENCES]</strong>, pour des raisons de santé.<br><br>
                                Nous vous rappelons qu'il(elle) lui appartient de s'informer de la matière enseignée et de rattraper les éventuels travaux écrits.<br><br>
                                En vous remerciant de votre précieuse collaboration, nous vous prions d'agréer, Madame, Monsieur, nos salutations distinguées.<br><br>
                                Le secrétariat du CEPM.<br>
								<span>cepm.secretariat@vd.ch</span>
								
                            </p> </body>
     </html>";
// retour à la ligne après Madame, Monsieur							


// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
		header("Location:login.php");
}
    // vérifie qu' un formulaire d'ajout a été complété
if(isset($_GET['dpt'])){

		$departement = $_GET['dpt'];
}
if((isset($_GET['excuset'])) AND $_GET['excuset']=='Oui'){
		$sql=" update t_absence set abs_excuse ='Oui' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
}
if((isset($_GET['excuset'])) AND $_GET['excuset']=='Non'){
        $sql=" update t_absence set abs_excuse ='Non' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
        $change=$bdd->prepare($sql);
        $change->execute();
}
if(isset($_GET['traitet'])=='Oui'){
		$sql=" update t_absence set abs_traite='1' where idx_eleve='".$_GET['eleve']."' and abs_date='".$_GET['date']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
}
if(isset($_GET['excuse'])=='Oui'){
		$sql=" update t_absence set abs_excuse='Oui' where id_absence='".$_GET['idabs']."'";
		$change=$bdd->prepare($sql);
		$change->execute();
}
if(isset($_GET['Traite'])=='Oui'){
		$sql=" update t_absence set abs_traite='1' where id_absence='".$_GET['idabs']."'";
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



$requete5=$bdd->prepare("SELECT count(id_absence)as 'nbrperiode',id_eleve,ele_nom,ele_prenom,cla_nom,abs_date,abs_excuse,ele_mail,idx_entreprise FROM t_absence,t_eleve,t_sdh,t_classe,t_horaire  WHERE  abs_date < CURDATE() AND t_absence.idx_eleve=t_eleve.id_eleve AND t_absence.idx_cours=t_sdh.id_sdh AND t_absence.idx_classe=t_classe.id_classe  AND t_sdh.cou_periode=t_horaire.id_horaire  AND (abs_traite='0' OR abs_traite IS NULL)  and t_absence.idx_classe in (select id_classe from t_classe where t_classe.idx_departement='".$_GET['dpt'] ."') group by abs_date,idx_eleve ORDER BY abs_date desc ");
$requete5->execute();
$nbperiode=$requete5->fetchAll();


$requete4=$bdd->prepare("SELECT *	FROM t_absence,t_eleve,t_sdh,t_classe,t_horaire  WHERE abs_date < CURDATE() AND t_absence.idx_eleve=t_eleve.id_eleve AND t_absence.idx_cours=t_sdh.id_sdh AND t_absence.idx_classe=t_classe.id_classe AND t_sdh.cou_periode=t_horaire.id_horaire  AND (abs_traite='0' OR abs_traite IS NULL)  and t_absence.idx_classe in (select id_classe from t_classe where t_classe.idx_departement='".$_GET['dpt'] ."') ORDER BY abs_date desc");
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
                <div class="col-xs-12" style="    padding: 0 0 19px 0;">
                    <div class="col-lg-12 col-sm-8 col-xs-12 no-padding edusecArLangCss"><h3 class="box-title"><i class="fa fa-th-list"></i> Absences à traiter</h3></div>
                    <div class="col-lg-20 col-sm-20 col-xs-20 no-padding" style="padding-top: 20px !important;">
                        <div class="col-xs-3 right-padding"></div>
                        <div class="col-xs-3 right-padding">
                            <a data-toggle="modal" data-target="#modalenvoiMail" class="btn btn-block btn-primary" onclick='validate()'  href="">Absence aux cours professionnels</a>
                        </div>
		                <div class="col-xs-3 right-padding">
		                    <a data-toggle="modal" data-target="#modalenvoiMailc" class="btn btn-block btn-primary" onclick='validatec()'  href="">Absence aux cours professionnels - Départ santé</a>
                        </div>
	                    <div class="col-xs-3 right-padding">
                            <a class="btn btn-block btn-primary"  href="absences_traitees.php?dpt= <?php echo $_GET['dpt'];?>">Absences Traitées</a>
                        </div>
                    </div>
                </div>
		<script language="JavaScript">
            function toggle(source) {
              checkboxes = document.getElementsByName('ckbabsence[]');
              for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;

              }

              $('input', this).each(function() {
                var type = this.type;
                if (type == 'checkbox') {
                    var id = this.id;
                    var checked = this.checked ;

                    // Etc.
                }

            })};

            function checkAll(ele) {
                 var checkboxes = document.getElementsByTagName('input');
                 if (ele.checked) {
                     for (var i = 0; i < checkboxes.length; i++) {
                         if (checkboxes[i].name == 'ckbabsence[]') {
                             checkboxes[i].checked = true;

                         }
                     }
                 } else {
                     for (var i = 0; i < checkboxes.length; i++) {
                         console.log(i)
                         if (checkboxes[i].name == 'ckbabsence[]') {
                             checkboxes[i].checked = false;
                         }
                     }
                 }
             }
        </script>
<style>
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
			<th>Statut</th>
            <th>Action</th>
			<th></th>
			<th></th>
		 
			<th><input type="checkbox" id="checkAll"  /> </th>
			
          </tr>

        </thead>

        <tbody>

<?php foreach ($nbperiode as $rs) {
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
		} else if($difference == -1)
		{
				$rs['dateabs']  = 'Hier';
		}//AND t_absence.abs_traite='0'

		$requete4=$bdd->prepare("SELECT *, DATE_FORMAT(abs_date,'%d/%l/%Y') AS 'dateabs' FROM t_absence,t_eleve,t_sdh,t_classe,t_horaire  WHERE  abs_date < CURDATE() AND t_absence.idx_eleve=t_eleve.id_eleve AND t_absence.idx_cours=t_sdh.id_sdh AND t_absence.idx_classe=t_classe.id_classe AND t_sdh.cou_periode=t_horaire.id_horaire  AND abs_traite='0' and t_absence.idx_eleve='". $rs['id_eleve']."' and t_absence.abs_date='". $rs['abs_date']."'  ORDER BY abs_date,ele_nom ASC");
		$requete4->execute();
		$absence=$requete4->fetchAll();
		$i=1;			
		$coursmanque="";
		$mailconv = "" ;
		$mailpatron = "";
        $Absences_string ="";
		foreach ($absence as $rs2) {
				$coursmanque= $coursmanque . "Période : ". $i ."  |  Cours: ". $rs2['cou_matlibelle'] . "\n"  ;
				$i=$i+1;
				$coursmanque= $coursmanque . "-------------------- \n Remarque: ".$rs2['abs_commentaire'];
		/*		if (( $rs2['abs_mailc']=="0") || ($rs2['abs_mailc']==""))
				{
						$mailconv="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='Convocation non envoyée'><img src='assets/img/convnok.png' ></small>";
				} else {
						$mailconv="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='Convocation envoyée'><img src='assets/img/convok.png' ></small>";
				}
				if (( $rs2['abs_mailp']=="0") || ($rs2['abs_mailp']==""))
				{
						$mailpatron="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='E-Mail non envoyé au patron'><img src='assets/img/patnok.png' ></small>";
				} else {
						$mailpatron="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='E-Mail envoyé au patron'><img src='assets/img/patok.png' ></small>";
				}*/
		}

        $requete8=$bdd->prepare("SELECT * FROM `t_absence` left join t_sdh on t_absence.idx_cours=t_sdh.id_sdh WHERE  abs_date < CURDATE() AND t_absence.`idx_eleve`='".$rs['id_eleve']."' and t_absence.abs_date=date('".$rs['abs_date']."')");
        $requete8->execute();
        $Absences=$requete8->fetchAll();

        foreach ($Absences as $key_absence=>$absence) {
            $Absences_string .= "Période : ".$absence["cou_heuredebut"]." |  Cours: ".htmlentities($absence["cou_matlibelle"], ENT_QUOTES, "UTF-8");
            $Absences_string .= "<br>";
        }

        $coursmanque= $Absences_string;

		echo "		<tr>					<td>		"  . $rs['ele_nom'] ." ". $rs['ele_prenom'] ."</td><td>" . $rs['cla_nom'] ."</td><td>" . $rs['nbrperiode'] ."</td><td> <small style='cursor:pointer; text-align: left;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='".$coursmanque."'> <i class='glyphicon glyphicon-eye-open'></i></small> </td> <td><span style= 'display: none;'> ".str_replace('-','',$rs['abs_date'])."</span>" .date("d/m/Y", strtotime($rs['abs_date']))." </small> </td> <td style='width: 89px;  margin: auto;border-collapse: separate;'>";

		if ( @$entreprise[0]['ent_mail']!=""){
				$mail_ent=" | Entreprise: ".$entreprise[0]['ent_mail'];
		} else {
				$mail_ent=" | Entreprise: ";
		}
		if ( $rs['abs_excuse']=="Non"){       
				echo "Non-excusé</td> <td> <div><a class='btn btn-success'  href='absences_trt.php?excuset=Oui&eleve=".$rs['id_eleve']."&date=".$rs['abs_date']."&dpt=".$departement."' >Excuser</a>";
		} else {
				echo "Excusé</td> <td> <div><a class='btn btn-warning'  href='absences_trt.php?excuset=Non&eleve=".$rs['id_eleve']."&date=".$rs['abs_date']."&dpt=".$departement."' >Rejeter l'excuse</a>";
		}

		if (( $rs2['abs_mailc']=="0") || ($rs2['abs_mailc']==""))
		{
				$mailconv="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='Convocation non envoyée'><img src='assets/img/convnok.png' ></small>";
		} else {
				$mailconv="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='Convocation envoyée'><img src='assets/img/convok.png' ></small>";
		}
		if (( $rs2['abs_mailp']=="0") || ($rs2['abs_mailp']==""))
		{
				$mailpatron="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='E-Mail non envoyé au patron'><img src='assets/img/patnok.png' ></small>";
		} else {
				$mailpatron="<small style='cursor:pointer;' data-toggle='tooltip' data-placement='bottom' data-html='true' title='E-Mail envoyé au patron'><img src='assets/img/patok.png' ></small>";
		}
		echo "
						<a class='btn btn-primary'   href='absences_trt.php?traitet=Oui&eleve=".$rs['id_eleve']."&date=".$rs['abs_date']."&dpt=".$departement."'>Marquer comme traité</a> </div></td>
					<td style='width: 34px;  margin: auto;border-collapse: separate;'>". $mailpatron." </td>
					<td style='width: 32px;  margin: auto;border-collapse: separate;'>".$mailconv." </td>
					<td style='width: 35px; margin: auto; border-collapse: separate;'><input class='sel' type='checkbox' name='ckbabsence[]' value='id:". $rs['id_eleve']." | Nom: " .$rs['ele_nom']." " .$rs['ele_prenom']. " | Classe: " .$rs['cla_nom']. "| Nbr cas: " .$rs['nbrperiode'].  " | Date:" .$rs['abs_date']. $mail_ent." | Mail: ".$rs['ele_mail']." | NumAbsence: ".@$rs2['id_absence']."'>
					</div></td>
					</tr>";
					
					
}
    echo"    </tbody>  

      </table>

    ";
	
?>
      

	 <script>
		var validateit="";
		//Absence aux cours professionnels
		function validate(){
			var x = document.getElementById('destination');
			x.style.display = 'none';
			var datea = "";
			var nom = "";
			var ideleve = "";
			var ent = "";
			var idabs = "";
			var mail = "";
			var sList = "";
			var mails = "mailto:";
		 
		$('input.sel').each(function () {
			
			 if ($(this).is(':checked')) {

               console.log($(this).attr('class'));

				
			list= $(this).val();
			sList += "" + $(this).val() + "<br><br>";  // + (this.checked ? "checked" : "not checked") + ")"
 	 
			var arr = list.split("| Mail: ");
			arr= arr[1].split("| NumAbsence:");
			mails += arr[0].replace(" ", ",");
			 
			var idabsence = list.split("| NumAbsence: ");
			
			idabs +=  idabsence[1]+',';
			
		 
			
			
			 
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
			nom += nomeleve[0].replace(" ", " ");
			// id is ok 
			var ide = list.split("| Nom:");
			ide= ide[0].split("id:");
			ideleve += ide[1].replace(" ", ",");
			 
		//alert(ideleve);
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
				validateit=sList;
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
   
					document.getElementById("hiddenidabsence").value =idabs;
				// document.getElementById('txtlocal').innerHTML =mails ;
				// tu accèdes ici à chaque checkbox cochée avec check[i]
					
	
		 }
		
		 
		 

		 
function validate2(){
var checkboxes = getElementsByTagName("ckbabsence[]");
 
for (var i = 0, iMax = checkboxes.length; i < iMax; ++i) {
	
   var check = checkboxes[i];
   if (check.type == "checkbox" && check.checked) {
      // tu accèdes ici à chaque checkbox cochée avec check[i]
	 
   }
}}

  //Absence aux cours professionnels -Départ santé
function validatec(){
    var x = document.getElementById('destinationc');
    x.style.display = 'none';
    var datea = "";
    var nom = "";
    var ideleve = "";
    var ent = "";
    var idabs = "";
    var mail = "";
    var sList = "";
    var mails = "";


    $('input.sel').each(function () {
        if ($(this).is(':checked')) {

        list= $(this).val();
        sList += "" + $(this).val() + "<br><br>";  // + (this.checked ? "checked" : "not checked") + ")"

        var arr = list.split("| Mail: ");
        arr= arr[1].split("| NumAbsence:");
        mails += arr[0].replace(" ", ",");


        var idabsence = list.split("| NumAbsence: ");

        idabs +=  idabsence[1]+',';


        var entreprise = list.split(" | Entreprise: ");
        entreprise= entreprise[1].split("| Mail:");
        ent += entreprise[0].replace(" ", ",");



        // nom is ok

        var nomeleve = list.split(" | Nom: ");
        nomeleve= nomeleve[1].split("| Classe:");
        nom += nomeleve[0].replace(" ", " ");
        nom = nom.trim();
        nom += ',';
        // id is ok
        var ide = list.split("| Nom:");
        ide= ide[0].split("id:");
        ideleve += ide[1].replace(" ", ",");

    //alert(ideleve);
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
    document.getElementById('destinationc').innerHTML =sList ;

    document.getElementById("txtlocalc").href =mails;


//alert("s2");
    document.getElementById("txtlocalentrc").href =mails+','+ent;
    document.getElementById("hiddenentreprisec").value =ent;

    document.getElementById("hiddenmailsc").value =mails;
    document.getElementById("hiddenrestec").value =sList;


    document.getElementById("hiddendatec").value =datea;

    document.getElementById("hiddennomc").value =nom;
    document.getElementById("hiddenidc").value =ideleve;

    document.getElementById("hiddenidabsencec").value =idabs;
// document.getElementById('txtlocal').innerHTML =mails ;
// tu accèdes ici à chaque checkbox cochée avec check[i]



 }
		 
function validate2(){
var checkboxes = getElementsByTagName("ckbabsence[]");
 
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
</div>
    </section>
		
		
		
		
		
		
		
		
		 </div>
    <!-- absences maladie -> ChkConvocation <- Absence aux cours professionnels - Départ santé -->
    <div class="modal fade" id="modalenvoiMailc" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Absence à traiter: Mail de convocation</h4>
                </div>
                <div class="modal-body">
                    <br>
                    <form name="frmAddConvocation2" id="frmAddConvocation2" action="./chkForm/chkConvocation.php" method="POST">
                        <a class="btn btn-primary" href=""  id="txtlocalc"> Local </a>
                        &nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href=""  id="txtlocalentrc"> Local (Copie Entreprise)</a>
                        <br>
                        <br>
                        <label for="copiemail">Formulaire d'envoi</label>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <label for="copiemail">Titre :</label>
                                    <input id="txttitrec" name="txttitrec"  class="form-control" value="<?php echo $titremailc ?>">
                                    <input type="hidden" name='hiddenmailsc'  id="hiddenmailsc" value="" class="form-control">
                                    <input type="hidden"  name ='hiddenrestec' id="hiddenrestec" value="" class="form-control">
                                    <input type="hidden"  name ='hiddenentreprisec' id="hiddenentreprisec" value="" class="form-control">
                                    <input type="hidden"  name ='hiddennomc' id="hiddennomc" value="" class="form-control">
                                    <input type="hidden"  name ='hiddenidc' id="hiddenidc" value="" class="form-control">
                                    <input type="hidden"  name ='hiddendatec' id="hiddendatec" value="" class="form-control">
                                    <input type="hidden"  name ='hiddenidabsencec' id="hiddenidabsencec" value="" class="form-control">
                                    <input type="hidden"  name ='dptc' id="dptc" value="<?php echo $departement; ?>" class="form-control">
						        </div>
                            </div>
						</div>
						<div class="row">
						    <div class="col-xs-8">
						        <label for="texte" >Corps du message : </label>
						        <textarea width="700" height="700" id="txtcorpsc" name="txtcorpsc" rows="60*60*24" ><?php echo $corpsmailc ?></textarea>
						        <br /><br />
					        </div>
					    </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="cbox3">Mettre l'élève en copie du mail</label>
                                    </div>
                                    <input type='checkbox' name='ckbcopieEleve'>
                                    <label for="cbox2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="cbox3">Traiter les cas après envoi du mail</label>
                                    </div>
                                        <input type='checkbox' name='ckbtraiter' value='<?php if (isset($rs['id_eleve'])) echo $rs['id_eleve'].$rs['abs_date'] ;?>."'>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <label for="copiemail">Déstinataires </label>
                                <div id="destinationc" class="absencedest">
                                </div>
                                <i class="fa fa-caret-down" id='montrerdestc' onclick='showhide()'></i>
													<input type='hidden' name="cache" value=""/>
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
                        <input type="submit" class="btn btn-danger" value="Envoyer" name="frmConSubmit" />
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
  </div>
    <!-- fin Modal add stage -->
    <!-- absences aux cours professionnels -> chkmailabsence <- Concerne : absences aux cours professionnels -->
    <div class="modal fade" id="modalenvoiMail" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Absence à traiter: Mail aux entreprises</h4>
                </div>
            <div class="modal-body">
                <form name="frmAddConvocation" id="frmAddConvocation" action="./chkForm/chkmailabsence.php" method="POST">
                    <a class="btn btn-primary" href=""  id="txtlocal"> Local </a>
                    &nbsp;&nbsp;&nbsp;	<a class="btn btn-primary" href=""  id="txtlocalentr"> Local (Copie Entreprise)</a>
                    <br><br><br>
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
                                   <input type="hidden"  name ='hiddenidabsence' id="hiddenidabsence" value="" class="form-control">
                                   <input type="hidden"  name ='dpt' id="dpt" value="<?php echo $departement; ?>" class="form-control">
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
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label for="cbox3">Mettre l'élève en copie du mail</label>
                                    </div>
                                    <input type='checkbox' name='ckbcopieEleve'>
                                    <label for="cbox2">
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="cbox3">Traiter les cas après envoi du mail</label>
                                </div>  <input type='checkbox' name='ckbtraiter' value='<?php if (isset($rs['id_eleve'])) echo $rs['id_eleve'].$rs['abs_date'] ;?>."'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label for="copiemail">Déstinataires </label>
                            <div id="destination" class="absencedest">
                            </div>
                            <i class="fa fa-caret-down" id='montrerdest' onclick='showhide()'></i>
                            <input type='hidden' name="cache" value=""/>
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
                    <input type="submit" class="btn btn-danger" id="emailEntrepriseBtn" value="Envoyer" name="frmConSubmit" />
                </form>
        </div>
        <div class="modal-footer">
        </div>
      </div>
        </div>
    </div>
<!-- fin Modal add stage -->
<!-- Modal add convocation -->
    <div class="modal fade" id="modalenvoiMailSent" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Absence à traiter: Mail aux entreprises</h4>
                </div>
                <div class="modal-body">
                    <i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i> En cours d\'execution !
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
<!-- fin Modal add confirmation envoie  -->
    <!-- Modal add convocation -->
    <div class="modal fade" id="modalenvoiMailSent2" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Absence à traiter: Mail de convocation</h4>
                </div>
                <div class="modal-body">
                    <i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i> En cours d\'execution !
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
                        <!-- fin Modal add confirmation envoie  -->
</body>

    <!-- /#wrapper -->


    <!-- Bootstrap Core JavaScript -->
    <!--<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>-->

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
 


	function showhide() {
    var x = document.getElementById('destination');
    if (x.style.display === 'none') {
        x.style.display = 'block';
		document.getElementById("montrerdest").className = "fa fa-caret-up";
    } else {
        x.style.display = 'none';
		document.getElementById("montrerdest").className = "fa fa-caret-down";
    }
	var x = document.getElementById('destinationc');
    if (x.style.display === 'none') {
        x.style.display = 'block';
		document.getElementById("montrerdestc").className = "fa fa-caret-up";
    } else {
        x.style.display = 'none';
		document.getElementById("montrerdestc").className = "fa fa-caret-down";
    }
}
	
		</script>
		
	
		
		
	<script type="text/javascript">
		tinyMCE.init({
			
			mode : "textareas",
			 width : "640",
  height : "300"
		});
	
	</script>
    <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js" async></script>
	 <!-- Include all compiled plugins (below), or include individual files as needed -->
    
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
	

     
	<script >
     
	 $('#myData').dataTable({
        "order": [[ 5, "desc" ]]
    });


    </script>
	 <script>
        // Permet de fixer le header du tableau lorsque l'utilisateur scroll
        var element = $(".keep-visible");
       if (element.length) element.data("original-offset", element.offset().top)	;
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




$("#checkAll").click(function () {//console.log('CECHK');
     $('#myData input:checkbox').not(this).prop('checked', this.checked);}); 






$('#frmAddConvocation2').submit(function(e){ 

//$('#modalenvoiMail .modal-body').html('');
	$('#modalenvoiMailc').modal('hide'); 	$('#modalenvoiMailSent2').modal('show'); 
 		$.ajax({
           type: "POST",
           url: $(this).attr('action'),
           data: $(this).serialize(), // serializes the form's elements.
           success: function(data){
           	$('#modalenvoiMailSent2 .modal-body').html(data);

               // setTimeout(function(){     location.reload(); }, 3000);
 




           	// show response from the php script.
           }
         });



//console.log('Hi!!');

    e.preventDefault();      
    // do something
});














$('#frmAddConvocation').submit(function(e){ 

//$('#modalenvoiMail .modal-body').html('');
	$('#modalenvoiMail').modal('hide'); $('#modalenvoiMailSent').modal('show'); 
 		$.ajax({
           type: "POST",
           url: $(this).attr('action'),
           data: $(this).serialize(), // serializes the form's elements.
           success: function(data)
           {
           	$('#modalenvoiMailSent .modal-body').html(data);
               // setTimeout(function(){     location.reload(); }, 3000);



           	 // show response from the php script.
           }
         });



//console.log('Hi!!');

    e.preventDefault();      
    // do something
});







$(document).ready(function(){
		
 if (  $('[data-toggle="tooltip"]').length )    $('[data-toggle="tooltip"]').tooltip(); });
	
</script>

</body>

</html>
