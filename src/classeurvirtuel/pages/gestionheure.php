<?php
    session_start();
    include '../include/bdd.php';

	$id_classe='';
	if (isset ($_POST['idx_classe']))
{  $id_classe=@$_POST['idx_classe'];
}
if (isset ($_GET['idx_classe']))
{  $id_classe=@$_GET['idx_classe'];
}
 
	
if (isset( $_GET['idclasse']))
{
	 $id_classe=$_GET['idclasse'];
	 }
	 
   
    $id_prof=@$_POST['idx_professeur'];
    $id_salle=@$_POST['idx_salle'];




if (isset($_GET['modifiercours']))

{



    $jourEvent = $_POST ['mjour'];
    $alternance= $_POST ['malternance'];

    $req=$bdd->prepare("select * from t_horaire where  id_horaire='".$_POST ['mperiode']."'");
    $req->execute();
    $periode=$req->fetchAll();

    $startdate = $periode[0]['debutDePeriode'];

    $finish=(int)$_POST['mperiode'] + (int)$_POST ['mduree']- 1;
    $req=$bdd->prepare("select * from t_horaire where  id_horaire='". $finish."'");
    $req->execute();
    $periodeF=$req->fetchAll();

    $enddate= $periodeF[0]['finDePeriode'];






    if($alternance=='H')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST['mclasse']."' and cou_jour='$jourEvent' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')");

        //$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."' and cou_jour='$jourEvent' and  (cou_heuredebut between '$startdate' and  '$enddate'  or cou_heurefin between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if($alternance=='S.I')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S.P' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S2p'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if ($alternance=='S.P')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S.I'    and   cou_alternance <> 'S1i'  and   cou_alternance <> 'S2i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if($alternance=='S1')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S2' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if($alternance=='S2')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	//Alternances CEPM
 else if($alternance=='S1p')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S2' and   cou_alternance <> 'S2p' and   cou_alternance <> 'S2i' and   cou_alternance <> 'Q1'  and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	else if($alternance=='S2p')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'Q1'  and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	else if($alternance=='S1i')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S2' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.P' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	else if($alternance=='S2i')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'Q2' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	


    if ( count($verification)==0)
    {


        $sql="update `t_sdh` set `cou_duree` = '".$_POST ['mduree'] ."'
, `cou_alternance`='".$_POST ['malternance'] ."'
, `cou_matcode`='".$_POST ['mmatcode'] ."'
, `cou_matlibelle`='".$_POST ['mmatlibelle'] ."'
, `cou_jour`='".$_POST ['mjour']."'
, `cou_heuredebut`='".$startdate."'
, `cou_heurefin`='".$enddate ."'
, `cou_periode`='".$_POST ['mperiode'] ."'
, `idx_professeur`='".$_POST ['mprofesseur'] ."'
, `idx_salle`='".$_POST ['msalle'] ."'
, `idx_classe`='".$_POST ['mclasse'] ."' where id_sdh ='".$_POST ['eventid'] ."'";
	
        $req=$bdd->prepare($sql);
        $req->execute();




      

        header('Location: gestionheure.php?idclasse='.$_POST ['mclasse'].'&success=1' );
    }

    else
    {

        header('Location: gestionheure.php?idclasse='.$_POST ['mclasse'].'&success=0');
    }
    /* $sql="INSERT INTO `t_sdh` ( `cou_duree`, `cou_alternance`, `cou_matcode`,
`cou_matlibelle`, `cou_jour`, `cou_heuredebut`, `cou_periode`, `idx_professeur`,
`idx_salle`
`idx_classe`,
`temp_prenomprof`) VALUES
 ( '".$_POST ['cou_duree'] ."', '".$_POST ['cou_alternance'] ."', '".$_POST ['cou_matcode'] ."',
 '".$_POST ['cou_matlibelle'] ."', '".$_POST ['cou_jour'] ."', '00:00:00',
 ".$_POST ['cou_periode'] .", ".$_POST ['idx_professeur'] .", '".$_POST ['idx_salle'] ."',
 '', '', '', '', ".$_POST ['idx_classe'] .", 0, 0, 0, 0, 0, 'David')";
*/





}
//Vérification des horaires de la meme journée//




if (isset($_GET['success']))
{
	if ($_GET['success']==1)
	{
	$confirmation="<div class='alert-box success'><span>Succès: </span>Le cours a été crée avec succes.</div>";
	}
	else
	{
	$confirmation="<div class='alert-box warning'><span>Attention: </span>Un cours est programmé durant cet intervalle.</div>";
	}
	}
	else{$confirmation="";}
//	  $requete2=$bdd->prepare("delete from t_sdh where id_sdh='".$_GET['suppcours']."'");
  //  $requete2->execute();


if (isset($_GET['creercours']))

