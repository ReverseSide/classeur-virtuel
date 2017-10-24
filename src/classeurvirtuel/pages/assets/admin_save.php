<?php
	/* 
	 * Page:	produit.php
	 * Auteur:	Antoni Jashareviq
	 * Version:	0.9.1
	 * A faire:	Verification image au bon format
	 */
	include 'scripts/config.php';
	if(isset($_POST['ajouter'])){
				$idSolde='\'\'';
				if(isset($_POST['id'])&&ctype_digit($_POST['id']))$id=intval($_POST['id']);
				else{
					$requete=$bdd->prepare('SELECT AUTO_INCREMENT as id FROM INFORMATION_SCHEMA.TABLES WHERE table_name = \'Produit\'');
					$requete->execute();
					$tmp=$requete->fetch();
					$id=$tmp['id'];
				}
				if($_POST['prixReduction']!=null){
					$affichePourcentage=0;
					$afficheReduction=0;
					if(isset($_POST['affichePourcentage']))$affichePourcentage=1;
					if(isset($_POST['afficheReduction']))$afficheReduction=1;
					$requete=$bdd->prepare('INSERT INTO Solde Values (\'\',:prixReduction,:affichePourcentage,:afficheReduction,:dateDebut,:dateFin)');
					$requete->execute(array('prixReduction' => $_POST['prixReduction'],
											'affichePourcentage' => $affichePourcentage,
											'afficheReduction' => $afficheReduction,
											'dateDebut' => $_POST['dateDebut'],
											'dateFin' => $_POST['dateFin']));
					$requete=$bdd->prepare('SELECT max(idSolde) as id FROM Solde');
					$requete->execute();
					$idSolde=$requete->fetch();
					$idSolde=$idSolde['id'];
				}
				else if(isset($_POST['id'])){
					$requete=$bdd->prepare('SELECT idSolde FROM Produit WHERE idProduit=:idProduit');
					$requete->execute(array('idProduit' => $id));
					$idSoldeProduit=$requete->fetch();
					$idSoldeProduit=$idSoldeProduit['idSolde'];
					if($idSoldeProduit!=null){
						$requete=$bdd->prepare('DELETE FROM Solde WHERE idSolde='.$idSoldeProduit);
						$requete->execute();
					}
				}
				if(isset($_FILES['urlImage'])){
					$fichier='images/produit/'.$id.strtolower(strrchr(basename($_FILES['urlImage']['name']),'.'));
					if(file_exists($fichier))unlink($fichier);
					if (!$_FILES['urlImage']['error'] > 0){
						if ($_FILES['urlImage']['size'] <= 15000000){
							/*if(strrchr($fichier),'.'=='.png')*/$reussi=move_uploaded_file($_FILES['urlImage']['tmp_name'],$fichier);
						}
					}
				}
				if(isset($_FILES['urlPdf'])){
					$fichier='./pdf/produit/'.$id.strtolower(strrchr(basename($_FILES['urlPdf']['name']),'.'));
					if(file_exists($fichier))unlink($fichier);
					if (!$_FILES['urlPdf']['error'] > 0){
						if ($_FILES['urlPdf']['size'] <= 10000000){
							/*if(strrchr($fichier),'.'=='.png')*/$reussi=move_uploaded_file($_FILES['urlPdf']['tmp_name'],$fichier);
						}
					}
				}
				$requete=$bdd->prepare('DESCRIBE Produit');
				$requete->execute();
				if(isset($_POST['id'])){
					$req='UPDATE Produit SET';
					while($POST=$requete->fetch())if(substr($POST['Field'],0,2)!="id")$req=$req.' '.$POST['Field'].'=\''.utf8_decode(addslashes($_POST[$POST['Field']])).'\',';
					$req=$req.'idSolde='.$idSolde.' WHERE idProduit = '.$id;
					$requete=$bdd->prepare($req);
					$requete->execute();
					$requete=$bdd->prepare('DELETE FROM SecteurActiviteParProduit WHERE idProduit=:idProduit');
					$requete->execute(array('idProduit' => $id));
					$requete=$bdd->prepare('DELETE FROM ProduitParCategorie WHERE idProduit=:idProduit');
					$requete->execute(array('idProduit' => $id));
					$requete=$bdd->prepare('DELETE FROM AvantageParProduit WHERE idProduit=:idProduit');
					$requete->execute(array('idProduit' => $id));
					$requete=$bdd->prepare('DELETE FROM RealisationParProduit WHERE idProduit=:idProduit');
					$requete->execute(array('idProduit' => $id));
				}
				else{
					$req='INSERT INTO Produit Values (\'\'';
					while($POST=$requete->fetch())if(substr($POST['Field'],0,2)!="id")$req=$req.',\''.utf8_decode(addslashes ($_POST[$POST['Field']])).'\'';
					$req=$req.','.$idSolde.')';
					$requete=$bdd->prepare($req);
					$requete->execute();
				}
				if(isset($_POST['SecteurActivite'])){
					foreach($_POST['SecteurActivite'] as $secteurActiviteSelectionne){
						$requete=$bdd->prepare('INSERT INTO SecteurActiviteParProduit Values (:idProduit,:idActivite)');
						$requete->execute(array('idProduit' => $id, 'idActivite' => $secteurActiviteSelectionne));
					}
				}
				if(isset($_POST['Avantage'])){
					foreach($_POST['Avantage'] as $avantageSelectionne){
						$requete=$bdd->prepare('INSERT INTO AvantageParProduit Values (:idProduit,:idAvantage)');
						$requete->execute(array('idProduit' => $id, 'idAvantage' => $avantageSelectionne));
					}
				}
				if(isset($_POST['Categorie'])){
					foreach($_POST['Categorie'] as $categorieSelectionne){
						$requete=$bdd->prepare('INSERT INTO ProduitParCategorie Values (:idProduit,:idCategorie)');
						$requete->execute(array('idProduit' => $id, 'idCategorie' => $categorieSelectionne));
					}
				}
				if(isset($_POST['Realisation'])){
					foreach($_POST['Realisation'] as $realisationSelectionne){
						$requete=$bdd->prepare('INSERT INTO RealisationParProduit Values (:idProduit,:idRealisation)');
						$requete->execute(array('idProduit' => $id, 'idRealisation' => $realisationSelectionne));
					}
				}
				header('Location: /produits.php');
		}
		if(isset($_GET['action'])&&($_GET['action']=='supprimer')&&(isset($_GET['id']))&& ctype_digit($_GET['id'])){
			$requete=$bdd->prepare('DELETE FROM Toner where idToner=:idToner');
			$requete->execute(array('idToner'=>$_GET['id']));
			header('Location: /admin.php');
		}
		if(isset($_GET['action'])&&$_GET['action']=='modifier'&&isset($_GET['id']) && ctype_digit($_GET['id'])){
			$requete=$bdd->prepare('SELECT * FROM Toner where idToner=:idToner');
			$requete->execute(array('idToner'=>$_GET['id']));
			$tonerAModifier=$requete->fetch();
			if($tonerAModifier!=null){
				$suppModif=2;
				$requete=$bdd->prepare('SELECT Imprimante.idImprimante FROM Imprimante, ImprimanteParToner WHERE (Imprimante.idImprimante=ImprimanteParToner.idImprimante) AND (ImprimanteParToner.idToner=:idToner)');
				$requete->execute(array('idToner' => $tonerAModifier['idToner']));
				$imprimanteTonerAModifier=$requete->fetchAll();
			}
		}
		else if(isset($_GET['action'])&&$_GET['action']=='creer'){
			$suppModif=1;
		}
		else $suppModif=0;
		$requete = $bdd->prepare('SELECT * FROM Toner');
		$requete->execute();
		$toner=$requete->fetchAll();
		$requete=$bdd->prepare('SELECT * FROM Imprimante');
		$requete->execute();
		$imprimante=$requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr-CH">
