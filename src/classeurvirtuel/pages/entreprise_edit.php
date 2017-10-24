<?php
session_start();
/**
 * Created by PhpStorm.
 * User:
 * Date: 12.09.2017
 * Time: 14:48
 */

//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
include_once('../include/bdd.php');

// Check si l'utilisateur est connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}


$bd=new dbIfc();
$Entreprise = $bd->GetEntreprise($_GET['id_entreprise']);
$MaitreDappArray = $bd->GetEntrepriseMaitre($_GET['id_entreprise']);

if ( isset($_GET['edit_entreprise']) and $_GET['edit_entreprise']=='Yes'){
    $ent="UPDATE t_entreprise SET
            ent_nom = :ent_nom, 
            ent_rue = :ent_rue, 
            ent_npa = :ent_npa, 
            ent_localite = :ent_localite, 
            ent_canton = :ent_canton, 
            ent_tel1 = :ent_tell, 
            ent_tel2 = :ent_tel2, 
            ent_mobile1= :ent_mobile1,
            ent_mobile2= :ent_mobile2,
            ent_mail = :ent_mail,
            ent_mail_2 = :ent_mail_2,
            ent_fax = :ent_fax
            WHERE id_entreprise= :id_entreprise";


    $params_ent = array(
        ':ent_nom' => $_POST['ent_nom'] ,
        ':ent_rue' => $_POST['ent_rue'] ,
        ':ent_npa' => $_POST['ent_npa'],
        ':ent_localite' => $_POST['ent_localite'] ,
        ':ent_canton' => $_POST['ent_canton'] ,
        ':ent_tell' => $_POST['ent_tel1'] ,
        ':ent_tel2' => $_POST['ent_tel2'] ,
        ':ent_mobile1' => $_POST['ent_mobile1'],
        ':ent_mobile2' => $_POST['ent_mobile2'],
        ':ent_mail' => $_POST['ent_mail'] ,
        ':ent_mail_2' => $_POST['ent_mail_2'] ,
        ':ent_fax' => $_POST['ent_fax'],
        ':id_entreprise' => $_GET['id_entreprise'] );


    try {
        $req_ent=$bdd->prepare($ent);
        $req_ent->execute($params_ent);
    }
    catch(Exception $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }
    $_SESSION["message"] = "L'entreprise ".$_POST['ent_nom']." a été mis à jour.";
    header("Refresh:0; url='entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."'");
    exit;
}

if ( isset($_GET['del_maitreapp']) and $_GET['del_maitreapp']=='Yes'){
    $mai="UPDATE t_maitredapprentissage SET mai_deactivate=1 WHERE id_maitredapprentissage= :id_maitredapprentissage";
    $params_mai = array(':id_maitredapprentissage' => $_GET['id_maitre']);
    try {
        $req_mai=$bdd->prepare($mai);
        $req_mai->execute($params_mai);
    }
    catch(Exception $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }
    $_SESSION["message"] = "Le maître d'apprentissage ".$_POST['mai_prenom']." ".$_POST['mai_nom']." a été supprimé.";
    header("Refresh:0; url='entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."'");
    exit;
}


if ( isset($_GET['edit_maitreapp']) and $_GET['edit_maitreapp']=='Yes'){

    $ent="UPDATE t_maitredapprentissage SET
            mai_nom = :mai_nom, 
            mai_prenom = :mai_prenom, 
            mai_politesse = :mai_politesse, 
            mai_tel1 = :mai_tel1, 
            mai_tel2 = :mai_tel2, 
            mai_mobile = :mai_mobile, 
            mai_mail = :mai_mail, 
            mai_mail2= :mai_mail2
            WHERE id_maitredapprentissage= :id_maitredapprentissage";

    $params_ent = array(
        ':mai_nom' => $_POST['mai_nom'] ,
        ':mai_prenom' => $_POST['mai_prenom'] ,
        ':mai_politesse' => $_POST['mai_politesse'],
        ':mai_tel1' => $_POST['mai_tel1'] ,
        ':mai_tel2' => $_POST['mai_tel2'] ,
        ':mai_mobile' => $_POST['mai_mobile'] ,
        ':mai_mail' => $_POST['mai_mail'] ,
        ':mai_mail2' => $_POST['mai_mail2'],
        ':id_maitredapprentissage' => $_GET['id_maitre'] );


    try {
        $req_ent=$bdd->prepare($ent);
        $req_ent->execute($params_ent);
    }
    catch(Exception $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }
    $_SESSION["message"] = "Le maître d'apprentissage ".$_POST['mai_prenom']." ".$_POST['mai_nom']." a été mis à jour.";
    header("Refresh:0; url='entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."'");
    exit;
}

if ( isset($_GET['new_maitreapp']) and $_GET['new_maitreapp']=='Yes'){
    $ent="INSERT INTO t_maitredapprentissage SET
            mai_nom = :mai_nom, 
            mai_prenom = :mai_prenom, 
            mai_politesse = :mai_politesse, 
            mai_tel1 = :mai_tel1, 
            mai_tel2 = :mai_tel2, 
            mai_mobile = :mai_mobile, 
            mai_mail = :mai_mail, 
            mai_mail2= :mai_mail2,
            idx_entreprise= :idx_entreprise";

    $params_ent = array(
        ':mai_nom' => $_POST['mai_nom'] ,
        ':mai_prenom' => $_POST['mai_prenom'] ,
        ':mai_politesse' => $_POST['mai_politesse'],
        ':mai_tel1' => $_POST['mai_tel1'] ,
        ':mai_tel2' => $_POST['mai_tel2'] ,
        ':mai_mobile' => $_POST['mai_mobile'] ,
        ':mai_mail' => $_POST['mai_mail'] ,
        ':mai_mail2' => $_POST['mai_mail2'],
        ':idx_entreprise' => $_GET['id_entreprise'] );


    try {
        $req_ent=$bdd->prepare($ent);
        $req_ent->execute($params_ent);
    }
    catch(Exception $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }
    $_SESSION["message"] = "Le maître d'apprentissage ".$_POST['mai_prenom']." ".$_POST['mai_nom']." a été créé.";
    header("Refresh:0; url='entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."'");
    exit;
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
                <?php if (isset($_SESSION["message"])) {
                    echo '<br><div class="alert alert-success" >
                            <strong > Réussi ! </strong >'. $_SESSION["message"] .'
                        </div>';
                    unset($_SESSION["message"]);
                } ?>
                <h1 class="page-header">Entreprise</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <form class="form-horizontal" role="form" method="post" action=<?php echo "entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."&edit_entreprise=Yes" ; ?> >
                <fieldset>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Nom</label>
                        <div class="col-sm-4">
                            <input id="ent_nom" name="ent_nom" type="text" value="<?php echo $Entreprise[0][ent_nom] ; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Rue</label>
                        <div class="col-sm-4">
                            <input id="ent_rue" name="ent_rue" type="text" value="<?php echo $Entreprise[0][ent_rue]; ?>" class="form-control">
                        </div>
                    </div>
                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Localité</label>
                        <div class="col-sm-4">
                            <input id="ent_localite" name="ent_localite" type="text" placeholder="<?php echo $Entreprise[0][ent_localite]; ?>" value="<?php echo $Entreprise[0][ent_localite]; ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">NPA</label>
                        <div class="col-sm-4">
                            <input id="ent_npa" name="ent_npa" type="text" placeholder="<?php echo $Entreprise[0][ent_npa]; ?>" value="<?php echo $Entreprise[0][ent_npa]; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Canton</label>
                        <div class="col-sm-4">
                            <input id="ent_canton" name="ent_canton" type="text" placeholder="<?php echo $Entreprise[0][ent_canton]; ?>" value="<?php echo $Entreprise[0][ent_canton]; ?>" class="form-control">
                        </div>
                    </div>
                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Tel. n°1</label>
                        <div class="col-sm-4">
                            <input id="ent_tel1" name="ent_tel1" type="text" placeholder="<?php echo $Entreprise[0][ent_tel1]; ?>" value="<?php echo $Entreprise[0][ent_tel1]; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Tel. n°2</label>
                        <div class="col-sm-4">
                            <input id="ent_tel2" name="ent_tel2" type="text" placeholder="<?php echo $Entreprise[0][ent_tel2]; ?>" value="<?php echo $Entreprise[0][ent_tel2]; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Mobile. n°1</label>
                        <div class="col-sm-4">
                            <input id="ent_mobile1" name="ent_mobile1" type="text" placeholder="<?php echo $Entreprise[0][ent_mobile1]; ?>" value="<?php echo $Entreprise[0][ent_mobile1]; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Mobile. n°2</label>
                        <div class="col-sm-4">
                            <input id="ent_mobile2" name="ent_mobile2" type="text" placeholder="<?php echo $Entreprise[0][ent_mobile2]; ?>" value="<?php echo $Entreprise[0][ent_mobile2]; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Mail 1</label>
                        <div class="col-sm-4">
                            <input id="ent_mail" name="ent_mail" type="text" placeholder="<?php echo $Entreprise[0][ent_mail]; ?>" value="<?php echo $Entreprise[0][ent_mail]; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Mail 2</label>
                        <div class="col-sm-4">
                            <input id="ent_mail_2" name="ent_mail_2" type="text" placeholder="<?php echo $Entreprise[0][ent_mail_2]; ?>" value="<?php echo $Entreprise[0][ent_mail_2]; ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Fax</label>
                        <div class="col-sm-4">
                            <input id="ent_fax" name="ent_fax" type="text" placeholder="<?php echo $Entreprise[0][ent_fax]; ?>" value="<?php echo $Entreprise[0][ent_fax]; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-default">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Maître d'apprentisage lié à l'entreprise</h1>
            </div>
            <!-- /.col-lg-12 -->
            <p style="text-align: right">
                <a class="btn btn-default" href="#" role="button" data-toggle="modal" data-target="#myModal">Ajouter un maître d'apprentissage</a>
            </p>
        </div>
        <?php foreach($MaitreDappArray as $key => $MaitreDapp) { ?>
        <div class="row">
            <?php if ($key > 0) { echo "<h1 style='text-align:center'> --- </h1>"; } ?>
            <form class="form-horizontal" role="form" method="post" action=<?php echo "entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."&id_maitre=".$MaitreDapp[id_maitredapprentissage]."&edit_maitreapp=Yes" ; ?> >
                <fieldset>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Politesse</label>
                        <div class="col-sm-4">
                            <input id="mai_politesse" name="mai_politesse" type="text" value="<?php echo $MaitreDapp[mai_politesse] ; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Nom</label>
                        <div class="col-sm-4">
                            <input id="mai_nom" name="mai_nom" type="text" value="<?php echo $MaitreDapp[mai_nom] ; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Prenom</label>
                        <div class="col-sm-4">
                            <input id="mai_prenom" name="mai_prenom" type="text" value="<?php echo $MaitreDapp[mai_prenom]; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Tel. n°1</label>
                        <div class="col-sm-4">
                            <input id="mai_tel1" name="mai_tel1" type="text" value="<?php echo $MaitreDapp[mai_tel1] ; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Tel. n°2</label>
                        <div class="col-sm-4">
                            <input id="mai_tel2" name="mai_tel2" type="text" value="<?php echo $MaitreDapp[mai_tel2]; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Tel. Mobile</label>
                        <div class="col-sm-4">
                            <input id="mai_mobile" name="mai_mobile" type="text" value="<?php echo $MaitreDapp[mai_mobile] ; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="textinput">Mail </label>
                        <div class="col-sm-4">
                            <input id="mai_mail" name="mai_mail" type="text" value="<?php echo $MaitreDapp[mai_mail] ; ?>" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label" for="textinput">Mail 2</label>
                        <div class="col-sm-4">
                            <input id="mai_mail2" name="mai_mail2" type="text" value="<?php echo $MaitreDapp[mai_mail2] ; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="pull-right">
                                <a class="btn btn-default">Annuler</a>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="<?php echo "entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."&id_maitre=".$MaitreDapp[id_maitredapprentissage]."&del_maitreapp=Yes";?>" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i> Supprimer</a>

                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.row -->
        <!-- /.row -->
        <?php } ?>
        <p style="text-align: left">
            <a class="btn btn-default" href="#" role="button" data-toggle="modal" data-target="#myModal">Ajouter un maître d'apprentissage</a>
        </p>
    </div>
    <!-- Modal Création maitre d'apprentissage-->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Création d'un nouveau maître d'apprentissage</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" method="post" action=<?php echo "entreprise_edit.php?id_entreprise=".$_GET['id_entreprise']."&new_maitreapp=Yes" ; ?> >
                        <fieldset>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="textinput">Politesse</label>
                                <div class="col-sm-4">
                                    <input id="mai_politesse" name="mai_politesse" type="text" placeholder="Politesse" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="textinput">Nom</label>
                                <div class="col-sm-4">
                                    <input id="mai_nom" name="mai_nom" type="text" placeholder="Nom" class="form-control">
                                </div>
                                <label class="col-sm-2 control-label" for="textinput">Prenom</label>
                                <div class="col-sm-4">
                                    <input id="mai_prenom" name="mai_prenom" type="text" placeholder="Prenom" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="textinput">Tel. n°1</label>
                                <div class="col-sm-4">
                                    <input id="mai_tel1" name="mai_tel1" type="text" placeholder="Tel. n°1" class="form-control">
                                </div>
                                <label class="col-sm-2 control-label" for="textinput">Tel. n°2</label>
                                <div class="col-sm-4">
                                    <input id="mai_tel2" name="mai_tel2" type="text" placeholder="Tel. n°2" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="textinput">Tel. Mobile</label>
                                <div class="col-sm-4">
                                    <input id="mai_mobile" name="mai_mobile" type="text" placeholder="Tel. Mobile" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="textinput">Mail </label>
                                <div class="col-sm-4">
                                    <input id="mai_mail" name="mai_mail" type="text" placeholder="Mail 1" class="form-control">
                                </div>
                                <label class="col-sm-2 control-label" for="textinput">Mail 2</label>
                                <div class="col-sm-4">
                                    <input id="mai_mail2" name="mai_mail2" type="text" placeholder="Mail 2" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <div class="pull-right">

                                    </div>
                                </div>
                            </div>
                        </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
                </form>
            </div>
        </div>
    </div>
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