{
	
	
	 
	    $jourEvent = $_POST ['cou_jour'];
		$alternance= $_POST ['cou_alternance'];
		
		$req=$bdd->prepare("select * from t_horaire where  id_horaire='".$_POST ['cou_periode']."'");
		$req->execute();
		$periode=$req->fetchAll();
		
		$startdate = $periode[0]['debutDePeriode'];
		
		$finish=(int)$_POST['cou_periode'] + (int)$_POST ['cou_duree']- 1;
		$req=$bdd->prepare("select * from t_horaire where  id_horaire='". $finish."'");
		$req->execute();
		$periodeF=$req->fetchAll();
		
		$enddate= $periodeF[0]['finDePeriode'];
		
		
		
		
		if($alternance=='H')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST['mclasse']."' and cou_jour='$jourEvent' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')");

        //$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."' and cou_jour='$jourEvent' and  (cou_heuredebut between '$startdate' and  '$enddate'  or cou_heurefin between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if($alternance=='S.I')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S.P' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S2p'  and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if ($alternance=='S.P')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S.I'    and   cou_alternance <> 'S1i'  and   cou_alternance <> 'S2i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if($alternance=='S1')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S2' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
    else if($alternance=='S2')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	//Alternances CEPM
 else if($alternance=='S1p')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S2' and   cou_alternance <> 'S2p' and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.I'  and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	else if($alternance=='S2p')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'S.P'  and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	else if($alternance=='S1i')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and   cou_alternance <> 'S2p'  and   cou_alternance <> 'S2i' and   cou_alternance <> 'S.I' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	else if($alternance=='S2i')
    {
        $req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['mclasse']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S1' and   cou_alternance <> 'S1p'  and   cou_alternance <> 'S1i' and   cou_alternance <> 'S.P' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
        $req->execute();
        $verification=$req->fetchAll();
    }
	

	 /*
	if($alternance=='H')
	{
	$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST['idx_classe']."' and cou_jour='$jourEvent' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate')");
	
	//$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."' and cou_jour='$jourEvent' and  (cou_heuredebut between '$startdate' and  '$enddate'  or cou_heurefin between '$startdate' and  '$enddate') ");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if($alternance=='S.I')
	{
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."' and cou_jour='$jourEvent' and  cou_alternance <> 'S.P' and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if ($alternance=='S.P')
	{
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S.I' and   (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if($alternance=='S1') 
	{
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."'  and cou_jour='$jourEvent' and  cou_alternance <> 'S2' and  (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)  between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
	$req->execute();
	$verification=$req->fetchAll();
	}
	else if($alternance=='S2')
	{
		$req=$bdd->prepare("select * from t_sdh where  idx_classe='".$_POST ['idx_classe']."'  and cou_jour='$jourEvent' and   cou_alternance <> 'S1' and (DATE_ADD(cou_heuredebut,INTERVAL 1 SECOND)   between '$startdate' and  '$enddate'  or DATE_ADD(cou_heurefin,INTERVAL -1 SECOND)  between '$startdate' and  '$enddate') ");
	$req->execute();
	$verification=$req->fetchAll();
	}*/
	 
	
		if ( count($verification)==0)
		{
		 	
		
		 $sql="INSERT INtO `t_sdh` ( `cou_duree`, `cou_alternance`, `cou_matcode`,
`cou_matlibelle`, `cou_jour`, `cou_heuredebut`,`cou_heurefin`, `cou_periode`, `idx_professeur`,
 `idx_salle`, `idx_classe`) VALUES
	( '".$_POST ['cou_duree'] ."', '".$_POST ['cou_alternance'] ."', '".$_POST ['cou_matcode'] ."',
	'".$_POST ['cou_matlibelle'] ."', '".$_POST ['cou_jour'] ."', '$startdate','$enddate',
	".$_POST ['cou_periode'] .", ".$_POST ['idx_professeur'] .", '".$_POST ['idx_salle'] ."',
	 ".$_POST ['idx_classe'] .")"; 	 
	     
     
       
	$req=$bdd->prepare($sql);
               $req->execute();
			    
      
     $sql="update t_sdh set id_cours=id_sdh where id_cours is null"; 	 
	$req=$bdd->prepare($sql);
    $req->execute();
	
		header('Location: rec_act1.php?id=$id&fam_name=$fam_name');
			  
		  header('Location: gestionheure.php?idclasse='.$_POST ['idx_classe'].'&success=1' );
		}
		
	else
	{
		 
		  header('Location: gestionheure.php?idclasse='.$_POST ['idx_classe'].'&success=0');
	}
       /* $sql="INSERT INTO `t_sdh` ( `cou_duree`, `cou_alternance`, `cou_matcode`,
`cou_matlibelle`, `cou_jour`, `cou_heuredebut`, `cou_periode`, `idx_professeur`,
 `idx_salle`
  `idx_classe`,
   `temp_prenomprof`) VALUES
	( '".$_POST ['cou_duree'] ."', '".$_POST ['cou_alternance'] ."', '".$_POST ['cou_matcode'] ."',
	'".$_POST ['cou_matlibelle'] ."', '".$_POST ['cou_jour'] ."', '00:00:00',
	".$_POST ['cou_periode'] .", ".$_POST ['idx_professeur'] .", '".$_POST ['idx_salle'] ."',
	'', '', '', '', ".$_POST ['idx_classe'] .", 0, 0, 0, 0, 0, 'David')";
*/
     
    


	
}
//Vérification des horaires de la meme journée//


