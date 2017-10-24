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
include_once('../include/mysql.inc.php');
include_once('../api/api.inc.php');

$previous = "javascript:history.go(-1)";

if(isset($_SERVER['HTTP_REFERER'])) { $previous = $_SERVER['HTTP_REFERER']; } 

//ini_set( 'upload_tmp_dir', '/home/cepv/domains/cepv.ch/private_html/absences/pages/tmp' );

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}
$stu = 0 ;
if (!isset($_GET['stu']) )
{
    header("Location:management.php");
}
if (!is_numeric($_GET['stu']) )
    header("Location:management.php");


if ( isset($_GET['stu']) ) 
	if ( is_numeric($_GET['stu']) )
		$stu = $_GET['stu'] ;

if ( isset($_POST['stu']) ) 
	if ( is_numeric( $_POST['stu'] ) )
		$stu = $_GET['stu'] = $_POST['stu'] ;

if(isset($_POST['choixClasse'])){

    $sql="update t_eleve set idx_classe =(select id_classe from t_classe where id_classe='".$_POST['choixClasse']."') where id_codebarre='".$_GET['stu']."'";
    $change=$bdd->prepare($sql);
    $change->execute();
}






if ( ( isset($_GET['newElv'])) && (isset($_GET['newElv']))) {
	//echo $_GET['newElv'] ;
		$enom = $_POST['ele_nom'] ; 
		$eprenom = $_POST['ele_prenom'] ;
		$ecodebarre = $_POST['id_codebarre'];
		if ( is_numeric($ecodebarre) )
		{
			$testel = "SELECT * FROM t_eleve WHERE id_codebarre='" . $ecodebarre . "' LIMIT 1" ;
			$req=$bdd->prepare($testel);
			$req->execute();
			$teststudent = $req->fetchAll();
			$existst = 0;
			foreach($teststudent as $entry)
			{
				$existst++;
				print_r($entry);
			}
			
			if ( $existst <= 0 )
			{
				$eleve="INSERT INTO t_eleve ( ele_nom, ele_prenom, id_codebarre ) values ('". $enom ."', '" . $eprenom . "', '" . $ecodebarre . "' )";
				$req=$bdd->prepare($eleve);
				$req->execute();
				header("Location:student_dtl.php?stu=$ecodebarre");
				$_GET['stu'] = $_POST['id_codebarre'] ;
			} else {
				die("<html><head><meta http-equiv=\"refresh\" content=\"2;URL='$previous'\" /></head><body>Erreur : Code Barre déjà assigné </body></html>");
			}
		}
}

if((isset($_GET['delete'])) && (isset($_GET['stage']))) {

    $supstage="delete from  t_stage where id_stage='".$_GET['stage']."'";
    $req=$bdd->prepare($supstage);
    $req->execute();

}

if(isset($_GET['supdisp'])){
    if($_GET['supdisp']=="oui"){
      
        $eleve="update t_dispense set dis_datefin = now(),dis_actif =0 where idx_eleve ='".$_GET['stu']."' and idx_cours='".$_GET['cou']."' and dis_actif ='1' ";
        $req=$bdd->prepare($eleve);
        $req->execute();
		echo "<script>$('#dispenseeleve').modal('show')</script>"; 
    }
}


if(isset($_POST['dispense'])){
    if($_POST['dispense']==""){
        
		
		
		$requete6=$bdd->prepare("SELECT id_sdh FROM t_sdh  where cou_matcode='".$_POST['choixCours']."'");

$requete6->execute();
$Coursid=$requete6->fetchAll();


        $eleve="INSERT INTO t_dispense (dis_datecreation,dis_actif,idx_cours,idx_eleve,idx_professeur)values (  now(), '1', '".$Coursid[0]['id_sdh']."', '".$_GET['stu']."', '".$_SESSION['user_id']."')";
        $req=$bdd->prepare($eleve);
        $req->execute();
		  echo "<script>$('#dispenseeleve').modal('show')</script>"; 
        
    }
}
else
   $_GET['dispense']="";

if(isset($_POST['dateRupture'])){
    //if($_GET['rupture']=="oui" || $_GET['rupture']=="Non" ){
    if ( !isset( $_POST['ChoixSuivreCours'] )  && !isset( $_POST['dateRupture'] )  && !isset( $_GET['stu'] ) )
		die( "is not in ChoixSuivreCours dateRupture stu <br>" );
	else {
		$dateRupture =  str_replace( "'", "", $_POST['dateRupture'] ) ;
		$choixSuivreCours = str_replace( "'", "", $_POST['ChoixSuivreCours'] ) ; //, `DateModif` , now()
			
		if( $choixSuivreCours == "Oui" || $choixSuivreCours == "Non" ){

			$eleve="INSERT INTO `t_rupture` ( `date_rupture`, `idx_codebarre`, `suit_cours`, `Modifieur`) VALUES
		( '".$dateRupture."', '".$_GET['stu']."', '".$choixSuivreCours."', '".$_SESSION['user_id']."' )";
			//echo $eleve; 
			$req=$bdd->prepare($eleve);
			$req->execute();
		}else {
				die( "is not in ChoixSuivreCours :$choixSuivreCours;<br>" );
		}
	}

}



