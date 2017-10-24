<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : JOUDAR Salaheddine
// Date dernière modification   : 29.12.2016
// But    : Page administrateur permet la modification des informations concernant une classe.
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

//inclusion de la classe d'interaction avec la base de données

include '../include/bdd.php';
$maclasse = 0;
// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}
if((isset($_GET['idEleve'])) && (isset($_GET['idclasse']))) {

    $sql="update t_eleve set  idx_classe ='".$_GET['idclasse']."' where id_eleve ='".$_GET['idEleve']."'";
    // $sql=" insert into a values('".$_GET['idclasse']."')";
    $change=$bdd->prepare($sql);
    $change->execute();


}
$_SESSION['idclasse'] = $_GET['idclasse'];


if((isset($_GET['Intitule'])) && (isset($_GET['modifelev']))) {


    $sql="update t_classe set  cla_nom ='".$_GET['Intitule']."' where id_classe='".$_GET['modifelev']."'";
    $change=$bdd->prepare($sql);
    $change->execute();
}
else {
    $_GET['intitule'] = "";

}


$_SESSION['idclasse'] = $_GET['idclasse'];
IF (isset($_SESSION['idclasse'])){

    $maclasse = $_GET['idclasse'];
//    $requete=$bdd->prepare(" insert into a values('2222')");
     $requete=$bdd->prepare("SELECT * FROM t_classe where t_classe.id_classe='".$_SESSION['idclasse'] ."' ORDER BY cla_nom ASC");

    $requete->execute();
       $classe=$requete->fetchAll();

}

IF (isset($_GET['Intitule'])){

    $maclasse = $_GET['idclasse'];
//    $requete=$bdd->prepare(" insert into a values('2222')");
    $requete=$bdd->prepare("SELECT * FROM t_classe where t_classe.cla_nom='".$_GET['Intitule']."' ORDER BY cla_nom ASC");

    $requete->execute();
    $classe=$requete->fetchAll();
}




include_once('../include/mysql.inc.php');

//Va cherhcer les informations de l'élève
$bd=new dbIfc();
$tabStudent=$bd->GetStudentbyClasse($_GET['idclasse']);
unset($bd);


$alreadydone=false;





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
                <div class="col-lg-12">
                    <!-- Titre -->
                    <h1 class="page-header"><?php echo $classe[0] ['cla_nom']  ?></h1>
                    <!-- Fin  de : Titre -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">




                        <!-- Informations de base -->
                        <table class="table table-striped">
                            <form  method="get" action="?idclasse="<?php echo  $_GET['idclasse']?>"" >

                                    <tr>
                                        <td style="font-weight: bold"> Intitulé :</td>
                                        <td><input type="text" name="Intitule" value='<?php echo  $classe[0]['cla_nom'] ; ?>'/>
                                            <input type="hidden"  name="idclasse" value='<?php echo  $classe[0]['id_classe'] ; ?>'/>
                                            &nbsp;&nbsp;</td>
                                    </tr>

                                        </td>
                                    </tr>

                                
                        </table>

                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" name="modifelev" value="<?php echo  $_GET['idclasse']?>">
                           Modifier
                        </button>
                        </form>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajout_eleve" name="AssignEleve">
                            Assigner des élèves
                        </button>

                <br>

                    </div>
                </div>
            </div>






    <!-- Affiche les élèves -->
    <div  class='list-group-item'>
<?php
foreach($tabStudent as $entry)
{
    $ele_nom=$entry['ele_nom'];
    $ele_prenom=$entry['ele_prenom'];
    $ele_codebarre=$entry['id_codebarre'];
    //$cla_nom=$entry['cla_nom'];
    $id_eleve=$entry['id_eleve'];

    echo "<div class='list-group-item'>";

    //Affichage de la photo
    $filename = "images/utilisateurs/$ele_codebarre.jpg";
    $filename2 = "images/utilisateurs/$ele_codebarre.JPG";

    if (file_exists($filename)) {
        echo "<div class='list-group-item'>  <img class='student-thumbnail' width='35' alt='Alain Dupré' src='images/utilisateurs/$ele_codebarre.jpg'>";
    } else {
        if(file_exists($filename2))
        {
            echo " <img class='student-thumbnail' width='35' alt='Alain Dupré' src='images/utilisateurs/$ele_codebarre.JPG'>";
        }
        else
        {
            echo " <img class='student-thumbnail' width='35' alt='Alain Dupré' src='images/utilisateurs/usermale.png'>";
        }
    }

        echo "

                                                            <a href='student_dtl.php?stu=" . $ele_codebarre . "'>$ele_nom $ele_prenom</a> </div>";
    }




?>
















        <!-- ////////////////////////////////////////////Assigner un élève à une Classe/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

           <div class="modal fade" id="ajout_eleve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
             <div class="modal-dialog" role="document">
               <div class="modal-content">
                 <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                   </button>
                   <h4 class="modal-title" id="myModalLabel">assigner un élève</h4>
                 </div>
                 <div class="modal-body">
                   <table class="table table-striped">
                       <form method="post" action="#">
                           <tr>
                               <form name="rep" method="post" action="#">

                                   <input type="text" id="Eleve_id"  class="form-control" placeholder="Search..." onkeyup="autocomplet()">

                                   <ul id="Eleve_list_id"></ul>
                                   <div id ='moi'> </div>

                               <td><input type="submit" class="btn btn-primary btn-sm" value="Ajouter"></td>
                                   <input type="hidden" id="tidclasse" value =" <?php echo $_GET['idclasse'] ?>"
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


        <!-- /#page-wrapper -->

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

    <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js" async></script>
        <script type="text/javascript" src="../js/script_eleve.js"></script>

</body>

</html>