if (isset($_GET['valeur_id']))
{


     
  

}

















    
    function getDatesFromRange($startDate, $endDate) {
            $return = array($startDate);
            $start = $startDate;
            $i = 1;
            if (strtotime($startDate) < strtotime($endDate)) {
            while (strtotime($start) < strtotime($endDate)) {
            $start = date('Y-m-d', strtotime($startDate . '+' . $i . ' days'));
            $return[] = $start;
            $i++;
            }
            }

            return $return;
            }

    //echo $id_classe.';'.$id_prof.';'.$id_salle;
    if(isset($_POST['ete_debut']) AND isset($_POST['ete_fin'])){
        $sql="insert into t_vacance (debutDeVacances, finDeVacances, nomVacances) values (".$_POST['ete_debut'].", ".$_POST['ete_fin'].", 'Vacance d été')";  
        $req=$bdd->prepare($sql);
        $req->execute();
    }

    // Selection de toute les vacances inscrites dans la bdd
    $requete=$bdd->prepare('select * from t_vacance');
    $requete->execute();
    $vacance=$requete->fetchAll();
    
    $complet= Array();

    
    
    if(count($vacance)!=0){  
          for($a=0;$a<count($vacance);$a++){

              $startDate=$vacance[$a]['debutDeVacances'];
              $endDate=$vacance[$a]['finDeVacances'];

              $dates=getDatesFromRange($startDate, $endDate);
              $complet = array_merge($complet, $dates);
              $startDate=NULL;
              $endDate=NULL;
              $dates=NULL;
          }
    }
    //print_r($complet);
    
    if($id_classe!=''){
        
        $sql="select * from t_sdh where idx_classe=".$id_classe;
        $req=$bdd->prepare($sql);
        $req->execute();
        $cours=$req->fetchAll();
        
        

        $sql1="select * from t_classe where id_classe=".$id_classe."";
        $req1=$bdd->prepare($sql1);
        $req1->execute();
        $infoClasse=$req1->fetchAll();
        
        $sql2="select * from t_horaire order by id_horaire asc";
        $req2=$bdd->prepare($sql2);
        $req2->execute();
        $periode=$req2->fetchAll();
        //var_dump($periode);
        
        //ce switch set une coleur a aprtir du département de la classe choisie
         for($a=0;$a<count($cours);$a++){
			 
	
			
			  switch($cours[$a]['cou_alternance']){
				  case 'S1p':
                    $color="FEE347";
                    break;
					case 'S2p':
                    $color="EDD38C";
                    break;
					case 'S1i':
                    $color="FFDEAD";
                    break;
					case 'S2i':
                    $color="CD5C5C";
                    break;
             case 'Q1':
                    $color="ab3fdd";
                    break;
             case 'Q2':
                    $color="B0E0E6";
                    break;
             case 'H':
                    $color="ae163e";
                    break;
             case 'S1':
                    $color="13b4ff";
                    break;
             case 'S2':
                    $color="90EE90";
                    break;
           
            default:
                    $color="000000";
                    break;
		 }}
        // permet de numéroter les jours de la semaine le 1 est lundi et 7 dimanche
        //var_dump ($infoClasse);
        switch($infoClasse[0]['cla_jourdecours']){
            case 'Lundi':
                  $jour=1;
                 break;

            case 'Mardi':
                 $jour=2;
                 break;

            case 'Mercredi':
                 $jour=3;
                 break;

            case 'Jeudi':
                 $jour=4;
                 break;

            case 'Vendredi':
                 $jour=5;
                 break;
            default:
                $jour = 0;
        }
        
        $complet= Array();

        $i = 1;
    
        // prend le date du premier aout (choisi parce que c est en milieu d'annee durant les vacances d'été) et la converti en format date 
        $date = new DateTime('2017-08-01');
          $str = $date->format('Y-m-d');
         //echo date('W',$str).'<br>';
        // clone la variable date pour ne pas l'affecter et ajoute une annee pour permettre la date de fin d année 
        $endDate =new DateTime('2017-08-01');
        $endDate -> modify('+1 year');
         
        
        //echo "Test:" .$date->format('N');
        //echo "jour".$jour;
    
        // cette boucle permet de trouver la date du jour de cours de la classe
        /*while($date->format('N') != $jour){
            
            $date -> modify('+1 day');
            //var_dump($date);
        }*/
        /*var_dump($date);
          echo "<br>";
          var_dump($endDate);*/
    
        // Cette boucle va permettre de parcours tout les jours de cours d'une classe pendant une année et va créer un evenement de chaque cours
        while($date <  $endDate){
            if($i%2==0){$tab=$cours;}elseif($i%2!=0){$tab=$cours;}
            $strDate = $date->format('Y-m-d');
            //echo $strDate."<br>";
          // echo $i.'<br>';
            //print_r($tab); echo '<br>';
            for($a=0;$a<count($tab);$a++){
                
                            $debut=$cours[$a]['cou_heuredebut'];
                        $fin=$cours[$a]['cou_heurefin'];
                $events=([
                   'id' => $tab[$a]['id_sdh'],
                   'resourceId' => 'a',
                   'start' => $strDate.'T'.$debut,
                   'end' => $strDate.'T'.$fin,
                   'title' => ''.$tab[$a]['cou_matlibelle'].'',
                   'color'=> '#'.$color,
                   'textColor'=> 'black'
                ]);
                   
                //print_r($events);   echo"<br>"; 
                array_push($complet, $events);
            }
              
            $date->modify('+1 week');
            //print_r($date);
            //echo ":";
    
            $i++;
             	
        }
    }
