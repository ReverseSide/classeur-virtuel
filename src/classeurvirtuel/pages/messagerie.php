<?php
session_start(  );
//*********************************************************************************
// Societe: CEPM
// Auteur : Sébastien MEtthez
// Date dernière modification   : 12.06.2017
// But    : System de messagerie
//*********************************************************************************
// Modifications:
// Date   :
// Auteur :
// Raison :
//*********************************************************************************
/*
CREATE TABLE t_messages (
id_msg INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
msg_source TEXT NOT NULL,
msg_destination TEXT NOT NULL,
msg_titre TEXT NOT NULL,
msg_contenu TEXT NOT NULL,
reg_date TIMESTAMP
);

CREATE TABLE t_mlu (
id_mlu INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
idx_message INT NOT NULL,
idx_prof INT NOT NULL,
reg_date TIMESTAMP
);

CREATE TABLE `t_messages` (
  `id_msg` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `msg_source` text NOT NULL,
  `msg_destination` text NOT NULL,
  `msg_titre` text NOT NULL,
  `msg_contenu` text NOT NULL,
  `msg_lu` int NOT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

*/


//inclusion de la classe d'interaction avec la base de donnes

include_once( '../include/bdd.php' );
include_once( '../include/mysql.inc.php' );
include_once( '../api/api.inc.php' );
include_once('../include/dblogin_cepm.php');

$previous = "javascript:history.go(-1)";

if ( isset( $_SERVER['HTTP_REFERER'] ) ) { $previous = $_SERVER['HTTP_REFERER']; } 


// Check si l'utilisateur est connect
if ( empty( $_SESSION['user_id'] ) )
{
    header( "Location:login.php" );
}
$msgid = 0 ;
$newMSG = "" ;
$bld = new dbLogin( ) ;
$bd = new dbIfc( ) ;

$proiName = array();

$sql = "SELECT * from t_professeur ";
$req=$bdd->prepare($sql);
$req->execute();
$list=$req->fetchAll();
foreach ($list as $rs) {			
	//echo "".$rs['prenom']." ".$rs['nom']."";
	$proiName["".$rs['id_professeur'].""] = "".$rs['pro_prenom']." ".$rs['pro_nom']."" ;
}


/*function idtoname($proName,){
	
}*/
function replaceidtoname($proName,$target){
	$ret = "";
	foreach (explode(",", $target) as $rs) {
			if (array_key_exists($rs, $proName)) {
				$ret = $ret ."". $proName[ $rs ] .", " ;
			}
	}
	return $ret ;
	
}
/*
echo "test replaceidtoname :<br>" ;
echo replaceidtoname($proiName,"174,166") ; 
*/
$username = urlencode( $_SESSION['user_id'] ); //$_SESSION['user_name'] ) ;
if ( isset( $_GET['msgID'] ) ) 
	if ( is_numeric( $_GET['msgID'] ) )
		$msgid = $_GET['msgID']  ;
	
if ( isset( $_POST['newMSG'] ) ) {
	$postrray = return_array($_POST, true );
	
	$newMSG = $_POST['newMSG'] ;
	$msource  =  $_SESSION['user_id'] ; //$username 
	$mdest    =  "" ;
	$mtitre   =  urlencode( $_POST['titre'] ) ;
	$mcontenu =  urlencode( $_POST['message'] ) ;
	//echo "NEWMSG !!!!!";
	/*echo "<b>POST</b><br>";
	echo "$postrray";
	echo "<br><b>DEST : </b><br><br>";
	print_r($_POST['selDest']);
	echo "<b>ta_: . ;</b><br>";
	*/
	foreach ($_POST['selDest'] as $ta)
	{
			//echo "<b>ta_: $ta ;</b><br>";
			$ref="insert into t_messages (msg_source, msg_destination, msg_titre, msg_contenu, msg_lu) values ('".$msource."', '".urlencode( $ta )."', '".$mtitre."', '".$mcontenu."', 0 )";	
			$req=$bdd->prepare($ref);
			$req->execute();
	}
}
	

