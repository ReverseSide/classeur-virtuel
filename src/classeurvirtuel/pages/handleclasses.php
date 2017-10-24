<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Création   : 23.05.2016
// But    : Page de gestion des classes de son profil
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']) )
{
    header("Location:login.php");
}

//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');



//Si formulaire exécuté, Insertion d'une nouvelle classe
if(!empty($_POST['selectclass']))
{


    $bd=new dbIfc();
    $tabdepartment=$bd->InsertNewClass($_POST['selectclass']);
    unset($bd);
    unset($_POST['selectclass']);

}

//Si click sur "Supprimer", suppression de la classe en question
if(!empty($_GET['del']))
{


    $bd=new dbIfc();
   $blndel=$bd->DeleteMyClass($_GET['del']);
    unset($bd);
}



$bd=new dbIfc();

//Création du tableau contenant les départements
$tabdepartment=$bd->GetDepartment();

//Création du tableau contenant ses classes
$tabmyclass=$bd->GetMyClasses();
unset($bd);

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
                    <h1 class="page-header">Gestion de mes classes</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


            <!-- /.row -->
            <div class="row">
                <div class="col-xs-12">

                    <div id="timeline-1">
                        <!-- Affichage de ses classes -->
                        <ul class="list-group">
                            <?php

                            //Check si le mode combinaison est activé
                            $combination=false;
                            if(!empty($_GET['comb']))
                            {
                                $idcomb=$_GET['comb'];
                                $combination=true;
                            }


                                //Affichage de la liste de ses classes
                                foreach($tabmyclass as $entry)
                                {
                                    $nom=$entry['cla_nom'];
                                    $id=$entry['id_maclasse'];

                                    if($combination==true && $entry['id_maclasse']==$idcomb)
                                    {




                                        echo "<li class='list-group-item' style='background-color: #bcc9d5'>
                                        <a href='handleclasses.php?del=$id' class='btn btn-danger' style='float:right; padding:1px 7px;' role='button'>Supprimer</a>
                                        <a href='handleclasses.php' class='btn btn-warning' style='float:right; padding:1px 7px; margin-right: 20px;' role='button'>Combiner</a>
                                        $nom
                                        </li>";
                                    }
                                    else
                                    {


                                        echo "<li class='list-group-item'>
                                        <a href='handleclasses.php?del=$id' class='btn btn-danger' style='float:right; padding:1px 7px;' role='button'>Supprimer</a>
                                        <a href='handleclasses.php?comb=$id' class='btn btn-warning' style='float:right; padding:1px 7px; margin-right: 20px;' role='button'>Combiner</a>
                                        $nom
                                        </li>";
                                    }

                                }

                            ?>
                        </ul>
                        <!--  Fin de : Affichage de ses classes -->

                    </div>
                    <!-- PAGE CONTENT ENDS -->
                </div><!-- /.col -->
            </div><!-- /.row -->


            <!-- Formulaire d'ajout d'une classe -->
            <form class="form-horizontal" method="POST" name="formulaire">
                <fieldset>


                    <legend>Ajouter une classe</legend>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="col-md-1 control-label" for="selectbasic">Département</label>
                        <div class="col-md-2">
                            <select id="selectdepartement" name="selectdepartement" class="form-control" onchange="this.form.submit()">
                                <?php
                                    //Création de la liste déroulante contenant les départements
                                    foreach($tabdepartment as $entry)
                                    {
                                        $blnalreadydone=false;
                                        $nom=$entry['dep_nom'];
                                        $id=$entry['id_departement'];
                                        if(!empty($_POST['selectdepartement']))
                                        {
                                            if($_POST['selectdepartement']==$id)
                                            {
                                                echo "<option value='$id' selected='selected'>$nom</option>";
                                            }
                                            else
                                            {
                                                echo "<option value='$id'>$nom</option>";
                                            }
                                            $blnalreadydone=true;
                                        }
                                        if($blnalreadydone==false)
                                        {
                                            echo "<option value='$id'>$nom</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
            </form><form class="form-horizontal" method="POST" name="classe">
                        <label class="col-md-1 control-label" for="selectbasic">Classe</label>
                        <div class="col-md-2">
                            <select id="selectbasic" name="selectclass" class="form-control">
                                <?php
                                //Création de la liste déroulante contenant les classes selon le département choisi
                                if(!empty($_POST['selectdepartement']))
                                    {
                                        $bd=new dbIfc();
                                        $tabdepartment=$bd->GetClassDepartment($_POST['selectdepartement']);
                                        unset($bd);
                                        foreach($tabdepartment as $entry)
                                        {
                                            $nom=$entry['cla_nom'];
                                            $id=$entry['id_classe'];
                                            echo "<option value='$id'>$nom</option>";
                                        }
                                    }
                                    else
                                    {
                                        //Par défaut le département Conception du bâtiment est choisi
                                        $bd = new dbIfc();
                                        $tabdepartment = $bd->GetClassDepartment(1);
                                        unset($bd);
                                        foreach ($tabdepartment as $entry) {
                                            $nom = $entry['cla_nom'];
                                            $id = $entry['id_classe'];
                                            echo "<option value='$id'>$nom</option>";
                                        }
                                    }




                                ?>

                            </select>

                        </div>

                        <div class="col-md-2">
                            <button value="submit" id="singlebutton" name="singlebutton" class="btn btn-success">Ajouter</button>
                        </div>
                    </div>



                </fieldset>
            </form>
            <!-- Fin de : Formulaire d'ajout d'une classe -->

        <br/><br/><br/><br/><br/>

            <p style="text-align: right"><a class="btn btn-primary" href="management.php" role="button">Gestion des absences</a></p>


                    <!-- /.row -->
        </div>
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

</body>

</html>