//vERIF CLASSE NOT VIDE ICI HNA
/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
       
     if($id_classe!=''){
		 
		 
		 
         
        $sql1="select * from t_sdh where idx_classe=".$id_classe;
        $req1=$bdd->prepare($sql1);
        $req1->execute();
        $cours=$req1->fetchAll();
         
        $sql2="select * from t_horaire order by id_horaire asc";
        $req2=$bdd->prepare($sql2);
        $req2->execute();
        $periode=$req2->fetchAll();
        //print_r($cours_prof);
        
        $complet= Array();
        
        for($a=0;$a<count($cours);$a++){
			
			
			
			
        
                switch($cours[$a]['cou_jour']){
                    case 'lundi':
                          $jour=1;
                         break;

                    case 'mardi':
                         $jour=2;
                         break;

                    case 'mercredi':
                         $jour=3;
                         break;

                    case 'jeudi':
                         $jour=4;
                         break;

                    case 'vendredi':
                         $jour=5;
                         break;
                    default:
                        $jour = 0;
                }


                $i = 1;
                

                // prend le date du premier aout (choisi parce que c est en milieu d'annee durant les vacances d'été) et la converti en format date 
                $date = new DateTime('2017-08-01');


                // clone la variable date pour ne pas l'affecter et ajoute une annee pour permettre la date de fin d année 
                $endDate =new DateTime('2017-08-01');
                $endDate -> modify('+1 year');


                // cette boucle permet de trouver la date du jour de cours de la classe
                while($date->format('N') != $jour){

                    $date -> modify('+1 day');
                    //var_dump($date);
                }

                // Cette boucle va permettre de parcours tout les jours de cours d'une classe pendant une année et va créer un evenement de chaque cours
                while($date <  $endDate){

				
				
				
			
				
                    $strDate = $date->format('Y-m-d');
				 
				 	$dates=explode('-',$strDate);
					$numsemaine	= date('W',mktime(0,0,0,$dates[1],$dates[2],$dates[0])) ;
				 
				 	if($strDate > "2017-01-22") 
					{
						
						
						  switch($cours[$a]['cou_alternance']){
					  case 'S1p':
                    $color="FEE347";
                    break;
					case 'S2p':
                    $color="EDD38C";
                    break;
					case 'S1i':
                    $color="FFDEAD";
                    break;
					case 'S2i':
                    $color="CD5C5C";
                    break;
             case 'S.I':
                    $color="ab3fdd";
                    break;
             case 'Q2':
                    $color="B0E0E6";
                    break;
             case 'H':
                    $color="ae163e";
                    break;
             case 'S1':
                    $color="13b4ff";
                    break;
             case 'S2':
                    $color="90EE90";
                    break;
           
            default:
                    $color="000000";
                    break;
           
            
        }
						
					    $debut=$cours[$a]['cou_heuredebut'];
                        $fin=$cours[$a]['cou_heurefin'];
						
                       // echo $debut.': '.$fin.'<br>';
                        $events=([
                           'id' => $cours[$a]['id_sdh'],
                           'resourceId' => 'a',
                           'start' => $strDate.'T'.$debut,
                           'end' => $strDate.'T'.$fin,
                           'title' => ''.$cours[$a]['cou_matlibelle'],
                           'color'=> '#'.$color,
                            'textColor'=> 'black'
                        ]);
                        //print_r($events);    
                        array_push($complet, $events);
					
                        $i++;
					}
					
						
						
						
                      
                        $date->modify('+1 week');
                        //var_dump($date);

				
                } 
         }
    }

/*------------------------------------------------------------------------------------------------------------------------------------*/

   

    $requete=$bdd->prepare('SELECT * FROM t_classe ORDER BY cla_nom ASC');
	$requete->execute();
	$classe=$requete->fetchAll();

    $requete1=$bdd->prepare('SELECT * FROM t_professeur ORDER BY pro_nom ASC');
	$requete1->execute();
	$professeur=$requete1->fetchAll();

    $requete2=$bdd->prepare('SELECT * FROM t_salle ORDER BY sal_nom ASC');
	$requete2->execute();
	$salle=$requete2->fetchAll();


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
    

	
	
	
	
    <link rel="stylesheet" href="assets/css/calendar/fullcalendar.css" />
    <link rel="stylesheet" href="assets/css/calendar/fullcalendar.print.css" media="print"/>
    <link rel="stylesheet" href="assets/css/calendar/scheduler.css" />
    
    <script src="assets/js/calendar/moment.min.js"></script>
    <script src="assets/js/calendar/jquery.min1.js"></script>
    <script src="assets/js/calendar/fullcalendar.js"></script>
    <script src="assets/js/calendar/scheduler.js"></script>
    <script src="assets/js/calendar/locale-all.js"></script>
  <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script> 
 
     
       
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




