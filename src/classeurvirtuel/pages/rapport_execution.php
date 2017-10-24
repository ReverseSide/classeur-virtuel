
<?php

session_start();
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

    include '../include/bdd.php';
    $dir    = '../rapport/';
    $files1 = scandir($dir);
    foreach ($files1 as $key => $value)
       {
          if (!in_array($value,array(".","..")))
          {
             if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
             {
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
             }
             else
             {
                $result[] = $value;
             }
          }
       }

$fichier=@$_POST['choixListe'];
$add = array();
$set = array();
$disabled = array();

$select = "../rapport/".$fichier."";
$fp = fopen($select, 'r');

while (!feof($fp)) {
/*Tant qu'on n'atteint pas la fin du fichier on lit une ligne*/
$ligne = fgets($fp,4096);
/*On récupère les champs séparés par , dans liste*/
$liste = explode(',',$ligne);
/*On assigne les variables*/
$variable2 = $liste[4];
$variable2 = str_replace('"', '', $variable2);
$liste= str_replace('"', '', $liste);


    if($variable2==1){
        $add[]= $liste;
    }
    if($variable2==3){
        $disabled[]= $liste;
    }
    if($variable2==2){
        $set[]= $liste;
    }
}

//print_r($disabled);
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

                     <div class="col-lg-12">
                    <h1 class="page-header">Rapport d'execution syncronisation de l'Active Directory</h1>
                    </div>

                </div>
                 <div class="row">
                    <div class="col-lg-4 col-lg-offset-0 col-sm-12 col-sm-offset-0" name="recherche">
                        <div class="col-lg-5 col-lg-offset-0 col-sm-12 col-sm-offset-0">
                        <form method="POST" action="" name="choose">
                          <select class="dropdown form-control" name="choixListe">
                                        <option></option>
                                        <?php foreach($result as $file )echo'<option value="'.$file.'">'.$file.'</option>'; ?>
                          </select>
                        </div>
                        <div class="col-lg-1 col-lg-offset-0 col-sm-12 col-sm-offset-0">
                          <input class="btn btn-default" type="submit" name="rechercher" value="rechercher" class="boutonRecherche" >
                        </div>
                      </form>
                    </div>
                    <?php if(empty($fichier)){echo "Choissisez un rapport";}else{ ?>
                    <div class="col-lg-12 col-lg-offset-0 col-sm-12 col-sm-offset-0"><br><br></div>
                    <div class="col-lg-3 col-lg-offset-0.5 col-sm-12 col-sm-offset-0" name="1" >
                      <center><h3 class="page-header">Utilisateur ajouté</h3></center><br><br>
                      <?php
                            foreach($add as $student){
                                     ?>
                            <Table  cellpadding="10" cellspacing="1" width="100%">
                               <TR><?php
                               echo"<td align='center' class='col-md-4'>".$student[0]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[1]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[2]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[3]."</td><BR><br>"; ?>
                               </TR>
                            </Table>
                            <?php
                            }
                        ?>
                    </div>
                    <div class="col-lg-3 col-lg-offset-1 col-sm-12 col-sm-offset-0" name="2" >
                        
                      <center><h3 class="page-header">Utilisateur modifié</h3></center><br><br>
                      
                      <?php
                            foreach($set as $student){
                                ?>
                            <Table  cellpadding="10" cellspacing="1" width="100%">
                               <TR><?php
                               echo"<td align='center' class='col-md-4'>".$student[0]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[1]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[2]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[3]."</td><BR><br>"; ?>
                               </TR>
                            </Table>
                            <?php
                                
                            }
                       ?>
                        
                    </div>
                    <div class="col-lg-3 col-lg-offset-1 col-sm-12 col-sm-offset-0" name="3">
                      <center><h3 class="page-header">Utilisateur désactivé</h3></center><br><br>
                      <?php
                           foreach($disabled as $student){
                                    ?>
                            <Table  cellpadding="10" cellspacing="1" width="100%">
                               <TR><?php
                               echo"<td align='center' class='col-md-4'>".$student[0]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[1]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[2]."</td>";
                               echo"<td align='center' class='col-md-4'>".$student[3]."</td><BR><br>"; ?>
                               </TR>
                            </Table>
                            <?php
                           }
                       ?>
                    </div>
                    <?php } ?>
                    
                </div>

        </div>
    </div>
</body>

</html>