function msgLINKs( $id, $text )
{
	return "<a href=\"?msgID=".$id."\">$text</a>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>CEPM Scan System V2.0</title>
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300" />
    <script src="https://use.fontawesome.com/1d0285a7f2.js"></script>
</head>
<body>
<div id="wrapper">
	<?php include("../include/menu.php"); ?>
    <div id="page-wrapper">
			<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#message">Boîte de réception</a></li>
					<li><a data-toggle="tab" href="#nouveau">Nouveau</a></li>
					<li><a data-toggle="tab" href="#envoye">Mes Envois</a></li>
			</ul>
			<div class="tab-content">
					<div id="envoye" class="tab-pane fade">
							<h2 class="page-header"><i class="fa fa-info-circle"></i> Mes envois </h2>

							<table id="tlMSG" border="1" class="table table-bordered">
								<?php 
									$requete3=$bdd->prepare("SELECT * FROM t_messages, t_professeur WHERE id_professeur='".$username."' AND msg_source='".$username."' AND msg_del<>1 ORDER BY reg_date DESC ");
									$requete3->execute();
									$msgs=$requete3->fetchAll();
									echo "<tr><th>Statut</th><th>Expéditeur</th><th>Titre</th><th>Date</th><th>Action</th><tr>";
									foreach ($msgs as $ta)
									{			
										if ( $ta['msg_lu'] == 0) $color = '';
										else  					$color = "";
										echo "<tr class=\"blackbtn\" id=\"ta".$ta['id_msg']."\" $color onclick=\"chowMSG( ".$ta['id_msg']." );\"><td> - </td><td>".urldecode ( $proiName[ $ta['msg_destination'] ] )."</td><td>".urldecode ($ta['msg_titre'])."</td><td>".date( 'd-m-Y H:i:s',strtotime($ta['reg_date'] ))."</td><td id=\"del".$ta['id_msg']."\" onclick=\"deleteMSG_Sender(".$ta['id_msg'].");\"><i  class=\"fa fa-trash\" aria-hidden=\"true\"></i></td></tr>";
									}
							?>
							</table>
					</div>
					<div id="message" class="tab-pane fade in active">
							<h2 class="page-header"><i class="fa fa-info-circle"></i> Mes messages </h2>

							<table id="tblMSG" border="1" class="table table-bordered">
								<!-- <tr><th> - </th><th> - </th><th> - </th><th> - </th></tr> --->
								<?php 
									
									$requete3=$bdd->prepare("SELECT * FROM t_messages, t_professeur WHERE id_professeur='".$username."' AND msg_destination='".$username."' AND msg_lu < 3 ORDER BY reg_date DESC ");
									$requete3->execute();
									$msgs=$requete3->fetchAll();
                                    echo "<tr><th>Statut</th><th>Destinataire</th><th>Titre</th><th>Date</th><th>Action</th><tr>";
									foreach ($msgs as $ta)
									{			
										if ( $ta['msg_lu'] == 0){
										    $color = 'notread';
										    $envelope = 'fa fa-envelope-o';
										    $classaddittion = 'blanctxt'; }
										else {
                                            $color = '';
                                            $envelope = 'fa fa-envelope-open-o';
                                            $classaddittion = 'blacktxt';}
                                        echo "<tr id=\"tr".$ta['id_msg']."\" class=\"blackbtn $classaddittion $color\"  ><td id=\"tr".$ta['id_msg']."\" onclick=\"chowMSG(".$ta['id_msg'].");\"><i id=\"enve".$ta['id_msg']."\" class=\"$envelope\"} aria-hidden=\"true\"></i></td><td id=\"tr".$ta['id_msg']."\" onclick=\"chowMSG(".$ta['id_msg'].");\">".urldecode ( $proiName[ $ta['msg_source'] ] ) ."</td><td id=\"tr".$ta['id_msg']."\" onclick=\"chowMSG(".$ta['id_msg'].");\">".urldecode ($ta['msg_titre'])."</td><td id=\"tr".$ta['id_msg']."\" onclick=\"chowMSG(".$ta['id_msg'].");\">".date( 'd-m-Y H:i:s',strtotime($ta['reg_date']))."</td><td id=\"del".$ta['id_msg']."\" onclick=\"deleteMSG(".$ta['id_msg'].");\"><i  class=\"fa fa-trash\" aria-hidden=\"true\"></i></td></tr>";
                                    }
							?>
							</table>
					</div>
					<div id="nouveau" class="tab-pane fade">
							<h2 class="page-header"><i class="fa fa-info-circle"></i> Nouveau Message</h2>
							<input type="text" id="Targ_id"  class="form-control" placeholder="Search..." onkeyup="autocomplet()">
							<table>
								<ul id="Targ_list_id"></ul>
							</table>
							<div class="row">
									<div class="col-sm-3"><input class="btn btn-primary btn-block" value="Annuler" onclick="document.getElementById('newMSGForm').reset();" /></div>
									<form id="newMSGForm" action="messagerie.php" method="post">
									<div class="col-sm-6"></div>
									<div class="col-sm-3"><input type="submit" class="btn btn-primary btn-block" value=" Envoyer  " name="frmMsgSubmit" /></div>
							</div>
							<br>					
							<input type="hidden" name="newMSG" value="newMSG" />
							<select multiple class="form-control input-lg" id="selDest" name="selDest[]"> 
							<?php
							/*foreach ($bld->listProf($_SESSION['login']) as $eac ) 
								echo "<option value=\"".$eac['uid']."\">" . $eac['prenom'] . " " . $eac['nom'] . "</option>";
								<option> abcd </option>
							<option> defg </option>
							<option> ghij </option>
							<option> jklm </option>
							<option> mnop </option>
							<option> pqrs </option>
							<option> stuv </option>
							<option> vwxy </option>
							<option> yyzz </option>*/
							?>							
							</select>
							<br>
							<input class="form-control" name="titre" id="titre" placeholder="Titre" /> <br>
							
							<textarea class="form-control input-lg" cols="25" rows="35" placeholder="Message" id="message" name="message"></textarea>
							
							</form>
					</div>
					<div id="menu2" class="tab-pane fade">
							<h2 class="page-header"><i class="fa fa-info-circle"></i> Messages personnels</h2>
							<p>Some content in menu 2.</p>
					</div>
					<!-- Modal -->
					<div class="modal fade" id="msgModal" role="dialog">
						<div class="modal-dialog">   
							  <div class="modal-content">
									<div class="modal-header">
										  <h4 class="modal-title">Message</h4>
									</div>
									<div class="modal-body">
										<p> Expéditeur : </p>
										<input disabled="disabled" class="form-control input-lg" type="text" id="rsource" name="rsource" ></input>
										<textarea class="form-control input-lg" cols="25" rows="15" id="rmessage" name="rmessage" readonly></textarea>
									</div>
									<div class="modal-footer">
                                        <button onclick="" type="button" class="btn btn-success">Répondre</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
									</div>
							  </div>
						</div>
					</div>
					<!-- ./ Modal -->
                    <div id="reply" class="modal tab-pane fade">
                        <h2 class="page-header"><i class="fa fa-info-circle"></i> Répondre au message</h2>
                        <input type="text" id="Targ_id"  class="form-control">
                        <table>
                            <ul id="Targ_list_id"></ul>
                        </table>
                        <div class="row">
                            <div class="col-sm-3"><input class="btn btn-primary btn-block" value="Annuler" onclick="document.getElementById('replyMSGForm').reset();" /></div>
                            <form id="replyMSGForm" action="messagerie.php" method="post">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-3"><input type="submit" class="btn btn-primary btn-block" value=" Envoyer  " name="frmMsgSubmit" /></div>
                        </div>
                        <br>
                        <input type="hidden" name="newMSG" value="newMSG" />
                        <select multiple class="form-control input-lg" id="selDest" name="selDest[]">
                        </select>
                        <br>
                        <input class="form-control" name="titre" id="titre" placeholder="Titre" /> <br>

                        <textarea class="form-control input-lg" cols="25" rows="35" placeholder="Message" id="message" name="message"></textarea>

                        </form>
                    </div>
			</div>
	</div>
</div>
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="../dist/js/sb-admin-2.js"></script>
<!-- Core Javascript -->
<script src="../js/classeurvirtuel.js" async></script>
<script type="text/javascript">
var pro_nm = [{}];
var pro_n = {};

$(document).ready(function() {                                    });

function getnm(trg, dst ) {
	if(!pro_nm.hasOwnProperty(""+trg+"")){		
			$.ajax({
			url: '../pages/ajax_uid_to_n.php?uID='+trg,
			type: 'GET',
			success:function(data){
				var a = data ; 
				if (dst != 'null')
					document.getElementById( dst ).value = data ;	
				pro_nm[trg] = data; 
				
				console.log( "getnm("+trg+")AJAX>>" + a );
				return pro_nm[trg] ;
			}
		});
	} else { console.log( "getnm("+trg+")EXST>>" + trg);
			 return pro_nm[trg] ; }
}
// autocomplet : this function will be executed every time we change the text
function change(trg) {
	var select = document.getElementById( 'selDest' );

	for ( var i = 0, l = select.options.length, o; i < l; i++ )
	{
		o = select.options[i];
		if ( o.value  == trg )
			select.options[i].selected = true;
	}
	
}
function chadd(trg, name ) {
	var select = document.getElementById( 'selDest' );
	var notin = 0;
	for ( var i = 0, l = select.options.length, o; i < l; i++ )
	{
		o = select.options[i];
		if ( o.value  == trg )
		 notin++;	
	}
	if (notin == 0)
	{
		document.getElementById("selDest").innerHTML = document.getElementById("selDest").innerHTML + '<option selected value="'+trg+'" >'+name+'</option>';
		/*select.options[i+1].value = trg;
		select.options[i+1].text = name;
		select.options[i].selected = true;*/
	}
}
function autocomplet() {
	var min_length = 0; // min caracters to display the autocomplete
	var keyword = $('#Targ_id').val();

	if (keyword.length >= min_length) {
		$.ajax({
			url: '../pages/ajax_refresh_prof.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#Targ_list_id').show();
				$('#Targ_list_id').html(data);



			}
		});
	} else {
		$('#Targ_list_id').hide();
	}

}

