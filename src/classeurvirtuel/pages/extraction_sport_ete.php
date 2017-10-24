<?php
session_start();
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

include '../include/bdd.php';

    $day="Lundi";
    if(isset($_POST['jour'])){
        $day=$_POST['jour'];
    }

    $sql='SELECT choixSport FROM t_sportete';
	$requete=$bdd->prepare($sql);
	$requete->execute();
    $donnees=$requete->fetchAll();
	

    $sql1='SELECT id_eleve FROM t_eleve';
	$requete1=$bdd->prepare($sql1);
	$requete1->execute();
    $donnees1=$requete1->fetchAll();

    $sql2='SELECT * FROM t_sportete, t_classe, t_eleve WHERE (t_classe.cla_joursSport ="'.$day.'" ) AND (t_eleve.idx_classe=t_classe.id_classe) AND (t_sportete.id_eleve=t_eleve.id_eleve); ';
    $requete2=$bdd->prepare($sql2);
	$requete2->execute();
    $lieux=$requete2->fetchAll();

	$sql3='SELECT id_eleve FROM t_eleve WHERE t_eleve.idx_classe IN (select id_classe from t_classe where cla_type="Terminales")';
	$requete3=$bdd->prepare($sql3);
	$requete3->execute();
    $Terminales=$requete3->fetchAll();
	
	
    $sql4='SELECT * FROM t_sportete where choixSport="entreprise"';
	$requete4=$bdd->prepare($sql4);
	$requete4->execute();
    $donnees4=$requete4->fetchAll();
    
    $sql5='SELECT * FROM t_sportete where choixSport!="entreprise" OR choixSport!=NULL';
	$requete5=$bdd->prepare($sql5);
	$requete5->execute();
    $donnees5=$requete5->fetchAll();
	

    $pasInscrit=0;
	$foot=0;
	$beachvolley=0;
	$unihockey=0;
	$danse=0;
	$entreprise=0;
    $eleveTotal=0;
	
    for($a=0;$a<count($donnees);$a++){
		switch($donnees[$a][0]){
			case 'Football':
				$foot++;
				break;
			case 'Beach-volley':
				$beachvolley++;
				break;
			case 'Unihockey':
				$unihockey++;
				break;
			case 'Danse':
				$danse++;
				break;
			case 'Entreprise':
				$entreprise++;
				break;
            default:
                $pasInscrit++;
                break;
			
		}
	}
	
			/*print_r($foot); echo " ";
			print_r($beachvolley); echo " ";
			print_r($unihockey); echo " ";
			print_r($entreprise); echo " ";
			print_r($pasInscrit); echo " ";*/


    for($a=0;$a<count($donnees1);$a++){
        
        $eleveTotal++;
    }
	
	/*echo $pasInscrit . " - " . count($Terminales) . " = " ;*/
	
	//retire les classe en terminale du total 
	$pasInscrit=$pasInscrit-count($Terminales);
	
	/*echo $pasInscrit;*/
	
    $n_entreprise=count($donnees4);
    $n_participe=count($donnees5);
    $inscrit=$eleveTotal-$pasInscrit;
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
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>  
    <link rel="stylesheet" href="assets/css/inscription.css">
    <script src="../js/Chart.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css"> 
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>

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
                    <h1 class="page-header">Extraction des données de la semaine sportive d'hiver</h1>
                    </div>
                
                </div>
                <div class="row">
                    
                    <div class="col-lg-4 col-lg-offset-1 col-sm-12 col-sm-offset-0">
                        <canvas id="pie"/>
                    </div>
                    <div class="col-lg-4 col-lg-offset-1 col-sm-12 col-sm-offset-0">  
                        <canvas id="doughnut"/>
                    </div>
                    <div class="col-lg-12 col-lg-offset-0 col-sm-12 col-sm-offset-0"><br></div>

                    <div class="col-lg-4 col-lg-offset-1 col-sm-12 col-sm-offset-0" style="margin-top:10%;" >
                        
                        <button class="btn btn-app btn-grey btn-sm" onclick="location.href='function_extraction_ete.php';">
										<i class="ace-icon fa fa-floppy-o bigger-200"></i>
										Exporter
						</button>
                       
                        

                    </div>
                
                </div>
        </div>
    </div>
    
     <script>
                                        var dataPie = {
                                                            type: 'pie',
                                                            data: {
                                                                datasets: [{
                                                                    data: [
                                                                        <?php echo $foot.",".$beachvolley.",".$unihockey.",".$danse.",".$entreprise.",".$pasInscrit."" ?>
                                                                        
                                                                    ],
                                                                    backgroundColor: [
                                                                        "#F7464A",
                                                                        "#46BFBD",
                                                                        "#FDB45C",
                                                                        "#4D5360",
                                                                        "#949FB1",
																		"#000000"
                                                                    ],
                                                                }],
                                                                labels: [
                                                                    "Football",
                                                                    "Beach-volley",
                                                                    "Unihockey",
                                                                    "Danse",
																	"Entreprise",
                                                                    "Pas inscrit"
                                                                    
                                                                ]
                                                            },
                                                                options: {
                                                                responsive: true,
                                                                pointPadding: 0.2
                                                            }
                                                        };
                                                             
                                          var dataDoughnut = {
                                                            type: 'doughnut',
                                                            data: {
                                                                datasets: [{
                                                                    data: [
                                                                        <?php echo $n_participe.",".$pasInscrit.",".$n_entreprise."" ?>
                                                                    ],
                                                                    backgroundColor: [
                                                                        "#F7464A",
                                                                        "#46BFBD",
                                                                        "#FDB45C"
                                                                       
                                                                    ],
                                                                }],
                                                                labels: [
                                                                    "Participe",
                                                                    "Pas inscrit",
                                                                    "Entreprise"
                                                                    
                                                                ]
                                                            },
                                                                options: {
                                                                responsive: true
                                                            }
                                                        };
         
                                            /*var dataBar = {
                                                            type: 'bar',
                                                            data: {
                                                                datasets: [{
                                                                    label: "Départ par gare (<?php echo $day; ?>)",
                                                                    data: [
                                                                        <?php echo $lausanne.",".$aigle.",".$leysin."" ?>
                                                                    ],
                                                                    backgroundColor: [
                                                                        "#F7464A",
                                                                        "#46BFBD",
                                                                        "#FDB45C"
                                                                       
                                                                    ],
                                                                }],
                                                                labels: [
                                                                    "Lausanne",
                                                                    "Aigle",
                                                                    "Leysin"
                                                                    
                                                                ]
                                                            },
                                                                options: {
                                                                responsive: true,
                                                                scales: {
                                                                            xAxes: [{ barPercentage: 0.5 }]
                                                                        }
                                                            }
                                                        };
                                        
                                            var dataBar1 = {
                                                            type: 'bar',
                                                            data: {
                                                                datasets: [{
                                                                    label: "Besoin de materiel par sport (<?php echo $day; ?>)",
                                                                    data: [
                                                                        <?php echo $m_ski.",".$m_snow.",".$m_raquette."" ?>
                                                                    ],
                                                                    backgroundColor: [
                                                                        "#F7464A",
                                                                        "#46BFBD",
                                                                        "#FDB45C"
                                                                       
                                                                    ],
                                                                }],
                                                                labels: [
                                                                    "ski",
                                                                    "snow",
                                                                    "raquette"
                                                                    
                                                                ]
                                                            },
                                                                options: {
                                                                responsive: true,
                                                                scales: {
                                                                            xAxes: [{ barPercentage: 0.5 }]
                                                                        }
                                                                
                                                                
                                                            }
                                                        };
*/

                                                        window.onload = function() {
                                                            var ctx1 = document.getElementById("doughnut").getContext("2d");
                                                            window.myDoughnut = new Chart(ctx1, dataDoughnut);
                                                           
                                                            var xtc = document.getElementById("pie").getContext("2d");
                                                            window.myPie = new Chart(xtc, dataPie);
                                                        /*    
                                                            var tcx = document.getElementById("three").getContext("2d");
                                                            window.myBar = new Chart(tcx, dataBar);
                                                            
                                                            var asa = document.getElementById("bar").getContext("2d");
                                                            window.myBar = new Chart(asa, dataBar1);
                                                            */
                                                        };
                                    </script>
                    
    
    </body>
    
</html>
                