<style>
.alert-box {
    color:#555;
    font-family:Tahoma, Geneva, Arial, sans-serif;
    font-size:11px;
    padding:10px 36px;
    margin:10px;
    color:#fff;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    -khtml-border-radius: 5px;
    border-radius: 5px;
}
.alert-box span {
    font-weight:bold;
    text-transform:uppercase;
    letter-spacing: 1px;
}
.error {
    background:#e47c68 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAC3UlEQVR4Xm2RX0iTXQDGn1c3N2uzbUwvwrpww9jcRW5qlvLdqNCHeKUGaxd+qFdeBUEiSYYYKJ9/SnCwm9puhjGD11ARTOFTIckMW15JGJVDy/xE9y7d9r7ndHZgMsQfPBfnvM/z8PIcnOW/7u6/t/4d9EcGh/27T0f9u89G/ZGhYf+XoSH/cnd3I84gIIMJt7vBeckQUtbWNFAIQCkoE0AAQYCmvFz+GI22zBqNQa/XyzOqdNhbU9Ngk+XQ8aSoobKM80js7KiKq6oCe5ubYATByAKj02ptKCUkRBYXNSeKgi9GIyjAleabyYRj9k1eWlJdTyQCj4uL7yJFk91+e9JecrJeUEDf5efT5UeP6PHREZ2rr6dhdk5pvq6OSvv7dHVkhK6y8wbTnM2WbHU47uC+zeZfY6YPZjN939hICSGUwUvesJL52loeTvOpre20uMdiEbPI4RFYBAoYi4vYCwTASpBz8SJuBYO4MT6OXIOB3/0/MQFlairl58qOxaCiiszDAsBX//rgAWRm1jc38w2gViMmSTiensb2vXsAIenn4yUqWZZBBAECtwvcsPr8OUoqK6HR6bhZjscRZneXFQUC86ZJUEB1QgHCw+AlEasVRQMDSL1GIhrl709Y8GpfHz53duLqxsZpSZwSZEmaHBCA68higbmnByeUIsrC6pUVqObnIUkx/E4mkf/wIQ4dDvDNUp7sbGRtm0yv17XaJAGQd3AAQyIBKSrBEA7jR1cXfvb24tLKW8TYYHmsxMg8CoDNCxfIlsEg8u2qTCZ3q14fKJIktdpshpENuOfzpQfjv2xub4c0M4N4JILvOh0JxGIdcbvdxws8Hg9+LSy4Pbm5gSuHh2qcD1/+h15PXsbjHerqat/LUAgCMvirsND9j1YbuJYqoZQPmMk2C79IJjtcHo/vSX8/zqXO6WoauFEp+ktLxSmXS5wtKxNfOZ2ir6JC9Ny82eIdG0MmfwCjX3/U2vu6zQAAAABJRU5ErkJggg==) no-repeat 10px 50%;
    border:1px solid #d46c57;
}
.error:hover {  background-color: #d46c57; }
.success {
    background:#4cbe83 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAmJJREFUeNqkk0toE0Ech3+T3aRJX7RpPNgSgzQljYXiC1FbUcFrL9WTqAe96NGce+hF8KA5eVHsSaQni1CR4kHEFwoVxNrW0iJtA9lqk1TJbnZ2d3bGnbWPDT124Fvm9f32v+wMEUJgL02VD/IkASjEQw5IJwiGvd6AR3JzX8HjAwQmIEQRrjdyBcTV0v+AQBuKqpFcpiuTTiWS8eaG5qisz7D0I8vrK4MLxcWLlmPlvanJugq25NaGltFzfWezKpQYsxl0W99aa0x3dDcm25Mdb+fejVZNf94PCW1u6GwIRXJnegeyds2K6boOSmkdz3oeg5lO7GT6RDZCwjnp7AQwMdyzvztNdRozDAOmadZxt3vE3zZ1eNwLYbFUPJmWTjDgdKIpEa9Wq7Asy0dWsfZ7DTejV9BWbkKhUMC1l7cwOzcLTnlcOsGAAwqUqOu6+Hx+ClpZw8qvFaRIF061H4eqqhhbfooXpVdwQg6oTaPSCQaAuQw3Dl7GzMwMpg6N42iiHw/77/ny69J7PCiOATH4MJX5zk6AI1ZLxjod+XYHiqIgHA7jUe99hNUwFms/cXt5BLyZe/8CPjaxqHSCFXxcW9cqSlzB4I8h/61bXFq8DrRhW5bQaq0inWDAxJ/V8lIIxCRdBMe+X/DlvulBYF+9zLlrWpq5JJ2dAC6KrsHy5U/avGDcJCmCvq+enML2d0u4w0x9ujLPa25eOvUnkYtJpln4+1zLRbJN6UimMa6oalQuuRuM2gu1ij1vLHFH5NGqeKeQ7DrKfggvsS/0zcawx+7LpJAJtCjFoEL2ep3/CTAAj+gy+4Yc2yMAAAAASUVORK5CYII=) no-repeat 10px 50%;
    border:1px solid #36ad6f;
}
.success:hover { background-color: #36ad6f; }
.warning {
    background:#feb742 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABqklEQVR4XqWTvWsUURTFf+/tx7DmA5sUmyB+EGQDCkFRxCFosYWCFgELm2ApCBYW/gOCFpYSrUMsBIv4BwTSCSqaWgsTEDRV2EVBZWffvXIYwhZOEdgLhzmcc+7h3WKCuzPOhI+P80rDzE7WwmAHIHnzVIxxl4qJVaKbkYrBxvyVZQRxaYcq0EmehvePzp5YnD67hCAuzd0PUWB2JNQazzo377D7+auAuDR51QWjZWxYvD2e34DsJw+fbwviSJOnTHWBO5aGt6fa84szF67CzguCIYgjTZ4yuP9fYGqO2avO8j348hSKff4OkiAuDXnKKDsqGD1989jSLWJvA/58g+YUv34Xgrg0eSij7MEpsXx66k62O932wjT030NjAuotXj/YE8SlyUMZZbWj3ejmEFubp69fg711yCYha0GWcXftjCAuTZ4yKKsd7dbNfHXuUk6jeAPNCSBCAJpGb78PiGel7gCmLHMXc76/21oNn57kfm5lFg0W0KBPDag7GoYBEuCUE0uy/fIH4cOjy27J0SlI56DEiSVFFi4dEUUIMRBrQZTzjDFj/87/ACmm3+QFX8sKAAAAAElFTkSuQmCC) no-repeat 10px 50%;
    border:1px solid #eda93b;
}
.warning:hover { background-color: #eda93b; }
.notice {
    background:#77d3e0 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAwFBMVEX///8AVq0CYcADbNEDaMoDa88Das0EcdkFfe0CZMPv9fu/1esFe+kBXbkEdd8Uf+QEb9VRkdAGf/AFeecRacAEc9wBW7QBX7zs6dwFeudRjswGgvUFd+MCZscDbtTl4tIBWbFEo/rh3s7A2/QAVasCZcYEcNcBYL3n5NUFfOsWhe5UqPVCkNvj4NDd2swyhNMGgPIBXLbq59nB4Pw1m/lToOgCY8RBi9Lv9/8FduHm49QxfMfo5dcQYrNTnuVBhMhJU/nRAAAAAXRSTlMAQObYZgAAAMpJREFUeF4lzdVyRTEIQFGI57i7XXet+///VZN2vy0YBrA9HPZeF34v4L/XWXtVdRdIenT+/NheE2W8puxiJ7O2TZSq7jLiTMfm3u53YVXhIHriu3BIEuXVdYBI2Yr4Dex3npd22893KnpdFl9Qp2n3FgTbEZkm/oSQGuVSjiOagwJ9CPNcrodhPCEpb9MygycZDZTz0xyNsYhhkXMhGJuf0XhJXIBj1K/02YTGDQA4F042G7SRDwfs5EVo828qn3+sbW6cEZI1rsUvrDkTPAFMyQwAAAAASUVORK5CYII=) no-repeat 10px 50%;
    border:1px solid #6cc8d4;
}
.notice:hover { background-color: #6cc8d4; }
.foo {
  float: left;
  width: 20px;
  height: 20px;
  margin: 40px;
  border: 1px solid rgba(0, 0, 0, .2);
}

.s1 {
  background: #13b4ff;
}

.s2 {
  background: #90EE90;
}

.q1 {
  background: #ab3fdd;
}
.q2 {
  background: #B0E0E6;
}
.h {
  background: #ae163e;
}

.s1p {
  background: #FEE347;
}

.s2p {
  background: #EDD38C;
}

.s1i {
  background: #FFDEAD;
}

.s2i {
  background: #CD5C5C;
}
</style>




    <script>

        var events = <?php echo json_encode($complet); ?>

            console.log(events);
        $(document).ready(function() {

            $.ajax({
                url: 'process.php',
                type: 'POST', // Send post data
                data: 'type=fetch',
                async: false,
                success: function(s){
                    json_events = s;
                }
            });
            var currentMousePos = {
                x: -1,
                y: -1
            };
            jQuery(document).on("mousemove", function (event) {
                currentMousePos.x = event.pageX;
                currentMousePos.y = event.pageY;
            });

            $('#external-events.fc-event').each(function() {

                // store data so the calendar knows to render an event upon drop
                $(this).data('event', {
                    title: $.trim($(this).text()), // use the element's text as the event title
                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                });

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                });

            });
            $(function() { // document ready

                $('#calendar').fullCalendar({
                    defaultView: 'agendaWeek',
                    weekends:false,
                    allDaySlot: false,
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    minTime: '07:00:00',
                    maxTime: '21:10:00',
                    height: 700,
                    defaultTimedEventDuration: '00:05:00',
                    allDayDefault: false,
                    lang: 'fr',
                    locale:'fr',
					
                  editable: true,
                    selectable: true,
                    eventLimit: true, // allow "more" link when too many events
			
		 
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			axisFormat: 'H:mm', // uppercase H for 24-hour clock
			timeFormat: 'H:mm',
			snapOnSlots: false, // When dragging/resizing a event it snaps not on the timeslot
			snapDuration: '00:05:00', // instead it does steps of 15 minutes
			
			eventConstraint:{
			start: '07:45', 
			end: '21:10', 
			},
			
                    eventReceive: function(event){
                        var title = event.title;
                        var start = event.start.format("YYYY-MM-DD[T]HH:mm:SS");
                        $.ajax({
                            url: 'process.php',
                            data: 'type=new&title='+title+'&startdate='+start+'&zone='+zone,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response){
                                event.id = response.eventid;
                               // $('#calendar').fullCalendar('updateEvent',event);
							    $( "#send" ).click();
                            },
                            error: function(e){
                                console.log(e.responseText);
 $( "#send" ).click();
                            }
                        });
                        $('#calendar').fullCalendar('updateEvent',event);
                        console.log(event);
                    },

                  
	eventResize: function(event, delta, revertFunc) {
					console.log(event);
					var title = event.title;
					var end = event.end.format("HH:mm:SS");
					var start = event.start.format("HH:mm:SS");
					var date=event.start.format("YYYY-MM-DD");
					$.ajax({
						url: 'process.php',
						data: 'type=resize&title='+title+'&start='+start+'&date='+date+'&end='+end+'&eventid='+event.id,
						type: 'POST',
						dataType: 'json',
						success: function(response){
							if(response.status == 'success')
							alert('Mise à jour effectuée');
						if(response.status == 'failed')
							alert('Un autre cours est programmé durant cet intervalle');
						 $( "#send" ).click();
						},
						error: function(e){
							if(e.status == 'failed')
							//	 revertFunc();
						alert('Un autre cours est programmé: '+e.status);
						 revertFunc();

							$( "#send" ).click();
						}
						
					});
				},
    eventDrop: function(event, delta, revertFunc) {
					console.log(event);
					var title = event.title;
					var end = event.end.format("HH:mm:SS");
					var start = event.start.format("HH:mm:SS");
					var date=event.start.format("YYYY-MM-DD");
					$.ajax({
						url: 'process.php',
						data: 'type=resize&date&title='+title+'&start='+start+'&date='+date+'&end='+end+'&eventid='+event.id,
						type: 'POST',
						dataType: 'json',
						
						success: function(response){
							if(response.status == 'success')
							alert('Mise à jour effectuée');
						if(response.status == 'failed')
							alert('Un autre cours est programmé durant cet intervalle');
						 $( "#send" ).click();
						},
						error: function(e){
							if(e.status == 'failed')
							//	 revertFunc();
						alert('Un autre cours est programmé: '+e.status);
						 revertFunc();

							$( "#send" ).click();
						}
					});
				},
			       
                  eventClick: function(event, jsEvent, view) {
					console.log(event.id);
					//var title = prompt('Libelle du cours:'  , event.title, { buttons: { Ok: true, Cancel: false} });
					
					 
						 console.log(event.id);
                        //$("#cou_lib").html(event.description);
                        $('#modalTitle').html(event.id);
                      
						 $('#Startevent').html(event.start);
						// document.getElementById("suppcours").value = event.id;
						 $('#modifcours').val(event.id);
                       // $('#modallibelle').html(event.description);
                        
                        //document.getElementById('valeur_id').innerHTML = event.title;
                       //  $('#suppcours').html(event.id);
                    //  $('input[type=hidden]').attr({value : event.id});
                     //   $('input[type=text]').attr({value : event.title});

                      //  document.getElementById('#').value=event.title;
                      //  $('#cou_lib').val(event.title);
                      //  $('#modalBody').html(event.description);
                      //  $('#eventUrl').attr('href',event.url);
						 $('#dynamic-content').html('');
                        $('#mycourscalendar').modal("toggle");
						$.ajax({
						url: 'ajax_modal.php',
					  type: 'POST',
					  data: 'id='+event.id,
					  dataType: 'html'
				 })
				 .done(function(data){
					  console.log(data); 
					  $('#dynamic-content').html(''); // blank before load.
					  $('#dynamic-content').html(data); // load here
					  $('#modal-loader').hide(); // hide loader  
				 })
				 .fail(function(){
					  $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
					  $('#modal-loader').hide();
     });

					 
				},
  

                    header: {
                        left: 'next',
                        center: '',
                        right: 'agendaDay,agendaWeek'
                        //right: 'agendaWeek'
                    },
                    views: {
						
                        /*agendaTwoDay: {

                         type: 'agenda',
                         duration: { days: 2 },

                         // views that are more than a day will NOT do this behavior by default
                         // so, we need to explicitly enable it
                         groupByResource: true

                         //// uncomment this line to group by day FIRST with resources underneath
                         //groupByDateAndResource: true
                         }*/
                    },

                    //// uncomment this line to hide the all-day slot

                    events: events,

                    select: function(start, end, jsEvent, view, resource) {
                        console.log(
                            'select',
                            start.format(),
                            end.format(),
                            resource ? resource.id : '(no resource)'
                        );
                    },
                    dayClick: function(date, jsEvent, view, resource) {
                        console.log(
                            'dayClick',
                            date.format(),
                            resource ? resource.id : '(no resource)'
                        );
                    }
                });


            });


        });
        function getFreshEvents(){
            $.ajax({
                url: 'process.php',
                type: 'POST', // Send post data
                data: 'type=fetch',
                async: false,
                success: function(s){
                    freshevents = s;
                }
            });
            $('#calendar').fullCalendar('addEventSource', JSON.parse(freshevents));
        }


        function isElemOverDiv() {
            var trashEl = jQuery('#trash');

            var ofs = trashEl.offset();

            var x1 = ofs.left;
            var x2 = ofs.left + trashEl.outerWidth(true);
            var y1 = ofs.top;
            var y2 = ofs.top + trashEl.outerHeight(true);

            if (currentMousePos.x >= x1 && currentMousePos.x <= x2 &&
                currentMousePos.y >= y1 && currentMousePos.y <= y2) {
                return true;
            }
            return false;
        }
    </script>


 </head>
 
 <body>

     <div id="wrapper">

        <?php include("../include/menu.php"); ?>
        
		            <div id="page-wrapper">
	<h1 class="page-header">Horaire</h1>
		<div>
					 
					 <div class="foo q1"> &nbsp; &nbsp;Semaine Impaire</div>
					<div class="foo q2"> &nbsp; &nbsp;Semaine Paire</div>
					<div class="foo s1"> &nbsp; &nbsp;Semestre 1</div>
					<div class="foo s2"> &nbsp; &nbsp;Semestre 2</div>
					<div class="foo h">  &nbsp; &nbsp;Hebdomadaire</div>
					<div class="foo s1p">  &nbsp; &nbsp;Semestre:1 Paire</div>
					<div class="foo s2p">  &nbsp; &nbsp;Semestre:2 Paire</div>
						<div class="foo s1i">  &nbsp; &nbsp;Semestre:1 Impaire</div>
					<div class="foo s2i">  &nbsp; &nbsp;Semestre:2 Impaire</div>
					
	  <div class="row">
	  <div class="col-lg-4 col-lg-offset-1 col-sm-12 col-sm-offset-0">
	  &nbsp;
			</div>			 
	  </div>	
				</div>	
				
				
				 <form method="get" action="">
						
					
						
						
						
                             <div class="col-lg-3 col-lg-offset-0 col-sm-0 col-sm-offset-0">
                                 <select class="dropdown form-control" name="idx_classe" placeholder="Classe">
                                            <optgroup label="Classe">
                                            <option value='<?php  if(isset ($id_classe)){ echo $id_classe ;}?>'> </option>
                                            <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?> 
                                 </select> 
                            </div>
                           
                            <div class="col-lg-1 col-lg-offset-0 col-sm-0 col-sm-offset-0">
                                <input type="submit" class="btn btn-info" id='send' value="Afficher">
								
                            </div>
							  <div class="col-lg-2 col-lg-offset-0 col-sm-3 col-sm-offset-0">
                                  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#mycours"> Nouveau cours</button>
                            </div>
                            
                            <div class="col-lg-2 col-lg-offset-0 col-sm-3 col-sm-offset-0">
                                <?php if($_SESSION['login']=="Fernandez"){?> 
                         
                          <!--      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal"> Ajuster les dates</button>           -->  
                         
                                <?php } ?>
                            </div>
							
                         </form>
        
        <div class="row">

                    
                     <div class="col-lg-12">
                   
					<?php echo $confirmation; ?>
                    </div>
                
                </div>
                 <div class="row">
				 
                     				
				 </div>
					 <div class="row">
				 
                     <div class="col-lg-9 col-lg-offset-1 col-sm-12 col-sm-offset-0">
                       

                         <br><br>
                      

                        
                         <div id='calendar'></div>




                        
                       <div id="calendar"></div>

                     </div>
                </div>

        </div>
         <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Modal Header</h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            Vacance d'été:
                            <input type="date" name="ete_debut">
                                au
                            <input type="date" name="ete_fin"><br><br>
                            Vacance d'hiver:
                            <input type="date" name="hiver_debut">
                                au
                            <input type="date" name="hiver_fin"><br><br>
                            Semestre 1 du:
                            <input type="date" name="sem_debut">
                            au
                            <input type="date" name="sem_fin"><br><br>
                            <input type="submit" value="envoyer">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>




         <div class="modal fade" id="mycours" role="dialog">
             <div class="modal-dialog">
                 <!-- Modal content-->
                 <div class="modal-content">
                     <div class="modal-header">







                         <button type="button" class="close" data-dismiss="modal">&times;</button>
                         <h4 class="modal-title">Créer un cours</h4>
                     </div>
                     <div class="modal-body">

                             <table class="table table-striped">


                                         <form method="post" action="?creercours=oui">
                                             <tr>   </tr>
                                                 <td>
                            Cours code: </td><td>
                             <input type="text" name="cou_matcode"> </td> </tr><tr><td>

                             Libellé: </td><td>
                             <input type="text" name="cou_matlibelle" value=""> </td> </tr><tr><td>

                             Jour: </td><td>
                             <select class="dropdown form-control" name="cou_jour">
                                 <option value="lundi">Lundi</option>
                                 <option value="mardi">Mardi</option>
                                 <option value="mercredi">Mercredi</option>
                                 <option value="jeudi">Jeudi</option>
                                 <option value="vendredi">Vendredi</option>
                                 <option value="samedi">Samedi</option>
                             </select> </td> </tr><tr><td>

                             Alternance: </td><td>
                             <select class="dropdown form-control" name="cou_alternance">
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
                             <select class="dropdown form-control" name="cou_duree">
                                 <option value="1">1</option>
                                 <option value="2">2</option>
								 <option value="3">3</option>
								 <option value="4">4</option>

                             </select></td> </tr><tr><td>

                             Période début:</td>  <td>
                             <select class="dropdown form-control" name="cou_periode">
                                <?php for($a=0;$a<count($periode);$a++)echo'<option value="'.$periode[$a]['numeroDePeriode'].'">'.$periode[$a]['numeroDePeriode'].' - '. $periode[$a]['debutDePeriode'].'</option>'; ?>



                             </select></td> </tr><tr><td>

                                                     Classe:  </td><td>



                                                         <select  name="idx_classe" placeholder="idx_classe">
                                                             <optgroup label="Classe">
                                                                 <option></option>
                                                                 <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?>
                                                         </select></td> </tr><tr><td>
                                                    Professeur: </td><td>
                                                         <select class="dropdown form-control" name="idx_professeur">
                                                             <optgroup label="Professeur">
                                                                 <option></option>
                                                                 <?php for($a=0;$a<count($professeur);$a++)echo'<option value="'.$professeur[$a]['id_professeur'].'">'.$professeur[$a]['pro_nom'].' '.$professeur[$a]['pro_prenom'].'</option>'; ?>
                                                         </select></td> </tr><tr><td>
                                                   Salle:</td><td>
                                                         <select class="dropdown form-control" name="idx_salle">
                                                             <optgroup label="Salle de classe">
                                                                 <option></option>
                                                                 <?php for($a=0;$a<count($salle);$a++)echo'<option value="'.$salle[$a]['id_salle'].'">'.$salle[$a]['sal_nom'].'</option>'; ?>
                                                         </select></td> </tr>
                                             <tr>        <td colspan="2" align="center"><input type="submit" class="btn btn-primary btn-sm" value="Enregistrer" float="right"></td></tr>
                                         </form>
                             </table>
                     </div>

                     <div id="fullCalModal" class="modal fade">
                         <div class="modal-dialog">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>

                                 </div>
                                 <div id="modalBody" class="modal-body"></div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                     <button class="btn btn-primary"><a id="eventUrl" target="_blank">Event Page</a></button>
                                 </div>
                             </div>
                         </div>
                     </div>








                     <div class="modal-footer">

                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     </div>
                 </div>
             </div>
         </div>




         <div class="modal fade" id="mycourscalendar" role="dialog">
             <div class="modal-dialog">
                 <!-- Modal content-->
                 <div class="modal-content">
                     <div class="modal-header">






                        
                         <button type="button" class="close" data-dismiss="modal">&times;</button>
                         <h4 class="modal-title" id="modalTitle">Modifier le cours</h4>
                         <h4 class="modal-title" id="modalCode">Modifier le cours</h4>
				
					 <input type="hidden" id="modifcours"  name="modifcours" value="">
                  <!--       <input type="submit"  class="btn btn-delete" name="deletecours" value="Supprimer">-->
                     </div>
                     <div class="modal-body">

                         <table class="table table-striped">

						 
<div id="dynamic-content"></div>



                               
                         </table>
                     </div>

                     <div id="fullCalModal" class="modal fade">
                         <div class="modal-dialog">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                                     <h4 id="modalTitle" class="modal-title"></h4>
                                 </div>
                                 <div id="modalBody" class="modal-body"></div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                     <button class="btn btn-primary"><a id="eventUrl" target="_blank">Event Page</a></button>
                                 </div>
                             </div>
                         </div>
                     </div>








                     <div class="modal-footer">

                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     </div>
                 </div>
             </div>
         </div>








         <script>
      $('#myModal').on('shown.bs.modal', function () {
          $('#myInput').focus()
      })
 </script>
 </body>
 
 </html>