function chowMSG(msgID) {
	$.ajax({
			url: '../pages/ajax_get_msg.php?msgID='+msgID,
			type: 'get',
			success:function(data){
				
				document.getElementById("rmessage").innerHTML = data ;
			}
		});
	$.ajax({
			url: '../pages/ajax_get_msg.php?srcID='+msgID,
			type: 'get',
			success:function(ata){
				//getnm( ata ) ;
				//alert( getnm(""+ata+"", "rsource" ) );
				document.getElementById( "rsource" ).value = ata ;	
				/*getnm(""+ata+"", "rsource" );
				
				alert( getnm( ata ) );*/
			}
		});
	$('#msgModal').modal('show');
	document.getElementById( "tr"+msgID ).className += " blackbtn";
    document.getElementById( "tr"+msgID ).classList.remove("notread");
    document.getElementById( "tr"+msgID ).classList.remove("blanctxt");
    document.getElementById( "enve"+msgID).className ="fa fa-envelope-open-o";
}

function deleteMSG(msgID) {
    var answer = confirm("Voulez-vous supprimer le message ?");
    if (answer) {
        $.ajax({
                url: '../pages/ajax_del_msg.php?srcID='+msgID,
                type: 'get',
                success: function(){
                    location.reload(false);
                }
        });
    };
}

function deleteMSG_Sender(msgID){
    var answer = confirm("Voulez-vous supprimer le message ?");
    if (answer) {
        $.ajax({
            url: '../pages/ajax_del_msg_sender.php?srcID='+msgID,
            type: 'get',
            success: function(){
                location.reload(false);
            }
        });
    };
}

// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('#Targ_id').val(item);
	// hide proposition list
	$('#Targ_list_id').hide();


}
</script>
</body>

<style>

.notread{
background-color: #337ab7;
}


.blackbtn {
cursor: pointer; /* changes the mouse on hover */
padding: 10px 30px; /* adds 10px of space to top and bottom of text and 30px of space on either side */
color: #000;
}

.blackbtn:hover {
background-color: #286090;
border-color: #204d74;
color: #fff;
}

.blanctxt {
color: #fff;
}

.blacktxt{
color: #000;
}
/* XOMISSE END CSS FOR BUTTON */

.fa-trash:hover{
color:red;
}
</style>

</html>

<?php
unset($bld);
?>
