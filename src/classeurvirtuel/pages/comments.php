<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 12.08.2016
// But    : Page d'affichage des commentaires d'une classe par cours
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

// Check si connecté
if(empty($_SESSION['user_id']))
{
    header("Location:login.php");
}

// Vérifie les champs requis en entrée
if (!isset($_GET['idclasse']))
    $_GET['idclasse'] = 0;
if (!isset($_GET['matcode']))
    $_GET['matcode'] = "NotAMatCode";

// Récupère le nom de la classe et la matière
include_once "../include/mysql.inc.php";
$db = new dbIfc();
$className = $db->GetClassInfo($_GET['idclasse'])['cla_nom'];
$matLabel = $db->GetMatName($_GET['matcode']);

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

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body onload="UpdateComments('<?= $_GET['matcode'] ?>', <?= $_GET['idclasse'] ?>)">

        <div id="wrapper">

            <?php include("../include/menu.php"); ?>

            <div id="page-wrapper" class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Commentaires
                            <small><?= $matLabel ?> - <?= $className ?></small>
                        </h1>
                    </div>
                </div>

                <!-- Affichage du champ permettant de saisir des commentaires -->
                <div class="form-group">
                    <label for="comment-text-area">Entrez un commentaire</label>
                    <textarea id="comment-text-area" class="form-control" rows="5"></textarea>
                </div>
                <button type="button" onclick="SaveComment('<?= $_GET['matcode'] ?>', <?= $_SESSION['user_id'] ?>, <?= $_GET['idclasse'] ?>)" class="btn btn-primary">Enregistrer</button>


                <!-- Affichage des commentaires, chargés dynamiquement -->
                <hr />
                <div id="comments-container">
                    Chargement des commentaires...
                </div>

                <!-- Modal d'édition -->
                <div class="modal-container" id="edit-modal" style="visibility: hidden;">
                    <div class="modal-inner">
                        <h2>Modification d'un commentaire</h2>
                        <textarea style="width: 100%; min-height: 200px; margin-bottom: 25px;" id="edit-modal-textarea" data-class-id="<?= $_GET['idclasse'] ?>" data-mat-code="<?= $_GET['matcode'] ?>"></textarea>
                        <button class="btn btn-primary" type="button" onclick="EditCommentWithElement(jQuery('#edit-modal-textarea'))">Enregistrer</button>
                        <script type="text/javascript">
                            function EditCommentWithElement(element)
                            {
                                EditComment(
                                    element.data('comment-id'),
                                    element.val(),
                                    element.data('class-id'),
                                    element.data('mat-code')
                                );
                            }

                            function OpenEditModal(commentId, commentElement)
                            {
                                var textarea = jQuery("#edit-modal-textarea");
                                textarea.data("comment-id", commentId);
                                textarea.val(jQuery(commentElement).html());
                                jQuery("#edit-modal").css("visibility", "visible");
                            }
                        </script>
                    </div>
                </div>

            </div>

        </div>


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

        <!-- Core Javascript -->
        <script src="../js/classeurvirtuel.js" async></script>

        <!-- Styles pour cette page -->
        <style>

            #comments-container {
                margin-bottom: 50px;
            }

            #comments-container .comment {
                margin-bottom: 10px;
                padding: 10px 25px 15px 25px;
                border-radius: 5px;
                background-color: #fcf8e3;
            }
            #comments-container .comment.mine {
                background-color: #d9edf7;
            }

            #comments-container .comment .heading {
                margin-bottom: 10px;
                font-size: 110%;
            }

            #comments-container .comment .author {
                float: right;
            }

            #comments-container .comment .date {
                font-weight: bold;
            }

            #comments-container .comment .content {
                margin-left: 15px;
                white-space: pre;
            }

            #comments-container .comment .author .fa {
                visibility: hidden;
                opacity: 0;
                cursor: pointer;
                transition: opacity 0.2s, visibility 0s 0.2s;
            }
            #comments-container .comment:hover .author .fa {
                visibility: visible;
                opacity: 1;
                transition: opacity 0.2s;
            }
            #comments-container .fa.fa-pencil { color: #E8B700; }
            #comments-container .fa.fa-times { color: #FF4A00; }

            .modal-container {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(26, 26, 26, 0.3);
                z-index: 1000;
            }
            .modal-inner {
                position: relative;
                top: 175px;
                margin: auto;
                padding: 20px;
                max-width: 800px;
                width: 75%;
                background-color: white;
                border-radius: 3px;
                box-shadow: 0 0 24px 3px rgba(28, 28, 28, 0.5);
            }

        </style>

    </body>
</html>
