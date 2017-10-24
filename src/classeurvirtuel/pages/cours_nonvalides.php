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
if(isset($_GET['dpt'])){

    $departement = $_GET['dpt'];
}

if(isset($_GET['date']) && isset($_GET['cours'])){
    $sql=" INSERT INTO t_presprof (pre_date, idx_cours) VALUES ('".$_GET['date']."', '".$_GET['cours']."')";
    $change=$bdd->prepare($sql);
    $change->execute();
}





include_once('../include/mysql.inc.php');
//Va cherhcer les informations de l'élève
$bd=new dbIfc();
//$tabStudent=$bd->GetStudent($_GET['stu']);


//recherche des cours non validés



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
        
		
		
		
		
			<section class="content" style="min-height: 538px;">
        
<div class="col-xs-12">
    <div class="col-lg-8 col-sm-8 col-xs-12 no-padding edusecArLangCss"><h3 class="box-title"><i class="fa fa-th-list"></i> Cours non-validés</h3></div>
    <div class="col-lg-4 col-sm-4 col-xs-12 no-padding" style="padding-top: 20px !important;">
        <div class="col-xs-12 right-padding">
                  </div>
       
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


        <div id="accordion" class="panel-group">
<?php

$startDate=time();
$startDate += -60*-60*-24;
$finishdate=strtotime("2017-08-21");
while ($startDate > $finishdate)
{
    // Cherche si la classe a cours
    $requete=$bdd->prepare("SELECT id_classe FROM t_classe WHERE idx_departement='".$_GET['dpt'] ."';");
    $requete->execute();
    $classe=$requete->fetchAll();

    foreach ($classe as $classeencours)
    {
        $tabSchedule=$bd->GetSchedule($classeencours['id_classe'], $startDate);

        foreach(array_reverse($tabSchedule) as $entry)
        {
            $nombredecours=$entry['cou_duree'];

                $dateatester=date('Y-m-d', $startDate);
                $requete=$bdd->prepare("SELECT id_presprof FROM t_presprof WHERE pre_date ='".$dateatester."' AND idx_cours ='".$entry['id_sdh']."';");
                $requete->execute();
                if(!empty($requete->fetchAll(PDO::FETCH_ASSOC)))
                {

                }
                else
                {

                    $nomcours=$entry['cou_matlibelle'];
                    $nomclasse=$entry['cla_nom'];

                    //echo date('d/m/Y',$startDate)."-".$nomcours."-".$nomclasse."<br>";
                    $requete->closeCursor();

                    echo "

                
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h4 class='panel-title'>
 			        
					<table style='table-layout:fixed; width: 620px;'>
					<tr>
					<td data-toggle='collapse' data-parent='#accordion' style='width: 200px;  margin: auto; border-collapse: separate;'>". $nomclasse ." 
					</td>
					<td data-toggle='collapse' data-parent='#accordion' style='width: 200px;  margin: auto; border-collapse: separate;'>" .$nomcours ."</td>
					<td data-toggle='collapse' data-parent='#accordion' style='width: 200px;  margin: auto; border-collapse: separate;'> Nombre de périodes : " . $nombredecours ."</td>
					<td data-toggle='collapse' data-parent='#accordion' style='width: 200px;  margin: auto; border-collapse: separate;'> Date : " . date('d/m/Y',$startDate) ."</td>
					<td style='width: 200px;  margin: auto;border-collapse: separate;'>  
					     

					<a class='btn btn-block btn-warning'  href='cours_nonvalides.php?dpt=".$departement."&date=".$dateatester."&cours=".$entry['id_sdh']."' >Valider</a></a></td></tr></table></h4></div></div>
					";



                }
            //}
        }
    }
    $startDate += -60*-60*-24; // -1 jour
}




unset($bd);


$alreadydone=false;
//Traitement des informations de l'élève






?>
 </div>

	 <script>
		
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


                        <!-- fin Modal add stage -->

		
		</body>
		
		


	
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