$requete3=$bdd->prepare("SELECT distinct  (cou_matlibelle),cou_matcode  FROM t_sdh 
where idx_classe in (select idx_classe from t_eleve where id_codebarre ='".$_GET['stu']."' )   
ORDER BY cou_matcode ASC
");
$requete3->execute();
$Cours=$requete3->fetchAll();


$requete6=$bdd->prepare("select * from t_sdh where id_sdh in(select idx_cours from t_dispense  where dis_actif='1' and idx_eleve='".$_GET['stu']."') ORDER BY cou_matlibelle ASC");
$requete6->execute();
$Dispense=$requete6->fetchAll();




// vérifie qu' une demande de suppression a été envoyée



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
    if(@$_POST['mai_nom']=="-"){$_POST['mai_nom']="";}
    if(@$_POST['mai_prenom']=="-"){$_POST['mai_prenom']="";}
    if(@$_POST['mai_tel1']=="-"){$_POST['mai_tel1']="0";}elseif(@$_POST['mai_tel1']==""){$_POST['mai_tel1']="0";}
    if(@$_POST['mai_tel2']=="-"){$_POST['mai_tel2']="0";}elseif(@$_POST['mai_tel2']==""){$_POST['mai_tel2']="0";}
    if(@$_POST['mai_mobile']=="-"){$_POST['mai_mobile']="0";}elseif(@$_POST['mai_mobile']==""){$_POST['mai_mobile']="0";}

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
//Va cherhcer les informations de l'élève
$bd=new dbIfc();
$tabStudent=$bd->GetStudent($_GET['stu']);
$tabInternShip = $bd->GetStage($tabStudent[0]['id_eleve']);
$tabEnStage = $bd->GetCurrentStage($tabStudent[0]['id_eleve']);
unset($bd);

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

    $ele_politesse=$entry['ele_politesse'];
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
	if ($ent_id == '' )
		$ent_id='0';
    $ent_nom=$entry['ent_nom'];
    $ent_rue=$entry['ent_rue'];
    $ent_npa=$entry['ent_npa'];
    $ent_localite=$entry['ent_localite'];
    $ent_mail=$entry['ent_mail'];
    $ent_tel1=$entry['ent_tel1'];
    $ent_tel2=$entry['ent_tel2'];
    $mai_id=$entry['idx_maitredapprentissage'];
    $mai_nom=$entry['mai_nom'];
    $mai_prenom=$entry['mai_prenom'];
    $mai_tel1=$entry['mai_tel1'];
    $mai_tel2=$entry['mai_tel2'];
    $mai_mobile=$entry['mai_mobile'];
    $idx_classe_ele=$entry['idx_classe'];
	/*
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
	*/
    $rep_politesse=$entry['rep_politesse'];
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
//$requete4=$bdd->prepare("SELECT count(*) as 'rupture' FROM t_rupture where idx_codebarre='".$_GET['stu']."'");
$requete4=$bdd->prepare("SELECT * FROM t_rupture where idx_codebarre='".$_GET['stu']."' ORDER BY id_rupture DESC LIMIT 1");
$requete4->execute();
$rupture=$requete4->fetchAll();
//echo return_array($rupture, true );

$requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
$requete->execute();
$classe=$requete->fetchAll();
$classeKG=$classe;

$requete1=$bdd->prepare('SELECT * FROM t_entreprise ORDER BY ent_nom ASC');
$requete1->execute();
$Allentreprise=$requete1->fetchAll();

/*$sqlm="SELECT * FROM t_maitredapprentissage where idx_entreprise=(SELECT id_entreprise from t_entreprise where ent_nom='".$ent_nom."' LIMIT 1)";
$requete2=$bdd->prepare($sqlm);
$requete2->execute();
$maitre=$requete2->fetchAll();*/

$sqlm="SELECT * FROM t_maitredapprentissage ORDER BY mai_nom ASC";
$requete2=$bdd->prepare($sqlm);
$requete2->execute();
$maitre=$requete2->fetchAll();

$requete3=$bdd->prepare('SELECT * FROM t_representantlegal ORDER BY rep_nom ASC');
$requete3->execute();
$representant=$requete3->fetchAll();

//KG Get classe
$requete3=$bdd->prepare("SELECT * FROM t_classe WHERE id_classe='".$idx_classe_ele."' ");
$requete3->execute();
$detclasse=$requete3->fetchAll();


$requete3=$bdd->prepare("SELECT * FROM t_departement JOIN t_classe on t_classe.idx_departement=t_departement.id_departement WHERE id_classe='".$idx_classe_ele."'");
$requete3->execute();
$detdepartement=$requete3->fetchAll();

$requete3=$bdd->prepare("SELECT id_eleve FROM t_eleve WHERE idx_classe='".$idx_classe_ele."'");
$requete3->execute();
$detnbeleve=$requete3->fetchAll();





//Récupère les absences, arrivées tardives et les mises à la porte
$bd=new dbIfc();
$tabStudentDoor=$bd->GetStudentDoor($_GET['stu']);
$tabStudentLate=$bd->GetStudentLate($_GET['stu']);
$tabStudentMed = $bd->GetStudentGym($_GET['stu']);
$tabStudentGym = $bd->GetStudentGym($_GET['stu']);
$tabMissings=$bd->GetStudentMissing($_GET['stu']);
$tabNotices=$bd->GetStudentNotices($_GET['stu']);
unset($bd);
$string_firstname 	= str_replace(' ', '', $ele_prenom);
$string_lastname 	= str_replace(' ', '', $ele_nom);
$av_name 			= $string_lastname."_".$string_firstname;
$av_file = "images/utilisateurs/$stu.jpg";
$av_exist = 0;
if ( isset( $_FILES['avatar'] ) ) {
   if ( isset( $_FILES['avatar'] ) AND $_FILES['avatar']['error'] == 0 ) {
	   if ( $_FILES['avatar']['size'] <= 1000000 ) {
		   $infosfichier = pathinfo( $_FILES['avatar']['name'] ) ;
		   $extension_upload = $infosfichier['extension'];
		   $extensions_autorisees = array( 'jpg', 'jpeg', 'gif', 'png' ) ;
		   if ( in_array( $extension_upload, $extensions_autorisees ) ) {
			   //echo "";
			   move_uploaded_file( $_FILES['avatar']['tmp_name'],  $av_file );
			   echo "L'envoi a bien été effectué !";
		   }
	   }
   }
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

    <title>CEPV Scan System V2.0</title>

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
            <div class="col-lg-3 table-responsive edusec-pf-border no-padding edusecArLangCss" style="margin-bottom:15px">
                <div class="col-md-12 text-center">

                    <div class="student-container">
                        <style>
                            img {
                                display: block;
                                max-width:100%;
                                max-height:100%;
                                width: auto;
                                height: auto;
                            }
                        </style>
                        <?php
                        $string_firstname = str_replace(' ', '', $ele_prenom);
                        $string_lastname = str_replace(' ', '', $ele_nom);
                        $string=$id_codebarre;
                        $filename = "images/utilisateurs/$string.jpg";
                        $filename2 = "images/utilisateurs/$string.JPG";

                        if (file_exists($filename)) {
                            echo "<img height='350px' width='272' class='student-thumbnail' alt='Alain Dupré' src='images/utilisateurs/$string.jpg'>";
                        } else {
                            if(file_exists($filename2))
                            {
                                echo "<img height='350px' width='272' class='student-thumbnail' alt='Alain Dupré' src='images/utilisateurs/$string.JPG'>";
                            }
                            else
                            {

                                echo "<img class='student-thumbnail' alt='Alain Dupré' src='images/utilisateurs/usermale.png'>";
                            }
                        }
                        ?>
                    </div>



                </div>
                <table class="table table-striped">
                    <tbody>
					<?php if ( $av_exist == 0 )
					{
					?><tr>
                        <th>Photo :</th>
                        <td><form action="student_dtl.php" method="POST" enctype="multipart/form-data" class="idealforms"><input  name="stu" type="hidden"  value='<?php echo $id_codebarre  ;?>'><input  name="avatar" type="file"  value=''>
    <input type="hidden" name="MAX_FILE_SIZE" value="1234567" /><br><input type="submit" value="Modifier"></form></td>
                    </tr>
					<?php 
					} ?>
					<tr>
                        <th>Code barre:</th>
                        <td><?php echo $id_codebarre  ;?></td>
                    </tr>
                    <tr>
                        <th>Classe:</th>
                        <td><?php echo $cla_nom  ;?></td>
                    </tr>
                    <tr>
                        <th>Nom :</th>
                        <td><?php echo $ele_nom ." ". $ele_prenom ;?></td>
                    </tr>
                    <tr>
                        <th>Email :</th>
                        <td><?php echo $ele_mail ; ?></td>
                    </tr>
                    <tr>
                        <th>Téléphone :</th>
                        <td><?php echo $ele_numeromobile ; ?></td>
                    </tr>
                    <tr>
                        <th>Statut :</th>
                        <td>
                            <?php if ( @$rupture['0']['suit_cours'] == 'Oui' ) { 
                            
									echo	"<span class='label label-success'>Actif</span>";
							}elseif ( @$rupture['0']['suit_cours'] == 'Non' ) { 
									echo	"<span class='label label-danger'>Rupture</span>";
							}else{
								echo	"<span class='label label-success'>Actif</span>";
                                //echo	"<span class='label label-danger'>Rupture</span>";
                                ///echo	"<span class='label label-danger'>".$rupture['0']."</span>";

							}                           ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Responsabilité :</th>
                        <td>
                            <?php
                            //Contrôle de l'âge
                            $date = new DateTime();
                            $date_18 = $date->sub(new DateInterval('P18Y'));

                            function validateDate($date)
                            {
                                $d = DateTime::createFromFormat('Y-m-d', $date);
                                return $d && $d->format('Y-m-d') === $date;
                            }

                            $var = str_replace('/', '-', $ele_datedenaissance);
                            $var = date('Y-m-d', strtotime($var));


                            if($ele_datedenaissance != "" && validateDate($var)==true)
                            {
                                $date_naissance = new DateTime($var);
                                if($date_naissance >= $date_18)
                                {
                                    if ($ele_politesse =="M.") { echo "<span class='label label-danger'>Mineur</span>";} else { echo "<span class='label label-danger'>Mineure</span>";}

                                }
                                else
                                {
                                    if ($ele_politesse =="M.") { echo "<span class='label label-danger'>Majeur</span>";} else { echo "<span class='label label-danger'>Majeure</span>";}

                                }
                            }

                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Dispenses :</th>
                        <td>
                            <?php
							foreach($Dispense as $entry):
							?>
							<?php echo '<button type="button" class="btn btn-info">'.$entry['cou_matlibelle'].'</button>'; ?>
							<?php endforeach; 
							?>
                        </td>
                    </tr>

							
                    </tbody></table>
            </div>

            <div class="col-lg-9 profile-data">
                <ul class="nav nav-tabs responsive hidden-xs hidden-sm" id="profileTab">
                    <li class="active" id="personal-tab"><a href="#personal" data-toggle="tab" class="fa fa-user"><i class="fa fa-street-view"></i> Prive</a></li>
                    <!-- <li id="address-tab"><a href="#address" data-toggle="tab"><i class="fa fa-home"></i> Adresse</a></li> -->
                    <li id="guardians-tab"><a href="#classe" data-toggle="tab"><i class="fa fa-user"></i> Classe</a></li>
                    <li id="guardians-tab"><a href="#guardians" data-toggle="tab"><i class="fa fa-user"></i> Entreprise</a></li>
                    <li id="guardians-tab"><a href="#stats" data-toggle="tab" onclick="ComputePresenceRatio('e', '<?= addslashes($_GET['stu']) ?>', document.getElementById('presence-ratio-placeholder'));"><i class="fa fa-dashboard"></i> Statistiques</a></li>
                    <li id="guardians-tab"><a href="#absences" data-toggle="tab"><i class="fa fa-user"></i>  Absences</a></li>
                    <li id="guardians-tab"><a href="#retards" data-toggle="tab"><i class="fa fa-user"></i>  Arrivées tardives</a></li>
                    <li id="guardians-tab"><a href="#mp" data-toggle="tab"><i class="fa fa-user"></i>  Mises à la porte</a></li>
                    <li id="guardians-tab"><a href="#remarques" data-toggle="tab"><i class="fa fa-comment-o"></i>  Remarques</a></li>
                    <li id="guardians-tab"><a href="#stages" data-toggle="tab"><i class="fa fa-user"></i>  Stages</a></li>
                </ul>
                <div id="content" class="tab-content responsive hidden-xs hidden-sm">
                    <div class="tab-pane active" id="personal">
							<div class="row">
								<div class="col-xs-12">
									<h2 class="page-header">
										<i class="fa fa-info-circle"></i> Détails personnels
										<div class="pull-right">
											<!-- <a id="update-data" class="btn btn-primary btn-sm" href="student_edt.php?stu=<?php echo $_GET['stu']; ?>"><i class="fa fa-pencil-square-o"></i> Éditer</a> -->
											
											<button class='btn btn-danger' type='button' data-toggle='modal' data-target='#ruptureEleve'>  En rupture</button>
						    <?php 
						    ///echo "array count :".count($Dispense).";<br>";
						    if ( !count($Dispense) ) {
						    ?>
											<button class='btn btn-warning' type='button' data-toggle='modal' data-target='#DispenseEleve'>  Dispense</button>
						    <?php }
							?>
											<div class="btn btn-white"> Stage
													<a data-toggle="modal" data-target="#modalViewStage"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
													<a data-toggle="modal" data-target="#modalAddStage"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
											</div>
													<br>
										</div>
											<br>
									</h2>
								</div><!-- /.col -->
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="pull-left">
										<div class="econtainer" id="1editer" >
											<button type="button" class="btn" onclick="myEdit('tal'); togl('1editer'); togl('1edition'); ">Éditer</button>
										</div>
										<div class="econtainer" id="1edition" style="visibility:hidden;"  >
										<button type="button" id="detpers_r" class="btn" onclick="myReset('tal'); togl('1editer'); togl('1edition'); ">Annuler</button>
										<button type="button" id="detpers_s" class="btn" onclick="mySave('aForm')">Enregistrer</button>
										</div>
										<br>
									</div>
									
									<form id="aForm" action="../api/set-student.php?<?php echo "stuid=".$_GET['stu']."&rlgl=".$idx_replegal; ?>" method="post">
										<table id="tal" border="1" class="table table-bordered">
											<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
										</table>
									</form>
								</div> <!---COL XS 12--->
						   </div><!-- / div row -->
						   
						   <?php 
						   //echo "array count :".count($Dispense).";<br>";
						   if ( count($Dispense) ) {
						   ?>
						   <div class="row">
								<div class="col-xs-12"> <!-- col-md-12 col-lg-12">--->
									<h2 class="page-header">&nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Dispenses
									<div class="pull-right">
										<button class='btn btn-warning' type='button' data-toggle='modal' data-target='#DispenseEleve'>Dispense</button>
										<br>
									</div>
									<br>
									</h2>
								</div><!-- /.col -->
							</div>
							<br>
							<div class="row">						
								<div class="col-xs-12">
                                  <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cours Dispense</th>
												<th>Action</th>                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($Dispense as $entry): ?>
                                            <tr>
                                               <td><?php echo '<button type="button" class="btn btn-info">'.$entry['cou_matlibelle'].'</button>'; ?></td>
											   <td>	<a href='?supdisp=oui&cou=<?php echo $entry['id_sdh']?>&stu=<?php echo $_GET['stu']; ?>' title="Supprimer" class="glyphicon glyphicon-remove" data-confirm="Supprimer cet outil ?" data-method="post"></a></td>                                                
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
								</div> <!---COL XS 12--->
							</div> <!-- / div row -->
						   <?php }
						   ?>
							
							<?php
							if (1==1)
							{
							?>							
								   <div class="row">
										<div class="col-xs-12 col-md-12 col-lg-12">
											<h4 class="page-header">
												&nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Informations Complémentaires <div class="pull-right">
												</div>
											</h4>
										</div><!-- /.col -->
									</div>
									<div class="row">						
										<div class="col-xs-12">
											<!--
											<button type="button" class="btn" onclick="myEdit('tbl')">Éditer</button>
											<button type="button" class="btn" onclick="myReset('tbl')">Annuler</button>
											<button type="button" class="btn" onclick="mySave('bForm')">Enregistrer</button>
											-->
											<form id="bForm" action="../api/set-student.php?<?php echo "stuid=".$_GET['stu']."&rlgl=".$idx_replegal; ?>" method="post">
												<table id="tbl" border="1" class="table table-bordered">
													<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
												</table>
											</form>
										</div> <!---COL XS 12--->
									</div> <!-- / div row -->									
							<?php
							}
							?>
							
							<div class="row">
								<div class="col-xs-12 col-md-12 col-lg-12">
									<h4 class="page-header">
										&nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Représentant Légal	<div class="pull-right">
										</div>
									</h4>
								</div><!-- /.col -->
							</div> <!-- / div row -->
							<div class="row">						
								<div class="col-xs-12">
										<div class="econtainer" id="ed3ter" >
											<button type="button" class="btn" onclick="myEdit('tcl'); togl('3edition'); togl('ed3ter');">Éditer</button>
											
										</div>
										<div class="econtainer" id="3edition" style="visibility:hidden;"  >
											<?php /* if ($idx_replegal == 0) { ?>
														<button type="button" class="btn" onclick="myEdit('tcl')">+ Ajouter</button>
											  } */
											  
											  /*else { /*
														<button type="button" class="btn" onclick="myEdit('tcl')">Éditer</button>
											<?php /*} */?>
											<button type="button" class="btn" onclick="myReset('tcl'); ; togl('3edition'); togl('ed3ter');">Annuler</button>
											<button type="button" class="btn" onclick="mySave('cForm')">Enregistrer</button>
											
										</div>									
									<form id="cForm" action="../api/add-replegal.php?<?php echo "stuid=".$_GET['stu']."&rlgl=".$idx_replegal; ?>" method="post">
									<br>
										<table id="tcl" border="1" class="table table-bordered">
											<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
										</table>
									</form>
									<br><br>
									<br><br>
								</div> <!---COL XS 12--->
							</div> <!-- / div row -->
                    </div>

                    <!-- Onglet Classe -->
                    <div class="tab-pane" id="classe">
                        <div class="col-xs-12 ">
                            <h2 class="page-header">
                                &nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Classe<div class="pull-right"><button class="btn" type="button" data-toggle="modal" data-target="#assignclass">Assigner une classe</button><br>
                                </div></h2>
                                <?php

                                foreach($detclasse as $classe)
                                {
                                    $depclasse=$detdepartement[0]['dep_nom'];
                                    echo "<span style='font-weight: bold'>Departement : $depclasse</span><br/>";




                                    $classenom=$classe['cla_nom'];
                                    echo "Nom de la classe : $classenom<br/>";

                                    $classeniveau=$classe['cla_niveau'];
                                    echo "Niveau : $classeniveau<br/>";

                                    $classetype=$classe['cla_type'];
                                    echo "Type : $classetype<br/>";

                                    //CEPM
                                    $classejdc=$classe['cla_jourdecours'];
                                    //echo "Type : $classejdc<br/>";
                                    echo "Nombre d'élèves : ".count($detnbeleve)."";


                                }
                                ?>
                        </div><!-- /.col-xs-12 col-md-12 col-lg-12 -->
                    </div><!-- Onglet clase -->

                    <!-- Modal assignation d'une classe -->
                    <div class="modal fade" id="assignclass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Assigner une classe</h4>
                                </div>
                                <div class="modal-body">

                                    <form method="POST" action="?<?php echo "stu=".$_GET['stu']; ?>">
                                        <br>

                                        <div class="field">
                                            <label class="main">Cours:</label>
                                            <td>
                                                <select class="dropdown form-control" name="choixClasse">
                                                    <?php

                                                    foreach($classeKG as $listItem)
                                                    {
                                                        echo '<option value="'.$listItem['id_classe'].'">'.$listItem['cla_nom'].'</option>';
                                                    }


                                                    ?>
                                                </select> 
                                            </td>
                                        </div>
                                        <br>
                                        <!--- <button type="button" class="btn btn-secondary" onclick="UpdateStudentNotices('204158', CollectNotices())" style="text-align: right;">Mettre à jour</button> --->
                                        <div>
                                            <button type="submit" class="btn btn-success" name='' >Confirmer l'assignation</button>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- Fin du modal d'assignation de classe -->

                    <!-- STATISTIQUE PANE !-->
                    <div class="tab-pane" id="stats">
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
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




                    <!-- Tableau des Absences -->
                    <!-- Absences PANE !-->
                    <div class="tab-pane" id="absences">
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div id="miss" class="row">
                            <button type="button" onclick="UpdateStudentMissings(CollectMissings())" class="btn btn-secondary" style="text-align: right;">Mettre à jour</button>
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
                                            <th><?= date("d/m/Y", strtotime($entry['abs_date'])); ?></th>
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













                    </div>


                    <!-- Tableau des retard -->
                    <!-- retards PANE !-->
                    <div class="tab-pane" id="retards">
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div id="late" class="row">
                            <form><br>
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
                                                <th><?= date("d/m/Y", strtotime($entry['tar_date'])); ?></th>
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
                            </form>
                        </div>
                    </div>





			
	     <!-- Modal add stage -->
                        <div class="modal fade" id="modalAddStage" tabindex="-1" role="dialog" aria-labelledby="modalAddStage">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content" style="border-radius: 0;">
                                    <div class="modal-header" style="background: #2e6da4;color: white;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Ajouter un stage</h4>
                                    </div>
                                    <!-- sends the submitted form data to the page itself, instead of jumping to a different page. This way, the user will get error messages on the same page as the form. -->
                                    <div class="modal-body">
                                        <form name="frmAddInternShip" id="frmAddInternShip" action="./chkForm/chkInternShip.php" method="POST">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaDateDeb">Date début</label>
                                                        <input id="inpstaDateDeb" name="inpstaDateDeb" type="text" class="form-control date-picker">
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaDateFin">Date fin</label>
                                                        <input id="inpstaDateFin" name="inpstaDateFin" type="text" class="form-control date-picker">
                                                    </div>
                                                </div>
                                            </div>
                                            <h4 style="margin-bottom: 5px;">Entreprise</h4>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <label for="inpstaEntNom">Nom</label>
                                                        <input id="inpstaEntNom" name="inpstaEntNom" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <h4 style="margin-bottom: 5px;">Adresse</h4>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaEntRue">Rue</label>
                                                        <input id="inpstaEntRue" name="inpstaEntRue" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaEntNpa">NPA</label>
                                                        <input id="inpstaEntNpa" name="inpstaEntNpa" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-10">
                                                    <div class="form-group">
                                                        <label for="inpstaEntLocalite">Localité</label>
                                                        <input id="inpstaEntLocalite" name="inpstaEntLocalite" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-2">
                                                    <div class="form-group">
                                                        <label for="inpstaEntCanton">Canton</label>
                                                        <input id="inpstaEntCanton" name="inpstaEntCanton" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <h4 style="margin-bottom: 5px;">Contact</h4>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaEntConNom">Nom</label>
                                                        <input id="inpstaEntConNom" name="inpstaEntConNom" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaEntConPrenom">Prénom</label>
                                                        <input id="inpstaEntConPrenom" name="inpstaEntConPrenom" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaEntConTel">Téléphone</label>
                                                        <input id="inpstaEntConTel" name="inpstaEntConTel" type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <label for="inpstaEntConMob">Mobile</label>
                                                        <input id="inpstaEntConMob" name="inpstaEntConMob" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <label for="inpstaEntConEmail">Email</label>
                                                        <input id="inpstaEntConEmail" name="inpstaEntConEmail" type="email" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group text-right">
                                                        <input type="submit" class="btn btn-primary" value="Valider" name="frmStageSubmit" />
                                                        <button type="button" class="btn btn-warning" data-dismiss="modal">Annuler</button>
                                                        <input type="hidden" value="<?php echo $_GET['stu']; ?>" name="stuBarcode" id="stuBarcode" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- fin Modal add stage -->

						
           <!-- Modal view stage -->
                        <div class="modal fade" id="modalViewStage" tabindex="-1" role="dialog" aria-labelledby="modalViewStage">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content" style="border-radius: 0;">
                                    <div class="modal-header" style="background: #2e6da4;color: white;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Visualiser les stages</h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                            foreach ($tabInternShip as $internShip) {
                                                ?>

                                                <div class="row">
                                                    <div class="col-xs-12 text-center">
                                                        <h4>Nom : <?php print("<small>". isset($internShip['sta_entNom']) ? $internShip['sta_entNom'] : '-' ."</small>"); ?></h4>
                                                    </div>
                                                </div>

                                                <h4 style="margin-bottom: 5px;">Dates</h4>
                                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <h5>Début: <?php print("<small>". isset($internShip['sta_dateDeb']) ? $internShip['sta_dateDeb'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <h5>Fin: <?php print("<small>". isset($internShip['sta_dateFin']) ? $internShip['sta_dateFin'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>

                                                <h4 style="margin-bottom: 5px;">Entreprise</h4>
                                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                                <h5 style="margin-bottom: 5px;">Adresse</h5>
                                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <h5>Rue : <?php print("<small>". isset($internShip['sta_entRue']) ? $internShip['sta_entRue'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <h5>NPA : <?php print("<small>". isset($internShip['sta_entNpa']) ? $internShip['sta_entNpa'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <div class="form-group">
                                                            <h5>Localité : <?php print("<small>". isset($internShip['sta_entLocalite']) ? $internShip['sta_entLocalite'] : '-' ."</small>"); ?></h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <h5>Canton : <?php print("<small>". isset($internShip['sta_entCanton']) ? $internShip['sta_entCanton'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>

                                                <h5 style="margin-bottom: 5px;">Contact</h5>
                                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <h5>Nom : <?php print("<small>". isset($internShip['sta_entConNom']) ? $internShip['sta_entConNom'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <h5>Prénom : <?php print("<small>". isset($internShip['sta_entConPrenom']) ? $internShip['sta_entConPrenom'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <h5>Téléphone : <?php print("<small>". isset($internShip['sta_entConTel']) ? $internShip['sta_entConTel'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <h5>Mobile : <?php print("<small>". isset($internShip['sta_entConMob']) ? $internShip['sta_entConMob'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h5>E-mail : <?php print("<small>". isset($internShip['sta_entConEmail']) ? $internShip['sta_entConEmail'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>

                                                <hr style="border-top: 2px solid #eee;">

                                                <?php
                                            }
                                        ?>
                                    </div>
                                    <div class="modal-footer" style="background: #fff;">
                                        <button type="button" class="btn btn-warning" data-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- fin Modal view stage -->

                    <!-- detail professionnel !-->

			<div class="tab-pane" id="guardians">
<?php
							if ($ent_id == 0)
							{
?>
				<div class="row">
					<div class="col-xs-12 ">
						<h2 class="page-header">
&nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Entreprise<div class="pull-right"><button type="button" class="btn" data-toggle="modal" data-target="#ajout_maitre">Nouveau maître d'apprentissage</button>&nbsp;&nbsp;<button class="btn" type="button" data-toggle="modal" data-target="#ajout_ent">Nouvelle entreprise</button><br>
							
							<div class="econtainer" id="4Editer" style="visibility:hidden;" >
								<button type="button" id="ent_e" class="btn" onclick="myEdit('tml'); togl('4Ajout'); togl('4Editer'); ">Éditer</button>
							</div>
							<div class="econtainer" id="4Ajout">
								<button type="button" id="ent_a" class="btn btn-primary" onclick="togl('4AddEnt');togl('4AddMai'); ">Assigner</button>
								<!-- <button type="button" id="ent_a" class="btn btn-primary" onclick="getEnt('choixMaix', '' );">TADA</button>
								 <button type="button" id="ent_c" class="btn btn-success" onclick=" ">Créer</button> 
								<button type="button" id="ent_r" class="btn btn-danger" onclick="myReset('tml'); togl('4Editer'); togl('4Ajout');" >Annuler</button>-->
								<button type="button" id="ent_s" class="btn" onclick="mySave('mForm')" style="visibility:hidden;" > Enregistrer</button>
							</div>
					</div></h2>
					
					</div><!-- /.col-xs-12 col-md-12 col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">						
					<div class="col-xs-12 ">
						<!--<div class="pull-left">
						</div>
						 <div class="entainer" id="4entainer" style="visibility:hidden;" >
							<form id="mForm" action="../api/set-ent.php?<?php echo "stuid=".$_GET['stu']."&entID=".$ent_id; ?>" method="post">
								<table id="tml" border="1" class="table table-bordered">										<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
							</table></form>
						</div> -->
						<div  class="ecantainer" id="4AddEnt" style="visibility:hidden;" >
							<form method="POST" action="?entreprise=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $ent_id; ?>">
								<!-- <h4 class="page-header">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> Entreprise <div class="pull-right"> </div> </h4> -->
								<div class="field"> Raison sociale:
								<select class="dropdown form-control" name="choixEnt">
									<option value="<?php $ent_nom; ?>">    <?php if($ent_nom==""){echo "-";}else{echo "$ent_nom";} ?></option>
									<?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
								</select> 
								</div>
								<div >
									<!-- <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ajout_ent">Ajouter une entreprise</button> -->
									<button type="submit" class="btn btn-success">Enregistrer les données</button>
								</div>
							</form>
						</div>
						<div  class="econtainer" id="4AddMai" style="visibility:hidden;" >
                                <h4 class="page-header"> &nbsp;&nbsp;<i class="fa fa-info-circle"></i> Maître d'apprentissage<div class="pull-right"> </div></h4>
							<?php /*<!-- <div class="entainer" id="4mai" style="visibility:hidden;" >
								<form id="eForm" action="../api/set-ent.php?<?php echo "stuid=".$_GET['stu']."&entID=".$ent_id; ?>" method="post">
									<table id="tel" border="1" class="table table-bordered">
											<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
									</table>
								</form>
							</div> --->*/ ?>
							<form method="POST" action="?maitre=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $mai_id; ?>">
								<div class="field">	Assigner un maître d'apprentissage:
									<select class="dropdown form-control" name="choixMai">
										<option value=" <?php if(isset($mai_id))echo "$mai_id" ?>"> <?php if(isset($mai_nom))echo $mai_nom." ".$mai_prenom  ?></option>
										<?php for($a=0;$a<count($maitre);$a++)echo'<option value="'.$maitre[$a]['id_maitredapprentissage'].'">'.$maitre[$a]['mai_nom'].' '.$maitre[$a]['mai_prenom'].'</option>'; ?>
									</select>
								</div>
								<div class="field">
										<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ajout_maitre">Ajouter maître d'apprentissage</button> -->
										<button type="submit" class="btn btn-success">Enregistrer les données</button>
								
										
									</div>
								</form>
								<br><br><br><br><br><br><br>
						</div>
					</div> <!-- /.col-xs-12 -->
				</div> <!-- /.row -->
<?php 								
							}else{
?>							
				<div class="row">
					<div class="col-xs-12">
						<h2 class="page-header">
&nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Entreprise<div class="pull-right"><button type="button" class="btn" data-toggle="modal" data-target="#ajout_maitre">Nouveau maître d'apprentissage</button>&nbsp;&nbsp;<button class="btn" type="button" data-toggle="modal" data-target="#ajout_ent">Nouvelle entreprise</button><br>
							
							<div class="econtainer" id="4Editer" style="visibility:hidden;" >
								<button type="button" id="ent_e" class="btn" onclick="myEdit('tml'); togl('edition'); togl('4Editer'); ">Éditer</button>
							</div>
							<div class="econtainer" id="4Ajout">
								<!--<button type="button" id="ent_a" class="btn btn-primary" onclick="togl('4AddEnt');togl('4AddMai'); ">Assigner</button>
								 <button type="button" id="ent_a" class="btn btn-primary" onclick="getEnt('choixMaix', '' );">TADA</button>
								 <button type="button" id="ent_c" class="btn btn-success" onclick=" ">Créer</button> 
								<button type="button" id="ent_r" class="btn btn-danger" onclick="myReset('tml'); togl('4Editer'); togl('4Ajout');" >Annuler</button>
								<button type="button" id="ent_s" class="btn" onclick="mySave('mForm')" style="visibility:hidden;" > Enregistrer</button>-->
							</div>
					</div></h2>
					</div><!-- /.col-xs-12 col-md-12 col-lg-12 -->
				</div><!-- /.row -->
				<div class="row">						
					<div class="col-xs-12">
						<div class="pull-left">
							<div class="econtainer" id="4editer" >
								<button type="button" class="btn" onclick="myEdit('tml'); togl('4editer'); togl('4edition'); ">Éditer</button>
							</div>
							<div class="econtainer" id="4edition" style="visibility:hidden;">
								<button type="button" id="ent_r" class="btn" onclick="myReset('tml'); togl('4editer'); togl('4edition'); ">Annuler</button>
								<button type="button" id="ent_s" class="btn" onclick="mySave('mForm')">Enregistrer</button>
								<br>
							</div>
						</div>
						<form id="mForm" action="../api/set-ent.php?<?php echo "stuid=".$_GET['stu']."&entID=".$ent_id; ?>" method="post">
							<table id="tml" border="1" class="table table-bordered">
									<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
							</table>
						</form>
						<div  class="ecntainer" id="4AddEnt" > <!-- style="visibility:hidden;" > -->
							<form method="POST" action="?entreprise=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $ent_id; ?>">
								<!-- <h4 class="page-header">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> Entreprise <div class="pull-right"> </div> </h4> -->
								<div class="field">Assigner une raison sociale:
								<select class="dropdown form-control" name="choixEnt">
									<option value="<?php $ent_nom; ?>">    <?php if($ent_nom==""){echo "-";}else{echo "$ent_nom";} ?></option>
									<?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
								</select> 
								</div>
								<div >
									<!-- <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ajout_ent">Ajouter une entreprise</button> -->
									<button type="submit" class="btn btn-success">Enregistrer les données</button>
								</div>
							</form>
						</div>
						<div class="col-xs-12" class="econtainer" id="4AddEnt" >
                                <h4 class="page-header"> &nbsp;&nbsp;<i class="fa fa-info-circle"></i> Maître d'apprentissage<div class="pull-right"> </div></h4>
							<div class="entainer" id="4mai" >
								<form id="eForm" action="../api/set-ent.php?<?php echo "stuid=".$_GET['stu']."&entID=".$ent_id; ?>" method="post">
									<table id="tel" border="1" class="table table-bordered">
											<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
									</table>
								</form>
							</div>
							<form method="POST" action="?maitre=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $mai_id; ?>">
								<div class="field">Assigner un maître d'apprentissage:
									<select class="dropdown form-control" name="choixMai">
										<option value=" <?php if(isset($mai_id))echo "$mai_id" ?>"> <?php if(isset($mai_nom))echo $mai_nom." ".$mai_prenom  ?></option>
										<?php for($a=0;$a<count($maitre);$a++)
													if($maitre[$a]['idx_entreprise'] == $ent_id ) 
														echo'<option value="'.$maitre[$a]['id_maitredapprentissage'].'">'.$maitre[$a]['mai_nom'].' '.$maitre[$a]['mai_prenom'].'</option>'; ?>
									</select>
								</div>
								<div class="field">
										<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ajout_maitre">Ajouter maître d'apprentissage</button> -->
										<button type="submit" class="btn btn-success">Enregistrer les données</button>
									</div>
							</form>
							<br><br><br><br><br><br><br>
						</div>
					</div> <!-- /.col-xs-12 -->
				</div> <!-- /.row -->									
<?php
							}
							
							
							
							
							
							if (1==2)
							{
							?>							
								   <div class="row">
										<div class="col-xs-12 col-md-12 col-lg-12">
											<h4 class="page-header">
												&nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Entreprise <div class="pull-right">
												</div>
											</h4>
										</div><!-- /.col -->
									</div>
									<div class="row">						
										<div class="col-xs-12">
										
									<div class="pull-left">
										<div class="econtainer" id="4editer" >
											<button type="button" class="btn" onclick="myEdit('tml'); togl('4editer'); togl('4edition'); ">Éditer</button>
										</div>
										<div class="econtainer" id="4edition" style="visibility:hidden;"  >
										<button type="button" id="ent_r" class="btn" onclick="myReset('tml'); togl('4editer'); togl('4edition'); ">Annuler</button>
										<button type="button" id="ent_s" class="btn" onclick="mySave('mForm')">Enregistrer</button>
										</div>
									</div>
									
											<form id="mForm" action="../api/set-ent.php?<?php echo "stuid=".$_GET['stu']."&entID=".$ent_id; ?>" method="post">
												<table id="tml" border="1" class="table table-bordered">
													<tr><th> - </th><th> - </th><th> - </th><th> - </th></tr>
												</table>
											</form>
										</div> <!---COL XS 12--->
									</div> <!-- / div row -->									
									
							<?php
							} else if (2==1) {
							?>
							
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <h4 class="page-header">
                                    &nbsp;&nbsp;<i class="fa fa-info-circle"></i> Entreprise <div class="pull-right">
                                    </div>
                                </h4>
                            </div><!-- /.col -->
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <div class="col-lg-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss">&nbsp;&nbsp;&nbsp;&nbsp;Nom :</div>
                                <div class="col-lg-9 col-sm-9 col-xs-6 edusec-profile-text"><?php echo $ent_nom ; ?></div>
                            </div>
                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Adresse :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ent_rue." ". $ent_localite." ". $ent_npa ; ?></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Mail :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ent_mail; ?></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Tel 1 / Tel 2 :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ent_tel1 ."/".$ent_tel2 ; ?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-12 col-lg-12">
                                    <h4 class="page-header">
                                        &nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Maître d'apprentissage	<div class="pull-right">
                                        </div>
                                    </h4>
                                </div><!-- /.col -->
                            </div>
                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Nom & prénom :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_nom ." ".$mai_prenom ; ?></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Numéro Mobile :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_mobile  ; ?></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Numéro téléphone :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_tel1  ; ?></div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Numéro téléphone 2 :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_tel2  ; ?></div>
                                </div>
                            </div>
                        </div>

					<div >
						<form method="POST" action="?entreprise=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $ent_id; ?>">
								<h4 class="page-header">
                                    &nbsp;&nbsp;<i class="fa fa-info-circle"></i> Entreprise <div class="pull-right">
                                    </div>
                                </h4>
							<div class="field">      
								Raison sociale:
								<select class="dropdown form-control" name="choixEnt">
									<option value="<?php $ent_nom; ?>">    <?php if($ent_nom==""){echo "-";}else{echo "$ent_nom";} ?></option>
									<?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
								</select> 
							</div>
							<div >
								<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ajout_ent">Ajouter une entreprise</button>
								<button type="submit" class="btn btn-success">Enregistrer les données</button>
							</div>
						</form>
                                <h4 class="page-header">
                                    &nbsp;&nbsp;<i class="fa fa-info-circle"></i> Maître d'apprentissage<div class="pull-right">
                                    </div>
                                </h4>
						<form method="POST" action="?maitre=oui&stu=<?php echo $_GET['stu']; ?>&nom=<?php echo $mai_id; ?>">
							<div class="field">
								Nom et prénom:
								<select class="dropdown form-control" name="choixMai">
									<option value=" <?php if(isset($mai_id))echo "$mai_id" ?>"> <?php if(isset($mai_nom))echo $mai_nom." ".$mai_prenom  ?></option>
									<?php for($a=0;$a<count($maitre);$a++)echo'<option value="'.$maitre[$a]['id_maitredapprentissage'].'">'.$maitre[$a]['mai_nom'].' '.$maitre[$a]['mai_prenom'].'</option>'; ?>
								</select>
							</div>
							<div class="field">
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ajout_maitre">Ajouter maître d'apprentissage</button>
									<button type="submit" class="btn btn-success">Enregistrer les données</button>
								</div>
						</form>
                    </div>		
							<?php
							}
							?>					
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
                        <form method="POST" action="?ajout=maitre&stu=<?php echo $_GET['stu']; ?>">
                    <table class="table table-striped">
                           <tr>
                               <td style="font-weight: bold">Nom, prenom:</td>
                               <td><input type="text" name="mai_nom">&nbsp;&nbsp;<input type="text" name="mai_prenom"></td>
                           </tr> 
                           <tr>
                               <td style="font-weight: bold">Tel1:</td>
                               <td><input type="text" name="mai_tel1" value="-"></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Tel2:</td>
                               <td><input type="text" name="mai_tel2" value="-" ></td>
                           </tr>
                           <tr>
                           <td style="font-weight: bold">Mobile:</td>
                               <td><input type="text" name="mai_mobile" value="-" ></td>
                           </tr>
                           <tr>
                               <td style="font-weight: bold">Entreprise</td>
                               <td><select class="dropdown form-control" name="choixEntreprise">
                                    <option></option>
                                    <?php for($a=0;$a<count($Allentreprise);$a++)echo'<option value="'.$Allentreprise[$a]['id_entreprise'].'">'.$Allentreprise[$a]['ent_nom'].'</option>'; ?> 
                               </select></td>
                           </tr>
                           <tr>
                               <td><input type="submit" class="btn btn-primary" value="Ajouter" float="right"></td>
                           </tr>
                    </table>
                  </div>
                  <div class="modal-footer">
						<button type="submit" class="btn btn-success">Enregistrer les données</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                  </div>
                        </form>
                </div>
              </div>
           </div>


        <div class="modal fade" id="ajout_rep" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Ajouter un représentant légal</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <form method="POST" action="?ajout=rep&stu=<?php echo $_GET['stu']; ?>">
                                <tr>
                                    <td style="font-weight: bold">Nom:</td>
                                    <td><input type="text" name="rep_nom"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Prénom:</td>
                                    <td><input type="text" name="rep_prenom"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Politesse:</td>
                                    <td> <select class="dropdown form-control" name="rep_politesse">
                                            <option value="Madame">Madame</option>
                                            <option value="Monsieur">Monsieur</option>
                                        </select> </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Rue:</td>
                                    <td><input type="text" name="rep_rue">&nbsp</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">NPA:</td>
                                    <td><input type="text" name="rep_npa"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Localité:</td>
                                    <td><input type="text" name="rep_local"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel1</td>
                                    <td><input type="text" name="rep_tel1"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Tel2</td>
                                    <td><input type="text" name="rep_tel2"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold">Mobile:</td>
                                    <td><input type="text" name="rep_mobile"></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" class="btn btn-primary" value="Ajouter" float="right"></td>
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
                               <td><input type="submit" class="btn btn-primary" value="Ajouter" float="right"></td>
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



			
			
			
			
			
        <div class="modal fade" id="ruptureEleve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Déclarer une rupture</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <form method="POST" action="?<?php echo "stu=".$_GET['stu']; ?>">
                                <tr><td style="font-weight: bold">Date de rupture</td><td><input name="dateRupture" type="text" placeholder="dd/mm/yyyy" class="date-picker" ></td></tr>
                                <tr><td style="font-weight: bold">Suit les cours:</td><td> <select class="dropdown form-control" name="ChoixSuivreCours"><option value="Non">Non</option><option value="Oui'">Oui</option></select></td>
                                </tr><tr><td><input type="submit" class="btn btn-primary" value="Enregistrer" float="right"></td></tr>
                            </form>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="DispenseEleve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Dispense</h4>
                    </div>
                    <div class="modal-body">
                            <form method="POST" action="?<?php echo "stu=".$_GET['stu']; ?>">
                                <br>
								<div class="field">
								<label class="main">Cours:</label>
                                          <td>                                            <select class="dropdown form-control" name="choixCours">                                            
                                            <?php for($a=0;$a<count($Cours);$a++)echo'<option value="'.$Cours[$a]['cou_matcode'].'">'.$Cours[$a]['cou_matlibelle'].'</option>'; ?>
                                        </select> </td>
										</div>
                                <br>
								<!--- <button type="button" class="btn btn-secondary" onclick="UpdateStudentNotices('204158', CollectNotices())" style="text-align: right;">Mettre à jour</button> --->
                                <div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cours Dispense</th>
                                                <th>Action</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($Dispense as $entry): ?>
                                            <tr>
                                               <td> <?php echo $entry['cou_matlibelle']?></td>
											   <td>	<a href='?supdisp=oui&cou=<?php echo $entry['id_sdh']?>&stu=<?php echo $_GET['stu']; ?>' title="Supprimer" class="glyphicon glyphicon-remove" data-confirm="Supprimer cet outil ?" data-method="post">
													</a>
											   </td>                                           
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button type="submit" class="btn btn-success" name='dispense' >Ajouter</button>                               
                                </div>                          							
								</div>
								<div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>


                    <!-- Adresse pane !-->
                    <div class="tab-pane" id="address">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="page-header">
                                    <i class="fa fa-info-circle"></i> Informations sur l’adresse 	<div class="pull-right">
                                        <a id="update-data" class="btn btn-primary btn-sm" href="student_edt.php?stu=<?php echo $_GET['stu']; ?>"><i class="fa fa-pencil-square-o"></i>Editer</a>		</div>
                                </h2>
                            </div><!-- /.col -->
                        </div>
                        <!---Start Current Address Block--->



                        <!---Start Permenant Address Block--->
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss">&nbsp;&nbsp;&nbsp;&nbsp;Rue :</div>
                                <div class="col-md-9 col-sm-9 col-xs-6 edusec-profile-text"><?php echo $ele_rue ; ?></div>
                            </div>

                            <div class="col-md-12  col-xs-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Localité :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ele_localite ; ?></div>
                                </div>
                            </div>

                            <div class="col-md-12  col-xs-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">NPA :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ele_npa ; ?></div>
                                </div>
                            </div>
							
                            <div class="col-md-12  col-xs-12">
							
                            </div>
                        </div>
                    </div>


                    <!-- mis a la porte -->
                    <!-- mis a la porte PANE !-->
                    <div class="tab-pane" id="mp">
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
							
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
							
                            </div>
                        </div>

                        <div id="door" class="row">
                            <form>
                                <br>
                                <button type="button" onclick="UpdateStudentDoors(CollectDoors())" class="btn btn-secondary" style="text-align: right;">Mettre à jour</button>
                                <div class="col-sm-10">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Cours</th>
                                            <th>Commentaire</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($tabStudentDoor as $entry) : ?>
                                            <tr id="door-<?= $entry['id_porte'] ?>">
                                                <th><?= date("d/m/Y", strtotime($entry['por_date'])); ?></th>
                                                <td><?= $entry['cou_matlibelle'] ?></td>
                                                <td><input type="text" name="comment" value="<?= @$entry['por_commentaire'] ?>"></td>
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
                    </div>




                    <!-- mis a la porte -->
                    <!-- mis a la porte PANE !-->
                    <div class="tab-pane" id="remarques">
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                            </div>
                        </div>
                        <div id="notice" class="row">
                            <form>
                                <br>
                                <button type="button" class="btn btn-secondary" onclick="UpdateStudentNotices('<?= $id_codebarre ?>', CollectNotices())" style="text-align: right;">Enregistrer</button>
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
					</div>

					
					


                    <div class="tab-pane" id="stages" name="stages">
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <div class="modal-content" style="border-radius: 0;">
                                    <div class="modal-header" style="background: #2e6da4;color: white;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Visualiser les stages</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="btn btn-white">
                                            <a data-toggle="modal" data-target="#modalAddStage">Stage <span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                                        </div>
                                        <?php
                                        foreach ($tabInternShip as $internShip) {
                                            ?>
                                            <div class="row">
                                                <div class="col-xs-12 text-center">
                                                    <h4>Nom : <?php print("<small>". isset($internShip['sta_entNom']) ? $internShip['sta_entNom'] : '-' ."</small>"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="student_dtl.php?stu=<?php echo $_GET['stu']; ?>&delete=oui&stage=<?php echo $internShip['id_stage'];?>" title="Supprimer" data-confirm="Supprimer cet outil ?" data-method="post">Supprimer<span class="glyphicon glyphicon-remove"></span></a></h4>
                                                </div>
                                            </div>
                                            <h4 style="margin-bottom: 5px;">Dates</h4>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <h5>Début: <?php print("<small>". isset($internShip['sta_dateDeb']) ? $internShip['sta_dateDeb'] : '-' ."</small>"); ?></h5>
                                                </div>
                                                <div class="col-xs-6">
                                                    <h5>Fin: <?php print("<small>". isset($internShip['sta_dateFin']) ? $internShip['sta_dateFin'] : '-' ."</small>"); ?></h5>
                                                </div>
                                            </div>
                                            <h4 style="margin-bottom: 5px;">Entreprise</h4>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <h5 style="margin-bottom: 5px;">Adresse</h5>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <h5>Rue : <?php print("<small>". isset($internShip['sta_entRue']) ? $internShip['sta_entRue'] : '-' ."</small>"); ?></h5>
                                                </div>
                                                <div class="col-xs-6">
                                                    <h5>NPA : <?php print("<small>". isset($internShip['sta_entNpa']) ? $internShip['sta_entNpa'] : '-' ."</small>"); ?></h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <h5>Localité : <?php print("<small>". isset($internShip['sta_entLocalite']) ? $internShip['sta_entLocalite'] : '-' ."</small>"); ?></h5>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <h5>Canton : <?php print("<small>". isset($internShip['sta_entCanton']) ? $internShip['sta_entCanton'] : '-' ."</small>"); ?></h5>
                                                </div>
                                            </div>
                                            <h5 style="margin-bottom: 5px;">Contact</h5>
                                            <hr style="margin-top: 0;margin-bottom: 15px;">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <h5>Nom : <?php print("<small>". isset($internShip['sta_entConNom']) ? $internShip['sta_entConNom'] : '-' ."</small>"); ?></h5>
                                                </div>
                                                <div class="col-xs-6">
                                                    <h5>Prénom : <?php print("<small>". isset($internShip['sta_entConPrenom']) ? $internShip['sta_entConPrenom'] : '-' ."</small>"); ?></h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <h5>Téléphone : <?php print("<small>". isset($internShip['sta_entConTel']) ? $internShip['sta_entConTel'] : '-' ."</small>"); ?></h5>
                                                </div>
                                                <div class="col-xs-6">
                                                    <h5>Mobile : <?php print("<small>". isset($internShip['sta_entConMob']) ? $internShip['sta_entConMob'] : '-' ."</small>"); ?></h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <h5>E-mail : <?php print("<small>". isset($internShip['sta_entConEmail']) ? $internShip['sta_entConEmail'] : '-' ."</small>"); ?></h5>
                                                </div>
                                            </div>
                                            <hr style="border-top: 2px solid #eee;">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">

                            </div>
                        </div>
                    </div>






                    <div class="tab-pane" id="avant">

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <h4 class="page-header">
                                    &nbsp;&nbsp;<i class="fa fa-info-circle"></i> Entreprise <div class="pull-right">
                                    </div>
                                </h4>
                            </div><!-- /.col -->
                        </div>






                        <div class="row">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss">Nom :</div>
                                    <div class="col-lg-9 col-sm-9 col-xs-6 edusec-profile-text"><?php echo $ent_nom ; ?></div>
                                </div>
                            </div>

                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Adresse :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ent_rue." ". $ent_localite." ". $ent_npa ; ?></div>
                                </div>
                            </div>

                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Tel 1 / Tel 2 :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $ent_tel1 ."/".$ent_tel2 ; ?></div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-xs-12 col-md-12 col-lg-12">
                                    <h4 class="page-header">
                                        &nbsp;&nbsp;<i class="fa fa-info-circle"> </i> Maître d'apprentissage	<div class="pull-right">
                                        </div>
                                    </h4>
                                </div><!-- /.col -->
                            </div>

                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Nom et prénom :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_nom ." ".$mai_prenom ; ?></div>
                                </div>
                            </div>

                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Mobile :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_mobile  ; ?></div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Téléphone :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_tel1  ; ?></div>
                                </div>
                            </div>

                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="col-lg-6 col-sm-6 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-xs-6 edusec-profile-label edusecArLangCss">Téléphone 2 :</div>
                                    <div class="col-lg-6 col-xs-6 edusec-profile-text"><?php echo $mai_tel2  ; ?></div>
                                </div>
                            </div>








                        </div>

                    </div>
                    <div class="tab-pane" id="ici">

                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="page-header">
                                    <i class="fa fa-info-circle"></i> Informations sur l’adresse 	<div class="pull-right">
                                        <a id="update-data" class="btn btn-primary btn-sm" href="student_edt.php?stu=<?php echo $_GET['stu']; ?>"><i class="fa fa-pencil-square-o"></i>Editer</a>		</div>
                                </h2>
                            </div><!-- /.col -->
                        </div>

                        <!---Start Current Address Block--->



                        <!---Start Permenant Address Block--->




                        <div class="row">
                            <div class="col-md-12  col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss">&nbsp;Rue :</div>
                                    <div class="col-md-9 col-sm-9 col-xs-6 edusec-profile-text"><?php echo $ele_rue ; ?></div>
                                </div>
                            </div>

                            <div class="col-md-12  col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss">Localité :</div>
                                    <div class="col-md-9 col-sm-9 col-xs-6 edusec-profile-text"><?php echo $ele_localite ; ?></div>
                                </div>

                            </div>

                            <div class="col-md-12  col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-3 col-sm-3 col-xs-6 edusec-profile-label edusecArLangCss">NPA :</div>
                                    <div class="col-md-9 col-sm-9 col-xs-6 edusec-profile-text"><?php echo $ele_npa ; ?></div>
                                </div>

                            </div>





                            <div class="col-md-12  col-xs-12">


                            </div>
                        </div>



                    </div>
                    <div class="tab-pane" id="hna">

                        <!---Start Permenant Address Block--->
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">
                                    <i class="fa fa-files-o"></i> Documents en ligne	</h4>
                            </div><!-- /.col -->
                        </div>

                        <div class="table-responsive disp-doc">

                            <table class="table table-bordered">
                                <tbody><tr>
                                    <th class="text-center"><label for="studocs-stu_docs_category_id">Catégorie</label></th>
                                    <th class="text-center"><label for="studocs-stu_docs_details">Détails du document </label></th>
                                    <th class="text-center"><label for="studocs-stu_docs_status">Statut</label></th>
                                    <th class="text-center " style="width: 34%;">Action</th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="4">Pas de document téléchargé...</th>
                                </tr>
                                </tbody></table></div>
                        <script>
                            /*$(document).ready(function(){
                                $('input[type=file]').bootstrapFileInput();
                            });*/
                        </script>

                        <div class="col-xs-12 col-lg-12 no-padding" style="display:block">
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">
                                        <i class="fa fa-upload"></i> Mettre en ligne documents restant	     </h4>
                                </div><!-- /.col -->
                            </div>

                            <div class="box-default box view-item col-xs-12 col-lg-12">
                                <div class="stu-docs-form">
                                    <form id="stu-docs-form" action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="_csrf" value="X0YwbEo5b3AlJwklKww6SRUsZj4OUCESZy9bWjlMPR8HAgciAAgLOw==">	   		<div class="col-xs-12 col-sm-12 col-lg-12" style="background-color:#f4f4f4; border-bottom:2px solid #ddd;margin-bottom:2%;padding:1%">
                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_category_id_temp-1">
                                                    <label class="control-label" for="studocs-stu_docs_category_id_temp-1">Catégorie</label><input type="text" id="studocs-stu_docs_category_id_temp-1" class="form-control" name="StuDocs[stu_docs_category_id_temp][1]" value="S.S.C. Marksheet" maxlength="100" readonly=""><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_category_id-1 required">
                                                    <input type="hidden" id="studocs-stu_docs_category_id-1" class="form-control" name="StuDocs[stu_docs_category_id][1]" value="1">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_details-1">
                                                    <label class="control-label" for="studocs-stu_docs_details-1">Détails du document </label><input type="text" id="studocs-stu_docs_details-1" class="form-control" name="StuDocs[stu_docs_details][1]" maxlength="100"><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_stu_master_id required">
                                                    <input type="hidden" id="studocs-stu_docs_stu_master_id" class="form-control" name="StuDocs[stu_docs_stu_master_id]" value="15">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4 no-padding">
                                                <div class="col-lg-10 col-sm-6 col-md-10">
                                                    <div class="form-group field-studocs-stu_docs_path-1">
                                                        <label class="control-label" for="studocs-stu_docs_path-1">Documents</label><input type="hidden" name="StuDocs[stu_docs_path][1]" value=""><a class="file-input-wrapper btn btn-primary col-xs-12 col-lg-12 "><span>Chercher le document</span><input type="file" id="studocs-stu_docs_path-1" name="StuDocs[stu_docs_path][1]" title="Chercher le document" data-filename-placement="inside"></a><div class="help-block"></div>
                                                    </div>			</div>
                                            </div>

                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-lg-12" style="background-color:#f4f4f4; border-bottom:2px solid #ddd;margin-bottom:2%;padding:1%">
                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_category_id_temp-2">
                                                    <label class="control-label" for="studocs-stu_docs_category_id_temp-2">Catégorie</label><input type="text" id="studocs-stu_docs_category_id_temp-2" class="form-control" name="StuDocs[stu_docs_category_id_temp][2]" value="H.S.C. Marksheet" maxlength="100" readonly=""><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_category_id-2 required">
                                                    <input type="hidden" id="studocs-stu_docs_category_id-2" class="form-control" name="StuDocs[stu_docs_category_id][2]" value="2">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_details-2">
                                                    <label class="control-label" for="studocs-stu_docs_details-2">Détails du document </label><input type="text" id="studocs-stu_docs_details-2" class="form-control" name="StuDocs[stu_docs_details][2]" maxlength="100"><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_stu_master_id required">
                                                    <input type="hidden" id="studocs-stu_docs_stu_master_id" class="form-control" name="StuDocs[stu_docs_stu_master_id]" value="15">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4 no-padding">
                                                <div class="col-lg-10 col-sm-6 col-md-10">
                                                    <div class="form-group field-studocs-stu_docs_path-2">
                                                        <label class="control-label" for="studocs-stu_docs_path-2">Documents</label><input type="hidden" name="StuDocs[stu_docs_path][2]" value=""><a class="file-input-wrapper btn btn-primary col-xs-12 col-lg-12 "><span>Chercher le document</span><input type="file" id="studocs-stu_docs_path-2" name="StuDocs[stu_docs_path][2]" title="Chercher le document" data-filename-placement="inside"></a><div class="help-block"></div>
                                                    </div>			</div>
                                            </div>

                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-lg-12" style="background-color:#f4f4f4; border-bottom:2px solid #ddd;margin-bottom:2%;padding:1%">
                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_category_id_temp-3">
                                                    <label class="control-label" for="studocs-stu_docs_category_id_temp-3">Catégorie</label><input type="text" id="studocs-stu_docs_category_id_temp-3" class="form-control" name="StuDocs[stu_docs_category_id_temp][3]" value="Leaving Certificate" maxlength="100" readonly=""><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_category_id-3 required">
                                                    <input type="hidden" id="studocs-stu_docs_category_id-3" class="form-control" name="StuDocs[stu_docs_category_id][3]" value="3">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_details-3">
                                                    <label class="control-label" for="studocs-stu_docs_details-3">Détails du document </label><input type="text" id="studocs-stu_docs_details-3" class="form-control" name="StuDocs[stu_docs_details][3]" maxlength="100"><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_stu_master_id required">
                                                    <input type="hidden" id="studocs-stu_docs_stu_master_id" class="form-control" name="StuDocs[stu_docs_stu_master_id]" value="15">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4 no-padding">
                                                <div class="col-lg-10 col-sm-6 col-md-10">
                                                    <div class="form-group field-studocs-stu_docs_path-3">
                                                        <label class="control-label" for="studocs-stu_docs_path-3">Documents</label><input type="hidden" name="StuDocs[stu_docs_path][3]" value=""><a class="file-input-wrapper btn btn-primary col-xs-12 col-lg-12 "><span>Chercher le document</span><input type="file" id="studocs-stu_docs_path-3" name="StuDocs[stu_docs_path][3]" title="Chercher le document" data-filename-placement="inside"></a><div class="help-block"></div>
                                                    </div>			</div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-lg-12" style="background-color:#f4f4f4; border-bottom:2px solid #ddd;margin-bottom:2%;padding:1%">
                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_category_id_temp-4">
                                                    <label class="control-label" for="studocs-stu_docs_category_id_temp-4">Catégorie</label><input type="text" id="studocs-stu_docs_category_id_temp-4" class="form-control" name="StuDocs[stu_docs_category_id_temp][4]" value="Bonafied Certificate" maxlength="100" readonly=""><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_category_id-4 required">
                                                    <input type="hidden" id="studocs-stu_docs_category_id-4" class="form-control" name="StuDocs[stu_docs_category_id][4]" value="4">
                                                </div>		    </div>

                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_details-4">
                                                    <label class="control-label" for="studocs-stu_docs_details-4">Détails du document </label><input type="text" id="studocs-stu_docs_details-4" class="form-control" name="StuDocs[stu_docs_details][4]" maxlength="100"><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_stu_master_id required">
                                                    <input type="hidden" id="studocs-stu_docs_stu_master_id" class="form-control" name="StuDocs[stu_docs_stu_master_id]" value="15">
                                            </div>		    </div>
                                            <div class="col-xs-12 col-sm-4 col-lg-4 no-padding">
                                                <div class="col-lg-10 col-sm-6 col-md-10">
                                                    <div class="form-group field-studocs-stu_docs_path-4">
                                                        <label class="control-label" for="studocs-stu_docs_path-4">Documents</label><input type="hidden" name="StuDocs[stu_docs_path][4]" value=""><a class="file-input-wrapper btn btn-primary col-xs-12 col-lg-12 "><span>Chercher le document</span><input type="file" id="studocs-stu_docs_path-4" name="StuDocs[stu_docs_path][4]" title="Chercher le document" data-filename-placement="inside"></a><div class="help-block"></div>
                                                    </div>			</div>
                                            </div>
                                        </div>
										
                                        <div class="col-xs-12 col-sm-12 col-lg-12" style="background-color:#f4f4f4; border-bottom:2px solid #ddd;margin-bottom:2%;padding:1%">
                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_category_id_temp-6">
                                                    <label class="control-label" for="studocs-stu_docs_category_id_temp-6">Catégorie</label><input type="text" id="studocs-stu_docs_category_id_temp-6" class="form-control" name="StuDocs[stu_docs_category_id_temp][6]" value="Migration Certificate" maxlength="100" readonly=""><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_category_id-6 required">
                                                    <input type="hidden" id="studocs-stu_docs_category_id-6" class="form-control" name="StuDocs[stu_docs_category_id][6]" value="6">
                                                </div>		    </div>
                                            <div class="col-xs-12 col-sm-4 col-lg-4">
                                                <div class="form-group field-studocs-stu_docs_details-6">
                                                    <label class="control-label" for="studocs-stu_docs_details-6">Détails du document </label><input type="text" id="studocs-stu_docs_details-6" class="form-control" name="StuDocs[stu_docs_details][6]" maxlength="100"><div class="help-block"></div>
                                                </div>			<div class="form-group field-studocs-stu_docs_stu_master_id required">
                                                    <input type="hidden" id="studocs-stu_docs_stu_master_id" class="form-control" name="StuDocs[stu_docs_stu_master_id]" value="15">
                                                </div>		    </div>												
                                            <div class="col-xs-12 col-sm-4 col-lg-4 no-padding">
                                                <div class="col-lg-10 col-sm-6 col-md-10">
                                                    <div class="form-group field-studocs-stu_docs_path-6">
                                                        <label class="control-label" for="studocs-stu_docs_path-6">Documents</label><input type="hidden" name="StuDocs[stu_docs_path][6]" value=""><a class="file-input-wrapper btn btn-primary col-xs-12 col-lg-12 "><span>Chercher le document</span><input type="file" id="studocs-stu_docs_path-6" name="StuDocs[stu_docs_path][6]" title="Chercher le document" data-filename-placement="inside"></a><div class="help-block"></div>
                                                    </div>			</div>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-3 edusecArLangCss" style="display:block;margin-top: 10px;">
                                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-upload"></i> Mettre en ligne</button>    </div>
                                    </form>    </div>
                            </div>
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


                <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
                <!-- Code JS Affichage des tableaux Absences - Arrivées tardives - Mises à la porte -->
                <script>
                    /*$('#myModal').on('shown.bs.modal', function () {
                        $('#myInput').focus()
                    })*/

                </script>
                <script type="text/javascript">
                    /*$(document).ready(function(){
                        $("#myForm :input").prop("disabled", true);
                    });*/
					
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

                <style>
                    .student-container {
                        position: relative;
                        margin-bottom: 30px;
                        border-radius: 3px;
                        overflow: hidden;
                        box-shadow: 0 0 12px 0 rgba(80, 80, 80, 0.6);
                        transition: box-shadow 0.3s;
                    }
                    .student-container > img {
                        width: 100%;
                    }
                    .student-container > span {
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        padding: 8px 23%;
                        font-size: 18px;
                        background-color: rgba(25, 25, 25, 0.8);
                        color: white;
                        text-align: center;
                        transition: all 0.6s;
                    }
                    .student-container:hover {
                        box-shadow: 0 0 18px 2px rgba(30, 30, 30, 0.5);
                    }
                    .student-container:hover > span {
                        bottom: 12px;
                        transform: translateY(100%);
                        -ms-transform: translateY(100%);
                    }
                    .student-container > span:hover {
                        bottom: 0;
                        padding-left: 2%;
                        padding-right: 2%;
                        font-size: 33px;
                        transform: translateY(0);
                        -ms-transform: translateY(0);
                    }
                    /* Pour une raison obscure, les différentes tailles de grilles bootstrap ne fonctionnent pas
                     stephanie.ponta@he-arc.ch */
                    @media screen and (max-width: 1300px) {
                        .bs-col-override {
                            width: 33.3%;
                        }
                    }
                    @media screen and (max-width: 980px) {
                        .bs-col-override {
                            float: left;
                            width: 50%;
                        }
                    }
                    @media screen and (max-width: 420px) {
                        .bs-col-override {
                            width: 100%;
                        }
                    }
                </style>
                <!-- /#page-wrapper -->
            </div>
            <!-- jQuery -->



            <!-- Modal add stage -->
            <div class="modal fade" id="modalAddStage" tabindex="-1" role="dialog" aria-labelledby="modalAddStage">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="border-radius: 0;">
                        <div class="modal-header" style="background: #2e6da4;color: white;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Ajouter un stage</h4>
                        </div>
                        <!-- sends the submitted form data to the page itself, instead of jumping to a different page. This way, the user will get error messages on the same page as the form. -->
                        <div class="modal-body">
                            <form name="frmAddInternShip" id="frmAddInternShip" action="./chkForm/chkInternShip.php" method="POST">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaDateDeb">Date début</label>
                                            <input id="inpstaDateDeb" name="inpstaDateDeb" type="text" class="form-control date-picker">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaDateFin">Date fin</label>
                                            <input id="inpstaDateFin" name="inpstaDateFin" type="text" class="form-control date-picker">
                                        </div>
                                    </div>
                                </div>

                                <h4 style="margin-bottom: 5px;">Entreprise</h4>
                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="inpstaEntNom">Nom</label>
                                            <input id="inpstaEntNom" name="inpstaEntNom" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <h4 style="margin-bottom: 5px;">Adresse</h4>
                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaEntRue">Rue</label>
                                            <input id="inpstaEntRue" name="inpstaEntRue" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaEntNpa">NPA</label>
                                            <input id="inpstaEntNpa" name="inpstaEntNpa" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-10">
                                        <div class="form-group">
                                            <label for="inpstaEntLocalite">Localité</label>
                                            <input id="inpstaEntLocalite" name="inpstaEntLocalite" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-2">
                                        <div class="form-group">
                                            <label for="inpstaEntCanton">Canton</label>
                                            <input id="inpstaEntCanton" name="inpstaEntCanton" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <h4 style="margin-bottom: 5px;">Contact</h4>
                                <hr style="margin-top: 0;margin-bottom: 15px;">

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaEntConNom">Nom</label>
                                            <input id="inpstaEntConNom" name="inpstaEntConNom" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaEntConPrenom">Prénom</label>
                                            <input id="inpstaEntConPrenom" name="inpstaEntConPrenom" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaEntConTel">Téléphone</label>
                                            <input id="inpstaEntConTel" name="inpstaEntConTel" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="inpstaEntConMob">Mobile</label>
                                            <input id="inpstaEntConMob" name="inpstaEntConMob" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="inpstaEntConEmail">Email</label>
                                            <input id="inpstaEntConEmail" name="inpstaEntConEmail" type="email" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group text-right">
                                            <input type="submit" class="btn btn-primary" value="Valider" name="frmStageSubmit" />
                                            <button type="button" class="btn btn-warning" data-dismiss="modal">Annuler</button>
                                            <input type="hidden" value="<?php echo $_GET['stu']; ?>" name="stuBarcode" id="stuBarcode" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin Modal add stage -->
            <!-- Modal view stage -->






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
            <script type="text/javascript">
function togl(anId)
{
	node = document.getElementById(anId);
	if (node.style.visibility=="hidden")
	{		// Contenu caché, le montrer
		node.style.visibility = "visible";
		node.style.height = "auto";			// Optionnel rétablir la hauteur
	}
	else
	{
		// Contenu visible, le cacher
		node.style.visibility = "hidden";
		node.style.height = "0";			// Optionnel libérer l'espace
	}
}

                var el_stu = "<?php echo $_GET['stu']; ?>"; <!-- "002833"; -->
				var el_maitredapprentissage = "<?php echo "$mai_id" ; ?> ";
				var el_representantlegal = "<?php echo "$idx_replegal"; ?>";
				var el_entreprise = "<?php echo "$ent_id" ; ?>";
				/***  Détails personnels  En rupture ***/
                var daraw = [	
					{ "d1": "Politesse", 	"d2": "ele_politesse", 		"d3": "Canton", 			"d4": "ele_canton" },
                    { "d1": "Prénom", 	 	"d2": "ele_prenom", 		"d3": "PAYS", 				"d4": "-" },
                    { "d1": "Nom", 		 	"d2": "ele_nom", 			"d3": "Téléphone", 			"d4": "-" },
                    { "d1": "Elève-Adr.", 	"d2": "-", 					"d3": "Mobile", 			"d4": "ele_numeromobile" },
                    { "d1": "Rue", 			"d2": "ele_rue", 			"d3": "Email", 				"d4": "ele_mail" },
                    { "d1": "NPA", 			"d2": "ele_npa", 			"d3": "Statut Juridique", 	"d4": "ele_majeur" },
                    { "d1": "Localité",		"d2": "ele_localite", 		"d3": "Date de Naissance", 	"d4": "ele_datedenaissance" }
                ];
				/*** BACKUP Informations Complémentaires  PUKCAB ***/
				var darbk = [	
					{ "d1": "N°AVS",	 		"d2": "-",	 				"d3": "Genre",	 		"d4": "ele_politesse" },
                    { "d1": "Id Élève",	 		"d2": "id_eleve", 			"d3": "Code Barre",		"d4": "id_codebarre" },
                    { "d1": "Désavantage", 		"d2": "ele_desavantage", 	"d3": "Formation", 		"d4": "-" },
                    { "d1": "Dispensé ECG",		"d2": "ele_dispenseecg", 	"d3": "Orientation",	"d4": "-" },
                    { "d1": "Dispensé BT",		"d2": "ele_dispensebt", 	"d3": "Dérogation AT",	"d4":"ele_derogation" },
                    { "d1": "Dispensé SPORT",	"d2": "ele_dispensesport", 	"d3": "Dérogation DA", 	"d4": "-" },
                    { "d1": "Élève Statut",		"d2": "ele_statut", 		"d3": "Commentaires",	"d4": "-" }
                ];
				/*** Informations Complémentaires ***/
                var darbw = [	
					{ "d1": "N°AVS",	 		"d2": "-",	 				"d3": "Genre",	 		"d4": "ele_politesse" },
                    { "d1": "Id Élève",	 		"d2": "id_eleve", 			"d3": "Code Barre",		"d4": "id_codebarre" },
                    { "d1": "Désavantage", 		"d2": "ele_desavantage", 	"d3": "Formation", 		"d4": "-" },
                    { "d1": "Dérogation DA", 	"d2": "-", 					"d3": "Commentaires",	"d4": "-"  },
                    { "d1": "Dérogation AT",	"d2": "ele_derogation", 		"d3": "Élève Statut",	"d4": "ele_statut" }
                ];
				/***  Représentant Légal ***/
                var darcw = [	
					{ "d1": "Politesse", 	"d2": "rep_politesse", 	"d3": "Canton", 			"d4": "-" },
                    { "d1": "Prénom", 	 	"d2": "rep_prenom", 	"d3": "PAYS", 				"d4": "-" },
                    { "d1": "Nom", 		 	"d2": "rep_nom", 		"d3": "Téléphone 1", 		"d4": "rep_tel1" },
                    { "d1": "Elève-Adr.", 	"d2": "-", 				"d3": "Téléphone 2", 		"d4": "rep_tel2" },
                    { "d1": "Rue", 			"d2": "rep_Rue", 		"d3": "Email", 				"d4": "-" },
                    { "d1": "NPA", 			"d2": "rep_npa", 		"d3": "Mobile", 			"d4": "rep_numeromobile" },
                    { "d1": "Localité",		"d2": "rep_localite", 	"d3": "- - - -", 			"d4": "-" }
                ];
				/*** Entreprise ***/
				var darmw = [	
					{ "d1": "entID", 		"d2": "id_entreprise", 			"d3": "Canton", 			"d4": "ent_canton" },
                    { "d1": "Nom", 	 		"d2": "ent_nom", 				"d3": "Fax", 				"d4": "ent_fax" },
                    { "d1": "Rue", 			"d2": "ent_rue", 				"d3": "Téléphone 1", 		"d4": "ent_tel1" },
                    { "d1": "Localité",		"d2": "ent_localite",			"d3": "Téléphone 2", 		"d4": "ent_tel2" },                    
                    { "d1": "NPA", 			"d2": "ent_npa",				"d3": "Mobile 1", 			"d4": "ent_mobile1" },
                    { "d1": "Email", 		"d2": "ent_mail", 				"d3": "Mobile 2", 			"d4": "ent_mobile2" }
                ];
				
				/*** prof ***/
				var darpw = [	
					{ "d1": "profID", 		"d2": "id_professeur", 			"d3": "Code Barre", 			"d4": "pro_codebarre" },
					{ "d1": "Prénom", 	 	"d2": "pro_prenom", 			"d3": "-", 						"d4": "-" },
                    { "d1": "Nom", 	 		"d2": "pro_nom", 				"d3": "Fax", 					"d4": "ent_fax" }
                    
                ];
				/*** Maitre D'apprentissage ***/
				var darew = [	
					{ "d1": "maiID",				 			"d2": "id_maitredapprentissage", 			"d3": "Téléphone 1", 		"d4": "mai_tel1"},
                    { "d1": "Prenom", 	 						"d2": "mai_prenom", 						"d3": "Téléphone 2", 		"d4": "mai_tel2" },
                    { "d1": "Nom", 								"d2": "mai_nom", 							"d3": "Mobile", 			"d4": "mai_mobile" }                    
                ];
                var el_data =  {};
                var el_API = "../api/get-student.php?stu="+el_stu;
                var ent_API = "../api/get-ent.php";
				/*** Get Entreprise Data ***/


                function getEnt( ta, va )
				{
					//var last = $("#choixMaix option:last");
                    //$("#choixMaix option").not(last).remove();
                    var table = $("#choixMaix");
                    (function() {
                        jQuery.getJSON( ent_API, {ent:0, whattoget:"ent"} )
                            .done(function( dat ) {console.log( "JSON Data: " + data);

                                $(dat).each(function(key, value) {
									console.log( "+>"+key+" : "+ value );
                                    //row = $("select");
                                    table.append(	$("<option>"),{key, value} ).text(value);
                                    //table.insertBefore(last);
                                });
                            });
                    })();
				}
				/*** Get Entreprise Data ***/
                function getEntMai( ta, va )
				{
					
				}
                /*** Fill Table with default data ***/
                function getEl( ta, va )
                {   /** if ( ta == '-' ){ return "-"; }
					if ( ta == 'null' ){ return ""; } **/
                    if(el_data.hasOwnProperty(""+ta+"")){	//console.log(ta+" key found and its value is "+ el_data[ta]);
                        if (ta == "ele_mail")                 return "<a href=\"mailto:"+el_data[ta]+"\">"+el_data[ta]+"</a>";
						else if (ta == 'ele_majeur') {
                            var majeur = "Majeur";
                            if ( el_data[ta] == "VRAI")       return "Majeur";
                            else if ( el_data[ta] == "FAUX")  return "Mineur";
							else                              return "/!\\?/!\\";
						}
						
						if ( el_data[ta] == 'null' ) { return "-"; }
						return ""+el_data[ta]+"";
                    }else{	console.log(ta+" key not found");
					if ( ta == '-' ){ return "-"; }
					else if ( ta == 'null' ){ return "-"; }
                    else {
						console.log("M__"+el_data[ta]+"__M")
						return ta;
					}		}        }



                /*** Fill Table in edit mode ***/
                function getEdl( ta, va )
                {
                    if(el_data.hasOwnProperty(""+ta+"")){	//console.log(ta+" key found and its value is "+ el_data[ta]);
                        if (ta == 'ele_datedenaissance' )
						{ 
							return "<input type=\"text\" class=\"form-control\" name=\""+ta+"\" id=\""+ta+"\" value=\""+el_data[ta]+"\" class=\"date-picker naissance-date\"/>";
							//.attr('class', 'date-picker start-date')
						}else
                        if (ta == 'ele_politesse' || ta == 'rep_politesse' )
                        {
                            var sexe = "M";
                            if ( el_data[ta] == "Mme")			                                sexe = "<option value=\"Mme\" selected=\"selected\">Mme</option><option value=\"M.\">M.</option>";
                            else if ( el_data[ta] == "M.")		                                sexe = "<option value=\"Mme\">Mme</option><option value=\"M.\" selected=\"selected\">M.</option>";
                            else if ( el_data[ta] == "Monsieur")                                sexe = "<option value=\"Mme\">Mme</option><option value=\"M.\" selected=\"selected\">M.</option>";
                            else                                 								sexe = "<option value=\"Mme\">Mme</option><option value=\"M.\" selected=\"selected\">M.</option>";
                            return "<select class=\"form-control\" id=\""+ta+"\" name=\""+ta+"\">"+sexe+"</select>";
                        }else if (ta == 'ele_majeur') {
                            var majeur = "VRAI";
                            if ( el_data[ta] == "VRAI")
                                majeur = "<option value=\"VRAI\" selected=\"selected\" >Majeur</option><option value=\"FAUX\">Mineur</option>";
                            else if ( el_data[ta] == "FAUX")
                                majeur = "<option value=\"VRAI\" >Majeur</option><option value=\"FAUX\" selected=\"selected\" >Mineur</option>";
                            else
                                majeur = "<option value=\"VRAI\" >Majeur</option><option value=\"FAUX\">Mineur</option>";
                            return "<select class=\"form-control\" id=\""+ta+"\" name=\"ele_majeur\">"+majeur+"</select>";
                        }else{
                            return "<input type=\"text\" class=\"form-control\" name=\""+ta+"\" id=\""+ta+"\" value=\""+el_data[ta]+"\" />";
                        }
                    }else{	console.log(ta+" key not found");
					if ( ta == '-' ){ return "-"; }
					else if ( ta == 'null' ){ return "-"; }
                    else{
						console.log("M__"+el_data[ta]+"__M")
						return ta;
						}
                    }        }



                /*** Load Data (at document ready) ***/
                function myLoad(targ) {
                    var last = $("#"+targ+" tr:last");
                    $("#"+targ+" tr").not(last).remove();
                    var table = $("#"+targ+"");
                    (function() {
                        jQuery.getJSON( el_API, { format: "json" } )
                            .done(function( dat ) {	//console.log( "JSON Data: " + data);
                                $.each( dat, function( a, b ) {
                                    el_data[a] = b;
                                    //console.log( ">>"+a +" : "+ getEl( a, el_data ) );
                                });
                                data = getDar(targ);
                                $(data).each(function() {
                                    row = $("<tr>");
                                    row.append(	$("<td>").html(this.d1)
                                    ).append(
												$("<td>").html(getEl(this.d2,el_data))
                                    ).append(
												$("<td>").html(this.d3)
                                    ).append(
												$("<td>").html(getEl(this.d4, el_data ))
                                    );
                                    row.insertBefore(last);
                                });
                            });
                    })();
                }



                /*** BTN SAVE It NTB ***/
                function mySave(targ) {
                    document.getElementById(targ).submit();
                }



                /*** BTN RESET NTB ***/
                function myReset(targ) {
                    myLoad(targ);
                }


                /*** BTN Edit It NTB ***/
                function getDar(targ) {
                    if (targ == "tal") { console.log("DARAW"); return daraw;}
                    else if (targ == "tbl") { console.log("DARbW"); return darbw;}
                    else if (targ == "tcl") { console.log("DARcW"); return darcw;}
                    else if (targ == "tdl") { console.log("DARdW"); return dardw;}
					else if (targ == "tml") { console.log("DARmW"); return darmw;}
					else if (targ == "tel") { console.log("DAReW"); return darew;}
                    else{}
                }


                /*** BTN Edit It NTB ***/
                function myEdit(targ) {
                    var last = $("#"+targ+" tr:last");
                    $("#"+targ+" tr").not(last).remove();
                    var table = $("#"+targ+"");
                    (function() {
                        jQuery.getJSON( el_API, { format: "json" } )
                            .done(function( dat ) {	//console.log( "JSON Data: " + data);
                                $.each( dat, function( a, b ) {
                                    el_data[a] = b;						//console.log( ">>"+a +" : "+ getEl( a, el_data ) );
                                });
                                data = getDar(targ);
                                $(data).each(function() {
                                    row = $("<tr>");
                                    row.append(	$("<td>").html(this.d1)
                                    ).append(
                                        $("<td>").html(getEdl(this.d2,el_data))
                                    ).append(
                                        $("<td>").html(this.d3)
                                    ).append(
                                        $("<td>").html(getEdl(this.d4,el_data))
                                    );
                                    row.insertBefore(last);
                                });
                            });
                    })();
                }



                /*** document ready ***/
                $(document).ready(function() {                                    });
						myLoad("tal");
						myLoad("tbl");
						if (el_representantlegal != "0")
						{		myLoad("tcl");
						}myLoad("tml");myLoad("tel");
						
						
						/*$("#myForm :input").prop("disabled", true);
				       $('#myModal').on('shown.bs.modal', function () {
                $('#myInput').focus()
            })*/

            </script>


</body>

</html>
