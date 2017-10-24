<?php
	/* Créé par Antoni Jashareviq
	 * Version 0.1.0
	 * 18.02.14
	<?php
$image = "http://uploads.siteduzero.com/files/192001_193000/192501.png";
// Ouvre un fichier pour lire un contenu existant
$current = file_get_contents($image);
// Écrit le résultat dans le fichier
$file = "dossier/mini.png";
file_put_contents($file, $current);
?>


	 */
	session_start();
	if(!isset($_SESSION['user']) && isset($_POST['nomUtilisateur']) && isset($_POST['motDePasse'])){
		$user=$_POST['nomUtilisateur'];
		$mdp=$_POST['motDePasse'];
		$connexion=ldap_connect("SERV-DC1.cepm.ch");
		if($connexion){
			ldap_set_option($connexion,LDAP_OPT_PROTOCOL_VERSION,3);
			ldap_set_option($connection, LDAP_OPT_REFERRALS,0);
			ldap_set_option($ds, LDAP_OPT_SIZELIMIT, 100000000);
			$session=ldap_bind($connexion,$user."@cepm.ch",$mdp);
			try{
				if(ldap_bind($connexion,$user."@cepm.ch",$mdp)){
					$parametres = array("cn");
					$resultat=ldap_search($connexion,'DC=cepm,DC=ch','(samaccountname='.$user.'*)',$parametres);
					$entrees=ldap_get_entries($connexion,$resultat);
					if($entrees['count']!=1)echo'<script type="text/javascript">alert("Une erreure est survenue lors de la récupération de votre compte");</script>';
				}
				else echo'<script type="text/javascript">alert("Mot de passe ou nom d\'utilisateur incorrect");</script>';
			}
			catch (Exception $e){
				echo'<script type="text/javascript">alert("Mot de passe ou nom d\'utilisateur incorrect");</script>';
			}
		}
		else echo'<script type="text/javascript">alert("Probleme de connexion");</script>';
		ldap_unbind($connexion);
	}
	else{ ?>
		<html>
			<head>
				<title>Connexion</title>
			</head>
			<body>
				<form id="connexion" action="index 2.php" method="POST">
				    <h1>Connexion</h1>
				    <fieldset id="inputs">
					<input name="nomUtilisateur" type="text" placeholder="Nom d'utilisateur" autofocus required>   
					<input name="motDePasse" type="password" placeholder="Mot de passe" required>
				    </fieldset>
				    <fieldset id="actions">
					<input type="submit" id="submit" value="Se connecter">
				    </fieldset>
				</form>
			</body>
		</html>
	<?php }
?>
