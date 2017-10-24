<?php
session_start();
//*********************************************************************************
// Societe: CEPM
// Auteur : Florent Crisinel
// Date dernière modification   : 20.08.2016
// But    : Route de modification et de suppression d'un commentaire
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************
function print_array(&$a, $str = "")
{
    if ($str[0]) { echo "$str ="; }
    echo ' array( ';
    foreach ($a as $k => $v) {
        echo "[$k]".' => ';
        if (is_array($v)) { print_array($v); }
        else { echo "<strong>$a[$k]</strong> "; }
    }
    echo ')  ';
}
function return_array($array, $html = false, $level = 0) {
    $space = $html ? "&nbsp;" : " ";
    $newline = $html ? "<br />" : "\n";
    $spaces = "";
    for ($i = 1; $i <= 6; $i++) {         $spaces .= $space;    }
    $tabs = $spaces;
    for ($i = 1; $i <= $level; $i++) {        $tabs .= $spaces;    }
    $output = "Array" . $newline . $newline;
    foreach($array as $key => $value) {
        if (is_array($value)) {
            $level++;
            $value = return_array($value, $html, $level);
            $level--;
        }   $output .= $tabs . "[" . $key . "] => " . $value . $newline;
    }
    return $output;
}
if (isset($_POST['ele_politesse']))
{
    echo return_array($_POST, true );
    $stu =  $_GET['stu'] ;
    echo "<b>stu : $stu </b><br>";
}
// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
if ( !isset( $_GET['stu'] ) )
{
    http_response_code( 400 );
    die( "Required parameters are: stu");
}

// Vérifie que l'utilisateur soit connecté. Si non, renvoyer une erreur 403 (Forbidden)
if ( empty( $_SESSION['user_id'] ) )
{
    http_response_code( 403 );
    die( "Forbidden" );
}

if ( empty( $_SESSION['user_id'] ) )
{
    http_response_code( 403 );
    die( "Forbidden" );
}

// Inclusion de la classe d'interaction avec la base de données
include_once('../include/mysql.inc.php');
$bd = new dbIfc();


$stu =  $_GET['stu'] ;
//echo "<b>stu : $stu </b><br>";
$tabStudent=$bd->GetStudent( $stu );
/*
echo "<b>tabStudent : </b><br>";
//print_r($tabStudent);
echo return_array($tabStudent, true );
//$bd->UpdateComment($_POST['id'], $_POST['commentaire']);
echo "\n<br>\n<br>\n";
echo "<b>JSNStudent : </b><br>";*/
echo str_replace(":null,", ":\"\",", json_encode($tabStudent[0]) ) ;

unset($bd);
