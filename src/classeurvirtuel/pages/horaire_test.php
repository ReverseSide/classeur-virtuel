<?php
    session_start();
    include '../include/bdd.php';

    $id_classe=@$_POST['choixClasse'];
    $id_prof=@$_POST['choixProf'];
    $id_salle=@$_POST['choixSalle'];
    
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
        $sql="insert into t_vacance (debutDeVacances, finDeVacances, nomVacances) values ('".$_POST['ete_debut']."', '".$_POST['ete_fin']."', 'Vacance d été')";  
        $req=$bdd->prepare($sql);
        $req->execute();
       
    }

    // Selection de toute les vacances inscrites dans la bdd
    $requete=$bdd->prepare('select * from t_vacance');
    $requete->execute();
    $vacance=$requete->fetchAll();
    
    $complet= Array();
    $holiday= Array();

    
    
    /*if(count($vacance)!=0){  
          for($a=0;$a<count($vacance);$a++){

              $startDate=$vacance[$a]['debutDeVacances'];
              $endDate=$vacance[$a]['finDeVacances'];

              $dates=getDatesFromRange($startDate, $endDate);
              $holiday = array_merge($holiday, $dates);
              $startDate=NULL;
              $endDate=NULL;
              $dates=NULL;
          }
    }*/
//echo count($vacance)."<br>";
    if(count($vacance)!=0){  
          for($a=0;$a<count($vacance);$a++){

              $startDate=$vacance[$a]['debutDeVacances'];
              $endDate=$vacance[$a]['finDeVacances'];
              
              $events=([
                           'id' => $vacance[$a],
                           'resourceId' => 'a',
                           'start' => $startDate,
                           'end' => $endDate,
                           'title' => $vacance[$a]['nomVacances'],
                           'color'=> 'blue',
                            'textColor'=> 'black'
                        ]);
                        //print_r($events);    
                        array_push($complet, $events);
                        
              
          }
    }
    
    //print_r($holiday);
    
    if($id_classe!=''){
        
        $sql="select * from t_cours where idx_classe1=".$id_classe." OR idx_classe2=".$id_classe." OR idx_classe3=".$id_classe." OR idx_classe4=".$id_classe."";
        $req=$bdd->prepare($sql);
        $req->execute();
        $cours=$req->fetchAll();
        
        $sql3="select * from t_cours where cou_alternance!='S.I' AND idx_classe1=".$id_classe." OR idx_classe2=".$id_classe." OR idx_classe3=".$id_classe." OR idx_classe4=".$id_classe."";
        $req3=$bdd->prepare($sql3);
        $req3->execute();
        $cours_impaire=$req3->fetchAll();
        
        $sql4="select * from t_cours where  cou_alternance!='S.P' AND idx_classe1=".$id_classe." OR idx_classe2=".$id_classe." OR idx_classe3=".$id_classe." OR idx_classe4=".$id_classe."";
        $req4=$bdd->prepare($sql4);
        $req4->execute();
        $cours_paire=$req4->fetchAll();

        $sql1="select * from t_classe where id_classe=".$id_classe."";
        $req1=$bdd->prepare($sql1);
        $req1->execute();
        $infoClasse=$req1->fetchAll();
        
        $sql2="select * from t_horaire";
        $req2=$bdd->prepare($sql2);
        $req2->execute();
        $periode=$req2->fetchAll();
        //var_dump($periode);
        
        //ce switch set une coleur a aprtir du département de la classe choisie
        switch($infoClasse[0]['idx_departement']){
             case 1:
                    $color="B4FF01";
                    break;
             case 2:
                    $color="FF0000";
                    break;
             case 3:
                    $color="0000FF";
                    break;
             case 4:
                    $color="FFFF00";
                    break;
             case 5:
                    $color="C0C0C0";
                    break;
             case 6:
                    $color="FF0080";
                    break;
            default:
                    $color="000000";
                    break;
        }
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
        $date = new DateTime('2016-08-01'); 
          $str = $date->format('Y-m-d');
         //echo date('W',$str).'<br>';
        // clone la variable date pour ne pas l'affecter et ajoute une annee pour permettre la date de fin d année 
        $endDate =new DateTime('2016-08-01');
        $endDate -> modify('+1 year');
         
        
        //echo "Test:" .$date->format('N');
        //echo "jour".$jour;
    
        // cette boucle permet de trouver la date du jour de cours de la classe
        while($date->format('N') != $jour){
            
            $date -> modify('+1 day');
            //var_dump($date);
        }
        /*var_dump($date);
          echo "<br>";
          var_dump($endDate);*/
    
        // Cette boucle va permettre de parcours tout les jours de cours d'une classe pendant une année et va créer un evenement de chaque cours
        while($date <  $endDate){
            if($i%2==0){$tab=$cours_paire;}elseif($i%2!=0){$tab=$cours_impaire;}
            $strDate = $date->format('Y-m-d');
            //echo $strDate."<br>";
          // echo $i.'<br>';
            //print_r($tab); echo '<br>';
            for($a=0;$a<count($tab);$a++){
                
                $temp=$tab[$a]['cou_periode']-1;
                $debut=$periode[$temp]['debutDePeriode'];
                $fin=$periode[$temp]['finDePeriode'];
               
                $events=([
                   'id' => $tab[$a]['id_cours'],
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

/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
       
     if($id_prof!=''){
         
        $sql1="select * from t_cours where idx_professeur=".$id_prof."";
        $req1=$bdd->prepare($sql1);
        $req1->execute();
        $cours_prof=$req1->fetchAll();
         
        $sql2="select * from t_horaire";
        $req2=$bdd->prepare($sql2);
        $req2->execute();
        $periode=$req2->fetchAll();
        //print_r($cours_prof);
        
        $complet= Array();
        
        for($a=0;$a<count($cours_prof);$a++){
        
                switch($cours_prof[$a]['cou_jour']){
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
                $date = new DateTime('2016-08-01'); 


                // clone la variable date pour ne pas l'affecter et ajoute une annee pour permettre la date de fin d année 
                $endDate =new DateTime('2016-08-01');
                $endDate -> modify('+1 year');


                // cette boucle permet de trouver la date du jour de cours de la classe
                while($date->format('N') != $jour){

                    $date -> modify('+1 day');
                    //var_dump($date);
                }

                // Cette boucle va permettre de parcours tout les jours de cours d'une classe pendant une année et va créer un evenement de chaque cours
                while($date <  $endDate){

                    $strDate = $date->format('Y-m-d');



                        $temp=$cours_prof[$a]['cou_periode']-1;
                        $debut=$periode[$temp]['debutDePeriode'];
                        $fin=$periode[$temp]['finDePeriode'];
                       // echo $debut.': '.$fin.'<br>';
                        $events=([
                           'id' => $cours[$a]['id_cours'],
                           'resourceId' => 'a',
                           'start' => $strDate.'T'.$debut,
                           'end' => $strDate.'T'.$fin,
                           'title' => ''.$cours_profs[$a]['cou_matlibelle'].''.$cours_prof[$a]['idx_salle'],
                           'color'=> 'blue',
                            'textColor'=> 'black'
                        ]);
                        //print_r($events);    
                        array_push($complet, $events);

                        $date->modify('+1 week');
                        //var_dump($date);

                        $i++;
                    
                } 
         }
    }

/*------------------------------------------------------------------------------------------------------------------------------------*/

    if($id_salle!=''){
        
        $sql="select * from t_salle where id_salle=".$id_salle."";
        $req=$bdd->prepare($sql);
        $req->execute();
        $salle=$req->fetchAll();
         
        $sql1="select * from t_cours where idx_salle='".$salle[0]['sal_nom']."'";
        $req1=$bdd->prepare($sql1);
        $req1->execute();
        $cours_salle=$req1->fetchAll();
         
        $sql2="select * from t_horaire";
        $req2=$bdd->prepare($sql2);
        $req2->execute();
        $periode=$req2->fetchAll();
        //print_r($cours_prof);
        
        $complet= Array();
        
        for($a=0;$a<count($cours_salle);$a++){
        
                switch($cours_salle[$a]['cou_jour']){
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
                $date = new DateTime('2016-08-01'); 


                // clone la variable date pour ne pas l'affecter et ajoute une annee pour permettre la date de fin d année 
                $endDate =new DateTime('2016-08-01');
                $endDate -> modify('+1 year');


                // cette boucle permet de trouver la date du jour de cours de la classe
                while($date->format('N') != $jour){

                    $date -> modify('+1 day');
                    //var_dump($date);
                }

                // Cette boucle va permettre de parcours tout les jours de cours d'une classe pendant une année et va créer un evenement de chaque cours
                while($date <  $endDate){

                    $strDate = $date->format('Y-m-d');


                    

                        $temp=$cours_salle[$a]['cou_periode']-1;
                        $debut=$periode[$temp]['debutDePeriode'];
                        $fin=$periode[$temp]['finDePeriode'];
                       // echo $debut.': '.$fin.'<br>';
                        $events=([
                           'id' => $cours[$a]['id_cours'],
                           'resourceId' => 'a',
                           'start' => $strDate.'T'.$debut,
                           'end' => $strDate.'T'.$fin,
                           'title' => ''.$cours_salle[$a]['temp_prenomprof'],
                           'color'=> 'blue',
                           'textColor'=> 'black'
                        ]);
                        //print_r($events);    
                        array_push($complet, $events);

                        $date->modify('+1 week');
                        //var_dump($date);

                        $i++;
                    
                } 
         }
    }

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

    <title>CEPM Scan System V2.0</title> 
    

    <link rel="stylesheet" href="assets/css/calendar/fullcalendar.css" />
    <link rel="stylesheet" href="assets/css/calendar/fullcalendar.print.css" media="print"/>
    <link rel="stylesheet" href="assets/css/calendar/scheduler.css" />
    
    <script src="assets/js/calendar/moment.min.js"></script>
    <script src="assets/js/calendar/jquery.min1.js"></script>
    <script src="assets/js/calendar/fullcalendar.js"></script>
    <script src="assets/js/calendar/scheduler.js"></script>
    <script src="assets/js/calendar/locale-all.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
     
       
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
    
    <script>
       
    var events = <?php echo json_encode($complet); ?>
     	
    console.log(events);  	
	$(document).ready(function() {
		
		

	$(function() { // document ready

		$('#calendar').fullCalendar({
			defaultView: 'agendaWeek',
            weekends:false,
            allDaySlot: false,
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            minTime: '07:00:00',
            maxTime: '21:10:00',
            height: 700,
            defaultTimedEventDuration: '00:45:00',
            allDayDefault: false,
			lang: 'fr',
            locale:'fr',
			editable: false,
			selectable: true,
			eventLimit: true, // allow "more" link when too many events
			header: {
				left: 'prev,next today',
				center: 'title',
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

    </script>

 </head>
 
 <body>

     <div id="wrapper">

        <?php include("../include/menu.php"); ?>
        
            <div id="page-wrapper">
                <div class="row">
                    
                     <div class="col-lg-12">
                    <h1 class="page-header">Horaire COUCOU</h1>
                    </div>
                
                </div>
                 <div class="row">
                     <div class="col-lg-9 col-lg-offset-1 col-sm-12 col-sm-offset-0">
                        <form method="post" action="">
                             <div class="col-lg-2 col-lg-offset-2 col-sm-3 col-sm-offset-0">
                                 <select class="dropdown form-control" name="choixClasse" placeholder="Classe">
                                            <optgroup label="Classe">
                                            <option></option>
                                            <?php for($a=0;$a<count($classe);$a++)echo'<option value="'.$classe[$a]['id_classe'].'">'.$classe[$a]['cla_nom'].'</option>'; ?> 
                                 </select> 
                            </div>
                            <div class="col-lg-2 col-lg-offset-0 col-sm-3 col-sm-offset-0">
                                 <select class="dropdown form-control" name="choixProf">
                                            <optgroup label="Professeur">
                                            <option></option>
                                            <?php for($a=0;$a<count($professeur);$a++)echo'<option value="'.$professeur[$a]['id_professeur'].'">'.$professeur[$a]['pro_nom'].' '.$professeur[$a]['pro_prenom'].'</option>'; ?> 
                                 </select> 
                            </div>
                            <div class="col-lg-2 col-lg-offset-0 col-sm-3 col-sm-offset-0">
                                 <select class="dropdown form-control" name="choixSalle">
                                            <optgroup label="Salle de classe">
                                            <option></option>
                                            <?php for($a=0;$a<count($salle);$a++)echo'<option value="'.$salle[$a]['id_salle'].'">'.$salle[$a]['sal_nom'].'</option>'; ?> 
                                 </select> 
                            </div>
                            <div class="col-lg-2 col-lg-offset-0 col-sm-3 col-sm-offset-0">
                                <input type="submit" class="btn btn-info" value="envoyer">
                            </div>
                            
                            <div class="col-lg-2 col-lg-offset-0 col-sm-3 col-sm-offset-0">
                                <?php if($_SESSION['login']=="Fernandez"){?> 
                         
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal"> Ajuster les dates</button>             
                         
                                <?php } ?>
                            </div>
                         </form>
                         <br><br><br><br><br><br><br><br><br><br>
                        
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
  <script>
      $('#myModal').on('shown.bs.modal', function () {
          $('#myInput').focus()
      })
 </script>
 </body>
 
 </html>