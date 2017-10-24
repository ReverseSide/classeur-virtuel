<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 15.08.2016
// But    : Page d'affichage et de saisie des coches pour une classe
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************

?>
<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>SB Admin 2 - Bootstrap Admin Theme</title>

        <!-- Bootstrap Core CSS -->
        <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

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

        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h1 style="font-weight: bold">CEPM</h1><h3>Classeur Virtuel</h3>
                        </div>
                        <div class="panel-body">
                            <!-- Création du formulaire de connexion -->
                            <form role="form" name="formulaire">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Ancien mot de passe" name="old_password" type="password" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Nouveau mot de passe" name="pass_1" type="password" value="">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Confirmation" name="pass_2" type="password" value="">
                                    </div>
                                    <!-- Change this to a button or input when using this as a form -->
                                    <button type="button" onclick="SavePassword()" class="btn btn-lg btn-success btn-block">Changer le mot de passe</button>
                                </fieldset>
                            </form>
                            <!-- Fin de : Création du formulaire de connexion -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function SavePassword()
            {
                var oldPw = document.querySelector("input[name=old_password]");
                var newPw = document.querySelector("input[name=pass_1]");
                var confirm = document.querySelector("input[name=pass_2]");
                if (newPw.value !== confirm.value)
                {
                    newPw.value = "";
                    confirm.value = "";
                    alert("Les mots de passe que vous avez entré ne correspondent pas");
                }

                ApiQuery('../api/update-password.php', {
                    oldPassword: oldPw.value,
                    newPassword: sha256(newPw.value)
                }, function(response) {
                    if (response.indexOf("success") >= 0)
                    {
                        window.location.href = "./index.php";
                    }
                    else
                    {
                        oldPw.value = "";
                        alert("Impossible de mettre à jour votre mot de passe. Votre code barre est-il correct?");
                    }
                });
            }
        </script>

        <!-- jQuery -->
        <script src="../bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="../dist/js/sb-admin-2.js"></script>

        <!-- Core javascript -->
        <script src="../js/classeurvirtuel.js"></script>

        <!-- Cryptographic function -->
        <script src="../js/sha256.js"></script>

    </body>
</html>
