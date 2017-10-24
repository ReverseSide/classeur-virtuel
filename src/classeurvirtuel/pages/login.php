<?php
//*********************************************************************************
// Societe: CEPM
// Auteur : Gregory Krieger
// Création   : 23.05.2016
// But    : Page de login
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************
session_start();

//inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');

//Si formulaire exécuté check si le login correspond à un professeur
if(!empty($_POST['nom'])&& !empty($_POST['pass']))
{

    $bd=new dbIfc();
    $blnconnect=$bd->Login($_POST['nom'],$_POST['pass']);
    unset($bd);

    //Si l'utilisateur est confirmé, redirection vers la page d'accueil
    if($blnconnect==true)
    {
        header('Location: index.php');
    }
    else
    {


            header('Location: login.php');

    }
}

//Connexion utilisant LDAP
/*if(isset($_POST['nomUtilisateur'])&& $mdp=$_POST['motDePasse'])
{
    $ldaprdn  = $_POST['nomUtilisateur'];     // DN ou RDN LDAP
    $ldappass =  $mdp=$_POST['motDePasse'];  // Mot de passe associé

// Connexion au serveur LDAP
    $ldapconn = ldap_connect("ldap.example.com")
    or die("Impossible de se connecter au serveur LDAP.");

    if ($ldapconn) {

        // Connexion au serveur LDAP
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

        // Vérification de l'authentification
        if ($ldapbind) {
            echo "Connexion LDAP réussie...";
        } else {
            echo "Connexion LDAP échouée...";
        }

    }

}*/


?>


<!DOCTYPE html>
<html lang="en">

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
                        <form role="form" method="POST" id="login-form" name="formulaire">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Login" name="nom" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="pass" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Se souvenir
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block">Connexion</button>
                            </fieldset>
                        </form>
                        <!-- Fin de : Création du formulaire de connexion -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Interception de l'envoi du formulaire pour encrypter le mot de passe
            jQuery("#login-form").submit(function(event) {
                event.stopPropagation();
                event.preventDefault();
                var name = document.querySelector("input[name=nom]").value;
                var password = document.querySelector("input[name=pass]").value;
                var isBarCode = true;
                if (!/^[0-9]{7}.[0-9A-Za-z]$/.test(password))
                {
                    isBarCode = false;
                    password = sha256(password);
                }
                ApiQuery('../api/check-login.php', {
                    username: name,
                    password: password
                }, function(response) {
                    if (response.indexOf("success") >= 0)
                    {
                        if (isBarCode)
                        {
                            var prompt = window.confirm("Vous vous êtes connecté avec votre code barre. Voulez-vous modifier votre mot de passe?");
                            if (prompt)
                            {
                                window.location.href = "./change-password.php";
                                return;
                            }
                        }
                        window.location.href = "./index.php";
                    }
                    else
                    {
                        document.querySelector("input[name=pass]").value = "";
                        alert("Échec d'authentification");
                    }
                });
            });
        });
    </script>

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <!-- Core Javascript -->
    <script src="../js/classeurvirtuel.js"></script>

    <!-- Cryptographic function -->
    <script src="../js/sha256.js"></script>

</body>

</html>
