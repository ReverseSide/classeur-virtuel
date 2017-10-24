<?php
/**
 * Created by PhpStorm.
 * User: gregory.krieger
 * Date: 05.11.2015
 * Time: 16:49
 */

if(isset($_POST['useradmin']) && isset($_POST['passadmin']))
{
    if($_POST['useradmin']=="cepmadmin" && $_POST['passadmin']=="123abc4560")
    {
        $_SESSION['administration']=true;

    }
}

include_once( '../include/bdd.php' );
include_once( '../include/mysql.inc.php' );
include_once( '../api/api.inc.php' );
include_once('../include/dblogin_cepm.php');
$newMSGs = 0 ;
$uusername = urlencode( $_SESSION['user_id'] ) ; //$_SESSION['user_name'] ) ;

/*Requête nb message*/
$req = $bdd->prepare("SELECT * FROM t_messages WHERE msg_destination='".$uusername."' AND msg_lu='0'");
$req->execute();
$nb_msg_count = $req->rowCount();
$req->closeCursor();

?><!-- Navigation -->
<head>
  <link rel="stylesheet" type="text/css"  href="../include/menu.css"/>
    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<!-------------------------------------------------------------------------------------------------------------------------------------------->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <a class="navbar-brand" href="index.php">CEPM Classeur Virtuel v2.0</a>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">



        <!-- /.dropdown -->

        <li id="time" class="dropdown">


        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <!-- <li><a href="#"><i class="fa fa-user fa-fw"></i> Mon profil</a>
                </li>
                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Paramètres</a>
                </li>-->
                <li class="divider"></li>
                <li><a href="../pages/deconnexion.php"><i class="fa fa-sign-out fa-fw"></i> Déconnexion</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->


    <!-- /.navbar-static-side -->
</nav>
<!-------------------------------------------------------------------------------------------------------------------------------------------->
<div class="row">
    <!-- uncomment code for absolute positioning tweek see top comment in css -->
    <!-- <div class="absolute-wrapper"> </div> -->
    <!-- Menu -->
    <div class="side-menu">

    <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <div class="brand-wrapper">
            <!-- Hamburger -->
            <button type="button" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Search body -->
            <div id="search" class="panel-collapse collapse">
                <div class="panel-body">
                    <form class="navbar-form" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                        <button type="submit" class="btn btn-default "><span class="glyphicon glyphicon-ok"></span></button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Menu -->
    <div class="side-menu-container">
        <ul class="nav navbar-nav">
            <li><a href="../pages/index.php"><span class="glyphicon glyphicon-bookmark"></span> Mes classes</a></li>
            <li class="active"><a href="../pages/messagerie.php"><span class="glyphicon glyphicon-send"></span> Messagerie  <span class="badge"><?php echo $nb_msg_count; ?></span></a></li>
            <?php if(isset($_SESSION['administration'])){ ?>
            <!-- Dropdown-->
            <li class="panel panel-default" id="dropdown">
                <a data-toggle="collapse" href="#dropdown-lvl1">
                    <span class="glyphicon glyphicon-user"></span> Administration <span class="caret"></span>
                </a>

                <!-- Dropdown level 1 -->
                <div id="dropdown-lvl1" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul class="nav navbar-nav">
                          <li><a href="../pages/departementtdb.php">Tableau de bord</a></li>
                          <li><a href="../pages/eleve_search.php">Elèves</a></li>
                          <li><a href="../pages/classe_list.php">Classes</a></li>
                          <li><a href="../pages/entreprise_search.php">Entreprises</a></li>
                          <li><a href="../pages/gestionheure.php">Horaires</a></li>
                          <li><a href="../pages/extraction.php">Extraction</a></li>
                          <li><a href="../pages/rapport_execution.php">Rapport d'exécution</a></li>
                        </ul>
                    </div>
                </div>
            </li> <?php } else{ ?>
                          <li><a href="#" data-toggle="modal" data-target="#login-modal" style="color: grey;"><span class="glyphicon glyphicon-bookmark"></span> Administration</a></li>
                  <?php } ?>

            <li class="panel panel-default" id="dropdown">
                <a data-toggle="collapse" href="#dropdown-lvl2">
                    <span class="glyphicon glyphicon-tag"></span> Sport <span class="caret"></span>
                </a>

                <!-- Dropdown level 1 -->
                <div id="dropdown-lvl2" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul class="nav navbar-nav">
                          <li class="panel panel-default" id="dropdown">
                               <a data-toggle="collapse" href="#dropdown-lvl3">
                                    Été <span class="caret"></span>
                               </a>
                               <div id="dropdown-lvl3" class="panel-collapse collapse">
                                   <div class="panel-body">
                                       <ul class="nav navbar-nav">
                                           <li><a href="../pages/inscription_sportive_ete.php">Inscription</a></li>
                                           <li><a href="../pages/extraction_sport_ete.php">Extraction</a></li>
                                           <li><a href="../pages/presence_sport_ete.php">Présence</a></li>
                                       </ul>
                                   </div>
                               </div>
                           </li>
                           <li class="panel panel-default" id="dropdown">
                             <a data-toggle="collapse" href="#dropdown-lvl4">
                                  Hiver <span class="caret"></span>
                             </a>
                             <div id="dropdown-lvl4" class="panel-collapse collapse">
                                 <div class="panel-body">
                                     <ul class="nav navbar-nav">
                                       <li><a href="../pages/inscription_sportive.php">Inscription</a></li>
                                       <li><a href="../pages/extraction_sport_ete.php">Extraction</a></li>
                                       <li><a href="../pages/presence_sport_ete.php">Présence</a></li>
                                     </ul>
                                 </div>
                             </div>
                         </li>

                        </ul>
                    </div>
                </div>
            </li>
			<li><a href="../pages/horaire_edt.php"><span class="glyphicon glyphicon-calendar"></span> Horaires</a></li>
            <li><a href="http://Enseignant:CEPM_2017@cepm.educanet2.ch/info/Qualite/redirection.html" target="_blank"><span class="glyphicon glyphicon-globe"></span> Référentiel QSC</a></li>




        </ul>
    </div><!-- /.navbar-collapse -->
