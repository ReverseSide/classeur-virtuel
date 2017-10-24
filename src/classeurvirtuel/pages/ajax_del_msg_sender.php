<?php
/**
 * Created by PhpStorm.
 * User: vincent.montet
 * Date: 14.08.2017
 * Time: 11:20
 */

session_start(  );


//inclusion de la classe d'interaction avec la base de donnes

include '../include/bdd.php';
include_once( '../include/mysql.inc.php' );
include_once( '../api/api.inc.php' );

$previous = "javascript:history.go(-1)";

if ( isset( $_SERVER['HTTP_REFERER'] ) ) { $previous = $_SERVER['HTTP_REFERER']; }

if ( empty( $_SESSION['user_id'] ) )
{
    header( "Location:login.php" );
}
$msgid = 0 ;
$newMSG = "" ;
$bd = new dbIfc( ) ;

if ( isset( $_GET['srcID'] ) ){
    if ( is_numeric( $_GET['srcID'] ) )
    {
        $msgid = $_GET['srcID']  ;
        $re = $bdd->prepare( "UPDATE t_messages SET msg_del=1 WHERE id_msg='".$msgid."' " ) ;
        $re->execute();
        $re->closeCursor();
        $re->execute();
    }
}
?>