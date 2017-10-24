<?php

session_start();
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

    include '../include/bdd.php';
	
$insValidee="";
	
$user_idS = $_SESSION['user_id'];

$SelProf=$bdd->prepare("SELECT pro_nom, pro_prenom, idx_departement, pro_typedecours, pro_typeenseignant, pro_codebarre, pro_statut FROM t_professeur WHERE id_professeur=$user_idS");
$SelProf->execute();
$prof=$SelProf->fetch();

$idDPT = $prof['idx_departement'];

$SelProfDPT=$bdd->prepare("SELECT dep_nom  FROM t_departement WHERE id_departement=$idDPT");
$SelProfDPT->execute();
$profDPT=$SelProfDPT->fetch();

$dptNom = $profDPT['dep_nom'];
	
if(isset($_POST['inscription'])){
	
	if ($_GET['send']=="yes"){
		
		$user_idP=@$_POST['user'];
			
		$depart=@$_POST['optDepart'];
			
		$lundi=@$_POST['lundi'];
		$mardi=@$_POST['mardi'];
		$mercredi=@$_POST['mercredi'];
		$jeudi=@$_POST['jeudi'];
		$vendredi=@$_POST['vendredi'];
			
		$lundiMercure=@$_POST['lundiMercure'];
		$mardiMercure=@$_POST['mardiMercure'];
		$mercrediMercure=@$_POST['mercrediMercure'];
		$jeudiMercure=@$_POST['jeudiMercure'];
		$vendrediMercure=@$_POST['vendrediMercure'];
			
		if($depart!="" AND ($lundi!="" OR $mardi!="" OR $mercredi!="" OR $jeudi!="" OR $vendredi!="")){
			
			$saveIn=$bdd->prepare("INSERT INTO `t_sporthiver_prof`(`fk_professeur`, `sp_lieuDepart`, `sp_lundi`, `sp_mardi`, `sp_mercredi`, `sp_jeudi`, `sp_vendredi`, `sp_lundi_Mercure`, `sp_mardi_Mercure`, `sp_mercredi_Mercure`, `sp_jeudi_Mercure`, `sp_vendredi_Mercure`) VALUES ($user_idP, '$depart', '$lundi', '$mardi', '$mercredi', '$jeudi', '$vendredi', '$lundiMercure', '$mardiMercure', '$mercrediMercure', '$jeudiMercure', '$vendrediMercure')");
			$saveIn->execute();
			
		}else {
			header('Location: inscription_sportive_hiver_prof.php?message=no');
			exit;
		}
		
		unset($_POST);
		header('Location: inscription_sportive_hiver_prof.php');
	}
	
}

$SelIns=$bdd->prepare("SELECT * FROM t_sporthiver_prof WHERE fk_professeur=$user_idS");
$SelIns->execute();
$Insc=$SelIns->rowCount();
$InscInfo=$SelIns->fetch();

/*print_r($InscInfo);
echo "<br>";
echo $InscInfo['sp_lieuDepart'];

echo $Insc;
*/

