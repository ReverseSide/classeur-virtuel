<?php
	try{
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO('mysql:host=nlhj.myd.infomaniak.com;dbname=toner','root','',$pdo_options);
	}
	catch (Exception $e){
		die('Erreur : ' . $e->getMessage());
	}
	$requete = $bdd->prepare('SELECT * FROM Toner');
	$requete->execute();
	$toner=$requete->fetchAll();
?>
<html>
	<head>
		<title>Liste des toners</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<h1>Liste des toners</h1>
		</br></br>
		<table>
			<tr>
				<td align="center">Code barre</td>
				<td align="center">Modèle</td>
				<td align="center">Stock disponible</td>
				<td align="center">Stock minimum</td>
				<td align="center">Tête d'impression</td>
				<td align="center">Couleur</td>
				<td align="center">Lieu</td>
			</tr>
			<?php for($a=0;$a<count($toner);$a++){ ?>
				<tr>
					<td><input type="text" name="idToner" value="<?php echo $toner[$a]['idToner'] ?>"></td>
					<td><input type="text" name="numModel" value="<?php echo $toner[$a]['numModel'] ?>"></td>
					<td><input type="number" name="stock" value="<?php echo $toner[$a]['stock'] ?>"></td>
					<td><input type="number" name="stockMin" value="<?php echo $toner[$a]['stockMin'] ?>"></td>
					<td align="center">
						<select name="select">
							<option <?php if($toner[$a]['teteImpression']==1)echo 'selected="selected"' ?>>oui</option>
							<option <?php if($toner[$a]['teteImpression']==0)echo 'selected="selected"' ?>>non</option>
						</select>
					</td>
					<td><input type="text" name="couleur" value="<?php echo $toner[$a]['couleur'] ?>"></td>
					<td>
						<?php
							for($c=0;$c<strlen($toner[$a]['idLieu']);$c++){
								if($c>0)echo' et ';
								switch(substr($toner[$a]['idLieu'], $c, 1)){
									case '1': echo'Armoire 1, CEPM';
										break;
									case '2': echo'Armoire 2, CEPM';
										break;
									case '3': echo'Armoire 3, CEPM';
										break;
									case '4': echo'Armoire 4, CEPM';
										break;
									case '5': echo'Armoire 1, Gymnase';
										break;
								}
							}
						?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</body>
</html>