<head>
	<meta charset="utf-8" />
	<title>Admin - cepm.ch</title>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="assets/css/font-awesome.min.css" />
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" />
	<link rel="stylesheet" href="assets/css/ace.min.css" />
</head>
<body>
<script type="text/javascript">
	function visible(){
		info = document.getElementById("blocImprimante");
		info.style.visibility="visible";
	}
	$(document).ready(function() {
		$('#example').dataTable();
	} );
</script>
<?php
	include "includes/menuhaut.php";
	include "includes/menu.php";
	global $suppModif;
	$requete = $bdd->prepare('SELECT * FROM Toner');
	$requete->execute();
	$toner=$requete->fetchAll();
?>
	<div class="main-content">
		<div class="breadcrumbs" id="breadcrumbs">
			<ul class="breadcrumb">
				<li><i class="icon-home home-icon"></i><a href="index.php">Accueil</a></li>
				<li class="active">Base de données</li>
			</ul>
		</div>
		<div class="page-content">
			<div class="row">
				<div class="col-xs-12">
					<h3 class="header smaller lighter blue">Toners enregistrés sur cepm.ch</h3>
					<div class="table-responsive">
											<table id="sample-table-2" class="table table-striped table-bordered table-hover dataTable" aria-describedby="sample-table-2_info">
												<thead>
													<tr role="row">
														<th class="sorting_desc" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 150px;" aria-sort="descending" aria-label="Code barre: activer pour trier">Code barre</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 108px;" aria-label="Modèle: activer pour trier">Modèle</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 118px;" aria-label="Stock disponible: activer pour trier">Stock disponible</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 178px;" aria-label="Stock minimum : activer pour trier">Stock minimum</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 145px;" aria-label="Tête d'impression: activer pour trier">Tête d'impression</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 118px;" aria-label="Couleur: activer pour trier">Couleur</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 178px;" aria-label="Lieu : activer pour trier">Lieu</th>
														<th class="hidden-480 sorting" role="columnheader" tabindex="0" aria-controls="sample-table-2" rowspan="1" colspan="1" style="width: 145px;" aria-label="Status: activer pour trier">Status</th>
														<th class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width: 156px;" aria-label="">Actions</th></tr>
												</thead>
												<tbody role="alert" aria-live="polite" aria-relevant="all">
													<tr class="odd">
														<?php for($a=0;$a<count($toner);$a++){ ?>
														<td class="center">
															<label>
																<input type="checkbox" class="ace">
																<span class="lbl"></span>
															</label>
														</td>

														<td class=" sorting_1"><?php echo $toner[$a]['idToner']; ?></td>
														<td class="hidden-480 "><?php echo $toner[$a]['numModel']; ?></td>
														<td class="hidden-480 "><?php echo $toner[$a]['stock']; ?></td>
														<td class="hidden-480 "><?php echo $toner[$a]['stockMin'] ?></td>
														<td class="hidden-480 "><?php if($toner[$a]['teteImpression']==1)echo 'oui';else echo'non'; ?></span></td>
														<td class="hidden-480 "><?php echo $toner[$a]['couleur'] ?></td>
														<td class="hidden-480 ">
															<?php
																for($b=0;$b<strlen($toner[$a]['idLieu']);$b++){
																	if($b>0)echo' et ';
																	switch(substr($toner[$a]['idLieu'], $b, 1)){
																		case '1': echo'CEPM: Armoire 1';
																			break;
																		case '2': echo'CEPM: Armoire 2';
																			break;
																		case '3': echo'CEPM: Armoire 3';
																			break;
																		case '4': echo'CEPM: Armoire 4';
																			break;
																		case '5': echo'Gymnase: Armoire 1';
																			break;
																	}
																}
															?>
														</td>
														<td class="">
															<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
																<a class="blue" href="#">
																	<i class="icon-zoom-in bigger-130"></i>
																</a>

																<a class="green" href="#">
																	<i class="icon-pencil bigger-130"></i>
																</a>

																<a class="red" href="#">
																	<i class="icon-trash bigger-130"></i>
																</a>
															</div>

															<div class="visible-xs visible-sm hidden-md hidden-lg">
																<div class="inline position-relative">
																	<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown">
																		<i class="icon-caret-down icon-only bigger-120"></i>
																	</button>

																	<ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
																		<li>
																			<a href="#" class="tooltip-info" data-rel="tooltip" title="View">
																				<span class="blue">
																					<i class="icon-zoom-in bigger-120"></i>
																				</span>
																			</a>
																		</li>

																		<li>
																			<a href="#" class="tooltip-success" data-rel="tooltip" title="Edit">
																				<span class="green">
																					<i class="icon-edit bigger-120"></i>
																				</span>
																			</a>
																		</li>

																		<li>
																			<a href="#" class="tooltip-error" data-rel="tooltip" title="Delete">
																				<span class="red">
																					<i class="icon-trash bigger-120"></i>
																				</span>
																			</a>
																		</li>
																	</ul>
																</div>
															</div>
														</td>
													</tr>
													<?php } ?>
													</tbody></table><div class="row"><div class="col-sm-6"><div class="dataTables_info" id="sample-table-2_info">Showing 1 to 23 of 23 entries</div></div><div class="col-sm-6"><div class="dataTables_paginate paging_bootstrap"><ul class="pagination"><li class="prev disabled"><a href="#"><i class="icon-double-angle-left"></i></a></li><li class="active"><a href="#">1</a></li><li class="next disabled"><a href="#"><i class="icon-double-angle-right"></i></a></li></ul></div></div></div></div>
										</div>
										
					<div class="table-responsive">
						<table id="matable" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<td>Code barre</td>
									<td>Modèle</td>
									<td>Stock disponible</td>
									<td>Stock minimum</td>
									<td>Tête d'impression</td>
									<td>Couleur</td>
									<td>Lieu</td>
									<td>Imprimante</td>
									<td>Actions</td>
								</tr>
							</thead>
							<tbody>
								<?php for($a=0;$a<count($toner);$a++){ ?>
									<tr>
										<td><?php echo $toner[$a]['idToner'] ?></td>
										<td class="hidden-480"><?php echo $toner[$a]['numModel'] ?></td>
										<td><?php echo $toner[$a]['stock'] ?></td>
										<td><?php echo $toner[$a]['stockMin'] ?></td>
										<td><?php if($toner[$a]['teteImpression']==1)echo 'oui';else echo'non'; ?></td>
										<td><?php echo $toner[$a]['couleur'] ?></td>
										<td>
											<?php
												for($b=0;$b<strlen($toner[$a]['idLieu']);$b++){
													if($b>0)echo' et ';
													switch(substr($toner[$a]['idLieu'], $b, 1)){
														case '1': echo'CEPM: Armoire 1';
															break;
														case '2': echo'CEPM: Armoire 2';
															break;
														case '3': echo'CEPM: Armoire 3';
															break;
														case '4': echo'CEPM: Armoire 4';
															break;
														case '5': echo'Gymnase: Armoire 1';
															break;
													}
												}
											?>
										</td>
										<td>
											<?php
												$requete=$bdd->prepare('SELECT Imprimante.modele FROM Imprimante, ImprimanteParToner WHERE (Imprimante.idImprimante=ImprimanteParToner.idImprimante) AND (ImprimanteParToner.idToner=:idToner)');
												$requete->execute(array('idToner' => $toner[$a]['idToner']));
												$b=0;
												while($donnees=$requete->fetch()){
													$b++;
													if($b==1)echo utf8_encode($donnees['modele']);
													else echo ', '.utf8_encode($donnees['modele']);
												}
											?>
										<td>
											<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
												<a class="green" href="<?php echo '/toner/admin.php?id='.$toner[$a]['idToner'].'&action=modifier';?>">
													<i class="icon-pencil bigger-130" title="modifier"></i>
												</a>
												<a class="red" href="<?php echo '/toner/admin.php?id='.$toner[$a]['idToner'].'&action=supprimer';?>">
													<i class="icon-trash bigger-130" title="supprimer"></i>
												</a>
											</div>
										</td>
									</tr>
								<?php }?>
							</tbody>
						</table></br></br>
						<?php if($suppModif>0){ ?>
							<form method="POST" enctype="multipart/form-data">
								<div class="col-xs-4">
									<div id="signup-box" class="signup-box widget-box no-border">
										<div class="widget-body">
											<class="widget-main">
												<h4 class="header green lighter bigger">
													<i class="icon-pencil blue"></i>
													<?php if($suppModif==2){
														echo'Modification de "'.utf8_encode($tonerAModifier['numModel']).'"';
														echo'<input type="hidden" name="id" value="'.$_GET['id'].'">';
													}
													else echo'Nouveau toner';
													?>
												</h4>
												<div class="space-6"></div>
												<div>
													<?php
														$requete = $bdd->prepare('DESCRIBE Produit');
														$requete->execute();
														while($entete=$requete->fetch()){
															$a++;
															if(substr($entete['Field'],0,2)!="id" && $entete['Field']!='statut'){
													?>
																<label class="block clearfix">
																	<span class="block input-icon input-icon-right">
																	<p><?php echo utf8_encode(ucfirst($entete['Field']));?></p><input type="<?php if($entete['Field']=="prix")echo'number';else echo'text';?>" class="form-control" placeholder="<?php if($suppModif==1)echo $tableau[$a-1]?>" <?php if ($a==5 || $a==6 || $a==7)echo"required"?> name="<?php echo utf8_encode($entete['Field']);?>" <?php if($suppModif==2)echo 'value="'.utf8_encode($tonerAModifier[$a]).'"';?>>
																	</span>
																</label>
														<?php }} ?>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<p>Code barre</p>
															<input type="text" name="idToner"<?php if($suppModif==2)echo ' value="'.$tonerAModifier['idToner'].'"'; ?>>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<p>Modèle</p>
															<input type="text" name="numModel"<?php if($suppModif==2)echo ' value="'.$tonerAModifier['numModel'].'"'; ?>>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<p>Stock</p>
															<input type="number" name="stock"<?php if($suppModif==2)echo ' value="'.$tonerAModifier['stock'].'"'; ?>>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<p>Stock minimum</p>
															<input type="number" name="stockMin"<?php if($suppModif==2)echo ' value="'.$tonerAModifier['stockMin'].'"'; ?>>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<p>Tete d'impression</p>
															<select name="select">
																<option <?php if($tonerAModifier['teteImpression']==1 &&$suppModif==2)echo 'selected="selected"'; ?>>oui</option>
																<option <?php if($tonerAModifier['teteImpression']==0 &&$suppModif==2)echo 'selected="selected"'; ?>>non</option>
															</select>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" name="couleur"<?php echo ' value="'.$tonerAModifier['couleur'].'"'; ?>>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<p>Lieu</p>
															<?php for($d=1;$d<5;$d++){ ?>
																<label><input type="checkbox" name="idLieu[]" value="<?php echo $d ?>" <?php for($c=0;$c<strlen($tonerAModifier['idLieu']);$c++)if(substr($toner[$a]['idLieu'], $c, $d))echo' checked="checked"'; ?>><span class="lbl">CEPM: Armoire</span> <?php echo $d ?></label><br>
															<?php } ?>
															<label><input type="checkbox" name="idLieu[]" value="5" <?php for($c=0;$c<strlen($tonerAModifier['idLieu']);$c++)if(substr($toner[$a]['idLieu'], $c, 5))echo' checked="checked"'; ?>><span class="lbl">Gymnase: Armoire 1</span></label><br>
														</span>
													</label>
													<a href="#" onclick="visible();return false;">Gérer les imprimantes pour le toner "<?php echo $tonerAModifier['numModel'] ?>"</a>
													<button type="submit" name="ajouter" class="width-65 pull-right btn btn-sm btn-success">
														Ajouter
														<i class="icon-arrow-right icon-on-right"></i>
													</button>
													<div id="blocImprimante" style="visibility:hidden">
														<h4 class="header green lighter bigger">
															<i class="icon-pencil blue"></i>
															Imprimante(s)
														</h4>
														<?php for($d=0;$d<count($realisation);$d++){ ?>
															<label>
																<input name="imprimante[]" value="<?php echo $imprimante[$d]['idImprimante']?>" <?php if($suppModif==2)for($b=0;$b<count($imprimanteTonerAModifier);$b++)if($imprimanteTonerAModifier[$b]['idImprimante']==$imprimante[$d]['idImprimante'])echo'checked'; ?> type="checkbox" class="ace">
																<span class="lbl"> <?php echo utf8_encode($imprimante[$d]['modele']);?></span>
															</label>
															</br>
														<?php } ?>
													</div>
												</div><!--/widget-body-->
										</div><!-- /signup-box-->	
									</div>
								</div>
							</form>
						<?php }
						else if($suppModif==0){?>
							<a href="/toner/admin.php?action=creer">
								<button class="btn btn-lg btn-success">
									<i class="icon-pencil"></i>
									Ajouter un nouveau toner
								</button>
							</a>
						<?php }?>
					</div>
				</div>
			</div>
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div>
	</body>
</html>