if($Insc!=0){$insValidee="oui";}

	?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CEPM Inscription hiver professeur</title>
    
    
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
			<?php if(isset($_GET['message']) AND $_GET['message']=="no"){ ?>
			<div class="alert alert-danger">
				<strong>Attention</strong> Votre inscription n'a pas été validée car vous n'avez pas choisi de lieux de départ ou d'activités.
			</div>
			<?php }?>
				<div class="col-lg-12">
					<h1 class="page-header">Inscription semaine sportive d'hiver (Professeur)</h1>
                </div>
				<!--content-->
				<h3>Prénom, Nom : <b><?php echo $prof['pro_prenom'] . " " . $prof['pro_nom']; ?></b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Taux : <b>[?]</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DPT : <b><?php echo $dptNom; ?></b></h3>
			  <p><font color="red"><b>Les enseignants qui ne peuvent pas participer à la semaine sportive d'hiver sont priés de s'annoncer auprès du directeur.</b></font></p>
			  <p>LE DEPLACEMENT EN CAR EST OBLIGATOIRE</p>
			  <?php if($insValidee!="oui"){ ?>
			  <form method="POST" action="inscription_sportive_hiver_prof.php?send=yes">
			  <?php } ?>
			  <input type="hidden" name="user" value="<?php echo $user_idS; ?>">
			  <table class="table table-striped table-bordered center">
				<tbody>
				  <tr>
				  <td></td><td></td><td></td>
					<td>Départ Morges à 06h20 <br> (parking Marcelin) <br>
					<?php if($insValidee!="oui"){ ?>
						<div class="radio">
						  <label><input type="radio" name="optDepart" value="morges"></label>
						</div>
					<?php }elseif ($InscInfo['sp_lieuDepart'] == "morges") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>Départ Lausanne à 07h30 <br> (place de la gare) <br>
					<?php if($insValidee!="oui"){ ?>
						<div class="radio">
						  <label><input type="radio" name="optDepart" value="lausanne"></label>
						</div>
					<?php }elseif ($InscInfo['sp_lieuDepart'] == "lausanne") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>Départ Aigle à 08h15 <br> (place de la gare) <br>
					<?php if($insValidee!="oui"){ ?>
						<div class="radio">
						  <label><input type="radio" name="optDepart" value="aigle"></label>
						</div>
					<?php }elseif ($InscInfo['sp_lieuDepart'] == "aigle") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
				  </tr>
				  
				  <tr>
				  <td></td><td></td>
				  <td></td><td></td>
				  <td></td><td></td>
				  </tr>
				<!--</tbody>
			  </table>
			          
			  <table class="table table-striped table-bordered center">
				<tbody>-->
				  <tr>
					<td><b>Activité</b></td>
					<td><b>Lundi <br> 22 janv.</b></td>
					<td><b>Mardi <br> 23 janv.</b></td>
					<td><b>Mercredi <br> 24 janv.</b></td>
					<td><b>Jeudi <br> 25 janv.</b></td>
					<td><b>Vendredi <br> 26 janv.</b></td>
				  </tr>
				  <tr>
					<td><b>Uniquement <br> l'accompagnement dans les cars</b></td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="lundi" value="bus"></label>
					</div>
					<?php }elseif ($InscInfo['sp_lundi'] == "bus") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="mardi" value="bus"></label>
					</div>
					<?php }elseif ($InscInfo['sp_mardi'] == "bus") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="mercredi" value="bus"></label>
					</div>
					<?php }elseif ($InscInfo['sp_mercredi'] == "bus") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="jeudi" value="bus"></label>
					</div>
					<?php }elseif ($InscInfo['sp_jeudi'] == "bus") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="vendredi" value="bus"></label>
					</div>
					<?php }elseif ($InscInfo['sp_vendredi'] == "bus") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
				  </tr>
				  <tr>
					<td><b>Ski</b> <br> <i>(CHF 20.- par jour à payer en <br> semaine 49 au secrétariat)</i></td>
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="lundi" value="ski"></label>
					</div>
					<?php }elseif ($InscInfo['sp_lundi'] == "ski") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="mardi" value="ski"></label>
					</div>
					<?php }elseif ($InscInfo['sp_mardi'] == "ski") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="mercredi" value="ski"></label>
					</div>
					<?php }elseif ($InscInfo['sp_mercredi'] == "ski") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="jeudi" value="ski"></label>
					</div>
					<?php }elseif ($InscInfo['sp_jeudi'] == "ski") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="vendredi" value="ski"></label>
					</div>
					<?php }elseif ($InscInfo['sp_vendredi'] == "ski") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
				  </tr>
				  <tr>
					<td><b>Raquette</b> <br>  Randonnée accompagnée d'un <br> guide environ 4 heures (pique- <br> nique et boisson à prendre avec soi) <br> <i>(CHF 20.- par jour à payer en <br> semaine 49 au secrétariat)</i></td>
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="lundi" value="raquette"></label>
					</div>
					<?php }elseif ($InscInfo['sp_lundi'] == "raquette") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="mardi" value="raquette"></label>
					</div>
					<?php }elseif ($InscInfo['sp_mardi'] == "raquette") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="mercredi" value="raquette"></label>
					</div>
					<?php }elseif ($InscInfo['sp_mercredi'] == "raquette") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="jeudi" value="raquette"></label>
					</div>
					<?php }elseif ($InscInfo['sp_jeudi'] == "raquette") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					<div class="radio">
						  <label><input type="radio" name="vendredi" value="raquette"></label>
					</div>
					<?php }elseif ($InscInfo['sp_vendredi'] == "raquette") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
				  </tr>
				  <tr>
					<td><b>Utilisation de la salle <br> Hotel Mercure</b></td>
					<td>
					<?php if($insValidee!="oui"){ ?>
					  <input type="checkbox" name="lundiMercure" value="oui"><br>
					<?php }elseif ($InscInfo['sp_lundi_Mercure'] == "oui") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					  <input type="checkbox" name="mardiMercure" value="oui"><br>
					<?php }elseif ($InscInfo['sp_mardi_Mercure'] == "oui") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					  <input type="checkbox" name="mercrediMercure" value="oui"><br>
					<?php }elseif ($InscInfo['sp_mercredi_Mercure'] == "oui") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					  <input type="checkbox" name="jeudiMercure" value="oui"><br>
					<?php }elseif ($InscInfo['sp_jeudi_Mercure'] == "oui") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
					
					<td>
					<?php if($insValidee!="oui"){ ?>
					  <input type="checkbox" name="vendrediMercure" value="oui"><br>
					<?php }elseif ($InscInfo['sp_vendredi_Mercure'] == "oui") { ?>
					<h4><span class="glyphicon glyphicon-ok"></span></h4>
					<?php } ?>
					</td>
				  </tr>
					<tr>
					<td></td><td></td>
					<td></td><td></td>
					<td></td><td></td>
					</tr>
					<!--</tbody>
			  </table>
			          
			  <table class="table table-striped table-bordered center">
				<tbody>-->
				  <tr>
					<td><b>Taux contractuel</b> <br> (y compris décharge)</td>
					<td>0 à 19%</td>
					<td>20 à 39%</td>
					<td>40 à 59%</td>
					<td>60 à 79%</td>
					<td>80 à 100%</td>
				  </tr>
				  <tr>
					<td><b>Nombre de période</b></td>
					<td>0 à 4</td>
					<td>5 à 9</td>
					<td>10 à 14</td>
					<td>15 à 19</td>
					<td>20 à 25</td>
				  </tr>
				  <tr>
					<td>Votre participation <br> en nombre de jours</td>
					<td>0</td>
					<td>1</td>
					<td>2</td>
					<td>3</td>
					<td>4</td>
				  </tr>
				</tbody>
			  </table>
			  <?php if($insValidee!="oui"){ ?>
			  <input type="submit" class="btn btn-lg btn-primary btn-block" name="inscription" ></input>
			  </form>
			  <br>
			  <div class="center">
			  <p><font color="red">Une fois validée, l'inscription ne sera plus modifiable mais il sera toujours possible de la voir ici même</font></p>
			  </div>
			  <?php } ?>
			  
				<!--end content-->
			</div>
		</div>
	</div><!--end wrapper-->
</body>
</html>