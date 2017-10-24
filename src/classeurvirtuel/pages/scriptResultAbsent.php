<?php
session_start();
/*if (empty($_SESSION['user_id'])) {
    header("Location:login.php");
}
*/
//Password
$login = 'Admin'; 
$pass = '123415'; 

include '../include/bdd.php';

//day u want data
$day ="lundi";



if(($_SERVER['PHP_AUTH_PW']!= $pass || $_SERVER['PHP_AUTH_USER'] != $login)|| !$_SERVER['PHP_AUTH_USER']) 
{ 
    header('WWW-Authenticate: Basic realm="Test auth"'); 
    header('HTTP/1.0 401 Unauthorized'); 
    echo 'Auth failed'; 
    exit; 
}
//Password end

    $sql2 = 'SELECT t_eleve.*, t_sportete.*, t_classe.* FROM t_eleve, t_sportete, t_classe WHERE (t_eleve.idx_classe = t_classe.id_classe) AND (t_eleve.id_eleve = t_sportete.id_eleve) AND (cla_joursSport="'.$day.'") AND (choixSport!="") AND (choixSport!="Entreprise") AND (arrivee="0000-00-00 00:00:00") AND (depart="0000-00-00 00:00:00") ORDER BY t_sportete.arrivee DESC';
    $requete2 = $bdd->prepare($sql2);
    $requete2->execute();
    $eleve_car = $requete2->fetchAll();


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
    
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    
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

                                 <table id="table_id_car" class="display">
                                <thead>
                                <?php if(count($eleve_car)<=1){ ?>
                                    <h4><?php echo count($eleve_car).' '; ?>élève </h4><?php }else{?> <h4><?php echo count($eleve_car).' '; ?>élèves présent</h4><?php }?>
                                <tr>
                                    <th>Elève</th>
                                    <th>Choix d'activité</th>
                                    <th>Nom classe</th>     
									<th>Heure d'arrivée</th>
                                    <th>numéro de téléphone</th>
                                    <th>Codebarre</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                for ($a = 0; $a < count($eleve_car); $a++) { ?>

                                    <tr>
                                        <td>


                                            <?php
                                            //trouve la photo de l'utilisateur dans le dossier images/utilisateurs et l'afficher
                                            $id_codebarre = $eleve_car[$a]['id_codebarre'];
                                            $filename = "images/utilisateurs/$id_codebarre.jpg";
                                            $filename2 = "images/utilisateurs/$id_codebarre.JPG";


                                            if (file_exists($filename)) {
                                                echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.jpg' width='108' height='144'>";
                                            } else {
                                                if (file_exists($filename2)) {
                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/$id_codebarre.JPG' width='108' height='144'>";

                                                } else {
                                                    echo "<img alt='Alain Dupré' src='images/utilisateurs/usermale.png' width='108' height='144'>";
                                                }

                                            }

                                            ?>
                                            <?php echo $eleve_car[$a]['ele_prenom'] . " " . $eleve_car[$a]['ele_nom']; ?>
                                        </td>
                                        <td><?php echo $eleve_car[$a]['choixSport']; ?></td>
                                        <td><?php echo $eleve_car[$a]['cla_nom']; ?></td>
										<td><?php echo $eleve_car[$a]['arrivee']; ?></td>
                                        <td><?php echo $eleve_car[$a]['ele_numeromobile']; ?></td>
                                        <td><?php echo $eleve_car[$a]['id_codebarre']; ?></td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>



<script>

    $(document).ready(function () {
        //<body onload="document.forms['scan_form'].elements['scan'].focus()">
         $('#table_id_car').dataTable({
                        
                        paging: false,
                        bFilter: false,
                        "bSort" : false,
                        //"aaSorting" : [[]],
                        dom: 'Bfrtip',
                        buttons: [
                                    {
                                        text: 'Imprimer',
                                        extend: 'print',
                                    }
                                ]
         });      
   
        $('#table_id').dataTable({
            paging: false,
            
        });

        $('#scan').focus();
        
         var page=document.getElementById('page').value;
            
                //document.write(page);
        
                if(page=="scanCheck"){
                    $('.nav-tabs a[href="#table_id_car"]').tab('show');
                }
        
                if(page=="nonScan"){
                    $('.nav-tabs a[href="#presence"]').tab('show');
                }
            
                if(page=="modif"){
                    $('.nav-tabs a[href="#modification"]').tab('show');
                }
    });
    
    

</script>
</body>

</html>