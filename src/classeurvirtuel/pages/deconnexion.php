<?php
/**
 * Created by PhpStorm.
 * User: vincent.montet
 * Date: 17.08.2017
 * Time: 15:54
 */

session_start();

// Destruction à la main des variables et variables sessions connues
$nom="";
$prenom="";
$nom="";

$_SESSION['user_id']="";
$_SESSION['login'] = "";
$_SESSION['user_name']= "";


// Détruit toutes les variables de session
$_SESSION = array();

// Si vous voulez détruire complètement la session, effacez également
// le cookie de session.
// Note : cela détruira la session et pas seulement les données de session !
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalement, on détruit la session.

session_destroy();

header('Location: login.php');



