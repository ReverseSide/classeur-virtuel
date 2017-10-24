<?php
  $con = mysqli_connect('localhost','nlhj_databuser','classeur123','nlhj_cepm');
  
  
  
include '../include/bdd.php';
include_once('../include/mysql.inc.php');

$type = $_POST['type'];

if($type == 'new')
{
	$startdate = $_POST['startdate'].'+'.$_POST['zone'];
	$title = $_POST['title'];
	$insert = mysqli_query($con,"INSERT INTO calendar(`title`, `startdate`, `enddate`, `allDay`) VALUES('$title','$startdate','$startdate','false')");
	$lastid = mysqli_insert_id($con);
	echo json_encode(array('status'=>'success','eventid'=>$lastid));
}

if($type == 'changetitle')
{
	$eventid = $_POST['eventid'];
	$title = $_POST['title'];
	$update = mysqli_query($con,"UPDATE t_sdh SET cou_matlibelle ='$title' where id_sdh='$eventid'");
	if($update)
		echo json_encode(array('Statut'=>'Succès'));
	
	else
		echo json_encode(array('Statut'=>'Echec'));
	
}

if($type == 'resetdate')
{
	
	$title = $_POST['title'];
	$startdate = $_POST['start'];
	$enddate = $_POST['end'];
	$eventid = $_POST['eventid'];
	$dateevent=$_POST['date'];
	
	
	$cou_jour = date('w', strtotime($dateevent));
	
	
	switch ($cou_jour) {
    case "1":
        $jourEvent = "lundi";
        break;
    case "2":
       $jourEvent = "mardi";
        break;
    case "3":
       $jourEvent = "mercredi";
        break;
		 case "4":
      $jourEvent = "jeudi";
        break;
		 case "5":
       $jourEvent = "vendredi";
        break;
		 case "6":
       $jourEvent = "samedi";
        break;
		 case "7":
        $jourEvent = "dimanche";
        break;
		
}
	//$coursA = mysqli_query($con,"select * from t_sdh where id_sdh='$eventid'");
	
$requete3=$bdd->prepare("select * from t_sdh where id_sdh='$eventid'");
$requete3->execute();
$coursA=$requete3->fetchAll();
	//$coursA[0]['cou_alternance']
	
	
	
	if(($coursA[0]['cou_alternance'])=='H')
	{
		  $req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];

		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S.I') 
	{
		
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S.P' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S2p' and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S.P') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S.I' and   cou_alternance <> 'S1i'  and   cou_alternance <> 'S2i' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S1') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S2' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S2') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	
	///S1p
	
	else if(($coursA[0]['cou_alternance'])=='S1p') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   cou_alternance <> 'S2' and   cou_alternance <> 'S2p' and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.I'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	///S2p
	else if(($coursA[0]['cou_alternance'])=='S2p') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'S.I' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	
	
	///S1i
	
	else if(($coursA[0]['cou_alternance'])=='S1i') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S2' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.P'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	///S2i
	else if(($coursA[0]['cou_alternance'])=='S2i') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'S.P' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	 
	
		if ( count($verification)==0)
		{
		//Recupere les information de l'horaire debut le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heuredebut
			header('Content-Type: application/json');
			
		 		echo json_encode(array('status'=>'success'));
		///Requete de mise à jour
		$update = mysqli_query($con,"UPDATE t_sdh SET  cou_heuredebut = '$startdate',cou_periode='$cou_periode',cou_duree='$cou_duree', cou_heurefin = '".$enddate."',cou_jour='$jourEvent'  where id_sdh='$eventid'");
		
		
		}
		else
		{
			header('Content-Type: application/json');
			echo json_encode(array('status'=>'failed'));
		}
	
}

if($type == 'remove')
{
	$eventid = $_POST['eventid'];
	$delete = mysqli_query($con,"DELETE FROM calendar where id='$eventid'");
	if($delete)
		echo json_encode(array('status'=>'success'));
	else
		echo json_encode(array('status'=>'failed'));
}
if($type == 'resize')
{
	
	$title = $_POST['title'];
	$startdate = $_POST['start'];
	$enddate = $_POST['end'];
	$eventid = $_POST['eventid'];
	$dateevent=$_POST['date'];
	
	
	$cou_jour = date('w', strtotime($dateevent));
	
	
	switch ($cou_jour) {
    case "1":
        $jourEvent = "lundi";
        break;
    case "2":
       $jourEvent = "mardi";
        break;
    case "3":
       $jourEvent = "mercredi";
        break;
		 case "4":
      $jourEvent = "jeudi";
        break;
		 case "5":
       $jourEvent = "vendredi";
        break;
		 case "6":
       $jourEvent = "samedi";
        break;
		 case "7":
        $jourEvent = "dimanche";
        break;
		
}
	//$coursA = mysqli_query($con,"select * from t_sdh where id_sdh='$eventid'");
	
$requete3=$bdd->prepare("select * from t_sdh where id_sdh='$eventid'");
$requete3->execute();
$coursA=$requete3->fetchAll();
	//$coursA[0]['cou_alternance']
	
	
	
	if(($coursA[0]['cou_alternance'])=='H')
	{
		 $req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];


		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S.I') 
	{
		 $req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];

	
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent'  and  cou_alternance <> 'S.P' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S2p'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S.P') 
	{
		 $req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];

		
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S.I' and   cou_alternance <> 'S1i'  and   cou_alternance <> 'S2i' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S1') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];

		
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S2'   and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i'   and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if(($coursA[0]['cou_alternance'])=='S2') 
	{
		 $req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];

	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and      cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	 
	 
	 ///S1p
	
	else if(($coursA[0]['cou_alternance'])=='S1p') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   cou_alternance <> 'S2' and   cou_alternance <> 'S2p' and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.I'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	///S2p
	else if(($coursA[0]['cou_alternance'])=='S2p') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'S.I' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	
	///S1i
	
	else if(($coursA[0]['cou_alternance'])=='S1i') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S2' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.P'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')  and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	///S2i
	else if(($coursA[0]['cou_alternance'])=='S2i') 
	{
		$req1=$bdd->prepare("select   * from t_horaire where t_horaire.debutDePeriode<=  '$startdate' order by t_horaire.debutDePeriode desc limit 1 ");
	$req1->execute();
	$startHoraire=$req1->fetchAll();
	
	//Recupere les information de l'horaire fin le plus proche	 'id_horaire pour t_sdh.cou_periode & sdh_heurefin
	$req2=$bdd->prepare("select  * from t_horaire where t_horaire.finDePeriode<= '$enddate' order by t_horaire.finDePeriode desc limit 1 ");
	$req2->execute();
	$endHoraire=$req2->fetchAll();
	
			
			$startdate = $startHoraire[0]['debutDePeriode'];
			$enddate = $endHoraire[0]['finDePeriode'];
			$cou_duree=  (int)$endHoraire[0]['numeroDePeriode'] -   (int)$startHoraire[0]['numeroDePeriode'] +1;
			$cou_periode=  $startHoraire[0]['numeroDePeriode'];
	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$coursA[0]['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'S.P' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') and  id_sdh<>'$eventid'");
	$req->execute();
	$verification=$req->fetchAll();
	}
	
		if ( count($verification)==0)
		{
	
			
		 	
		
		///Requete de mise à jour
		$update = mysqli_query($con,"UPDATE t_sdh SET  cou_heuredebut = '$startdate',cou_periode='$cou_periode',cou_duree='$cou_duree', cou_heurefin = '".$enddate."',cou_jour='$jourEvent'  where id_sdh='$eventid'");
		header('Content-type: text/html');
		$data = array();
		$data["status"]  = "success";

		echo json_encode( $data );
		//echo json_encode(array('status'=>'success'));
	 		//	  header('Location: gestionheure.php?idclasse='.$_POST ['idx_classe'] );
			
		}	
	else {
		header('Content-type: text/html');
		$data = array();
		$data["status"]  = "failed";

		echo json_encode( $data );
	//	echo json_encode(array('status'=>'failed'));
//header('Location: gestionheure.php?idclasse='.$_POST ['idx_classe'] );

}
}


if($type == 'fetch')
{
	
	
	
	
	
	
}


























?>