</nav>

    </div>

    <!-- Main Content -->

</div>
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="loginmodal-container">
            <h1>Administration</h1><br>
            Accès réservé
            <form role="form" method="POST" name="formulaire">

                <input type="text" name="useradmin" placeholder="Username">
                <input type="password" name="passadmin" placeholder="Password">
                <input type="submit" name="login" class="login loginmodal-submit" value="Login">
            </form>
        </div>
    </div>
</div>
<style>
    @import url(https://fonts.googleapis.com/css?family=Roboto);

    /****** LOGIN MODAL ******/
    .loginmodal-container {
        padding: 30px;
        max-width: 350px;
        width: 100% !important;
        background-color: #F7F7F7;
        margin: 0 auto;
        border-radius: 2px;
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        font-family: roboto;
    }

    .loginmodal-container h1 {
        text-align: center;
        font-size: 1.8em;
        font-family: roboto;
    }

    .loginmodal-container input[type=submit] {
        width: 100%;
        display: block;
        margin-bottom: 10px;
        position: relative;
    }

    .loginmodal-container input[type=text], input[type=password] {
        height: 44px;
        font-size: 16px;
        width: 100%;
        margin-bottom: 10px;
        -webkit-appearance: none;
        background: #fff;
        border: 1px solid #d9d9d9;
        border-top: 1px solid #c0c0c0;
        /* border-radius: 2px; */
        padding: 0 8px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .loginmodal-container input[type=text]:hover, input[type=password]:hover {
        border: 1px solid #b9b9b9;
        border-top: 1px solid #a0a0a0;
        -moz-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    .loginmodal {
        text-align: center;
        font-size: 14px;
        font-family: 'Arial', sans-serif;
        font-weight: 700;
        height: 36px;
        padding: 0 8px;
        /* border-radius: 3px; */
        /* -webkit-user-select: none;
          user-select: none; */
    }

    .loginmodal-submit {
        /* border: 1px solid #3079ed; */
        border: 0px;
        color: #fff;
        text-shadow: 0 1px rgba(0,0,0,0.1);
        background-color: #4d90fe;
        padding: 17px 0px;
        font-family: roboto;
        font-size: 14px;
        /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#4787ed)); */
    }

    .loginmodal-submit:hover {
        /* border: 1px solid #2f5bb7; */
        border: 0px;
        text-shadow: 0 1px rgba(0,0,0,0.3);
        background-color: #357ae8;
        /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#357ae8)); */
    }

    .loginmodal-container a {
        text-decoration: none;
        color: #666;
        font-weight: 400;
        text-align: center;
        display: inline-block;
        opacity: 0.6;
        transition: opacity ease 0.5s;
    }

    .login-help{
        font-size: 12px;
    }
</style>
<script>
$(function () {
  $('.navbar-toggle').click(function () {
      $('.navbar-nav').toggleClass('slide-in');
      $('.side-body').toggleClass('body-slide-in');
      $('#search').removeClass('in').addClass('collapse').slideUp(200);

      /// uncomment code for absolute positioning tweek see top comment in css
      //$('.absolute-wrapper').toggleClass('slide-in');

  });

 // Remove menu for searching
 $('#search-trigger').click(function () {
      $('.navbar-nav').removeClass('slide-in');
      $('.side-body').removeClass('body-slide-in');

      /// uncomment code for absolute positioning tweek see top comment in css
      //$('.absolute-wrapper').removeClass('slide-in');

  });
});
</script>
