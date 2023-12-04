<?php
session_start(); 
if($_SESSION['isLocalMode']){
	include('inc/dev/variablejoomla-dev.php');
}
else {
	include('inc/variablejoomla.php');	
}	
include('../panier_tools/connectbd.php');
include('inc/allow-connexion.php');
// include('../panier_tools/ControllerUser.php');
include('controller/ControllerUser.php');


if(!isset($_SESSION['idla'])|| !is_numeric($_SESSION['idla'])){
	$mess="error no id";
	// if(isset($_SESSION['messidpwd'])){
	// 		$mess .=$_SESSION['messidpwd'];
	// }
	$_SESSION['messidpwd2']= $mess;
	//header("location:panierform.php");
}

include('inc/globalVariables.php');
include("controller/RecapitulatifController.php");
if(isset($_SESSION['hasUpdateEnfant'])){unset($_SESSION['hasUpdateEnfant']);}

$RecapitulatifController = new RecapitulatifController($db);
$limiteQuotientCaf = 960; // le rendre dynamique ?



//effacer une inscription ou ajouter caf
$action = (isset($_POST['action'])? $_POST['action']:  (isset($_GET['action'])? $_GET['action']:null )) ;
 switch($action){
      	// Case "effacer":
		// 	$lgncde= (isset($_POST['l'])? $_POST['l']:  (isset($_GET['l'])? $_GET['l']:null )) ;
		// 	$RecapitulatifController->deleteInscr($lgncde);
		// 	break;
		 
		Case "ajoutCaf":
			if(isset($_SESSION["orderid"]) && $_SESSION["orderid"] > 0){
				if(isset($_FILES['fichierCaf'])){
					$reductionCaf=$RecapitulatifController->ajoutCaf($_POST, $limiteQuotientCaf, $_FILES['fichierCaf']);
				}	
				else {
					$reductionCaf= array('ok'=> false, 'rep' => '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>Réduction non applicable : pas de justificatif envoyé</div>');
				}
			}
			else { $reductionCaf= array('ok'=> false, 'rep' => '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>Réduction non applicable : error no session</div>');}
			break;
}

//-------------------------------
//-----ENREGISTREMENT PANIER-----
//-------------------------------
//enregistrement du panier actuel

if(!isset($_SESSION['verrou'])){
		$RecapitulatifController->ajoutNewpanier();
	}
else {
	$erreur= 'Le panier est vide';
}

//On recupere les données inscr apres traitement
$inscr = $RecapitulatifController->getInscr();
$stopEnregistrement = array('action' => false , 'errors'=> array());

$hasSejour = $inscr['hasSejour'];
//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');

?>


<div id="panier" class="recap">
<?php
	if (isset($_SESSION['mess'])){
		echo ' '.$_SESSION['mess'].' '; unset($_SESSION['mess']);
		echo '<br/>';
	}
		if (isset($_SESSION['mess'])){
	echo $_SESSION['err'];
		}
?>
	<h2>Récapitulatif</h2>
<?php
	include('inc/bloc/bloc-info-client.php');
?>
	<a href="identification.php" class="btn btn-blue" >Modifier les informations personnelles</a>

	<div style="display:block; height:30px;"></div>
	<h2>S'inscrire</h2>
	
	<?php if ($inscr['mailEnv']):?>
		<div class="bg-success text-success"><p style="padding:5px;">Nous avons déjà envoyé un mail concernant cettte inscription. Si vous n'avez pas reçu de mail, merci de vérifier les courriers indésirables et votre adresse mail. Si le problème persiste n'hésitez pas à nous contacter par email ou téléphone.<br/> Vous avez toutefois encore la possibilité de modifier les informations, nous prendrons en compte le dernier mail envoyé pour l'inscription.</p></div>
	<?php endif;?>

<?php

//-----------------------------------------------------------------------------------
//--------------------------     PANIER      ----------------------------------------
//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------
// BLOC CAF
//-----------------------------------------------------------------------------------

if (!$inscr['noCafReduc'] && $inscr['hasSejour'] && $limiteQuotientCaf > 0): ?>
	<div class="block-caf form-block" id="block-caf" data-limitecaf="<?php echo $limiteQuotientCaf;?>">
		<h4>Réduction EE64</h4>

		<?php if($inscr['hasCaf'] || isset($reductionCaf)) {
					echo $reductionCaf['rep']; 
					if ($reductionCaf['ok']) { 
						echo '<i>D\'après vos déclarations, vous pouvez bénéficier d\'une réduction, les prix des séjours qui proposent une réduction sont mises à jour dans votre panier.***</i><br/><br/>'; 
					} 
				}
			
		?>

		<p>Votre quotient familial de la Caf peut vous permettre de bénéficier d'une réduction sur les séjours de vacance si celui ci est inférieur à <?php echo $limiteQuotientCaf; ?>, si vous pensez que vous pouvez en bénéficier merci d'entrer votre quotient famililal Caf dans l'encart :</p>
		<form method="post" action="recapitulatif.php" data-toggle="validator" enctype="multipart/form-data">
			<div class="form-group">
				<input type="text" name="quotientCaf" id="quotientCaf" required class="req">
			</div>
			<div class="btn btn-blue verifcaf" style="margin:10px;"><span class="glyphicon glyphicon-arrow-right"></span> Verifier</div>

			<div class="block-to-open">
						<p> Votre quotient familial de la Caf vous permet de bénéficier d'une réduction, merci de télécharger un justificatif précisant votre quotient familial emanant de la CAF afin
d'appliquer la réduction, puis cliquez sur appliquer :</p>
				<div class="form-group">
					<input class="form-control req" type="file" name="fichierCaf" required />
				</div>
				<input type="hidden" value="ajoutCaf" name="action"/>
				<div class="form-group">
					<input class="btn btn-blue sub-form" style="margin:10px;" type="submit" value="Appliquer"/>
				</div>
			</div>
			<div class="block-to-open">
				<p>Vous ne pouvez pas bénéficier d'une réduction.</p>
			</div>
		</form>
	</div>
<?php endif; 



// BOUCLE INSCRIPTIONS
if (count($inscr["inscriptions"]) <= 0 ):?>
	<p>Le panier est vide</p>
	<p>Si vous venez d'effectuer un paiement en ligne pour cette inscription, celle ci sera désormais consultable dans <a href="https://education-environnement-64.org/espace_perso/">Mon compte</a>.</p>
	</table>
<?php else : 

$cssCaf= ($inscr['hasCaf']? 'hasCaf':'');?>
<div class="no-justificatifcaf"> ! Attention vous n'avez pas téléchargé votre justificatif Caf, la réduction n'est pas appliquée.</div>
<form method="post" action="paiement.php" role="form" class="with-dates <?php echo $cssCaf;?>" data-toggle="validator">
	<table>	
		<!--//les libeles du tableau-->
		<tr id="fondpan"><td class="tdlibele">Séjour</td>
			<?php if($inscr['hasSejour']) { echo '<td>Transport *</td>';} ?>
			<td>Prix</td>
			<?php if($inscr['hasSejour']) { echo '<td>Total</td>';} ?>
			<!--<td>Effacer</td>-->
		</tr>

		<?php
		$orderid=0;
		$prixTotal = 0;
		$enfantAdded =0;
		$totalTransp = 0;
		for($i=0; $i<count($inscr["inscriptions"]); $i++):
			$ligne = $inscr["inscriptions"][$i]; 
			$orderid= $ligne['ins']->orderid();
			$prixLigne = 0;
			$dataPriceLigne = '';
			?>
			<?php if ($ligne['ins']->id_article()>100) : //sejour?>
				<?php 
				 $prixD1 = (($inscr['hasCaf'] && $ligne['sej']->prix_reducCaf()>0 )? $ligne['sej']->prix_reducCaf() : $ligne['sej']->prix());
				 $prixD2 = (($inscr['hasCaf'] && $ligne['sej']->prix_reducCafD2()>0) ? $ligne['sej']->prix_reducCafD2() : $ligne['sej']->prix_duree2());
				if($ligne['ins']->duree2()>1){
					$prixLigne = (($inscr['hasCaf'] && $ligne['sej']->prix_reducCaf()>0 ) ? $ligne['sej']->prix_reducCafD2(): $ligne['sej']->prix_duree2());
				}
				else{
					$prixLigne = (($inscr['hasCaf'] && $ligne['sej']->prix_reducCaf()>0 ) ? $ligne['sej']->prix_reducCaf() : $ligne['sej']->prix());					
				}
				?>
				<?php $dataPriceLigne= ' data-priced2="'.$prixD2.'" data-price="'.$prixD1.'" '; 
					$dataPriceLigne.= ' data-transport="'.$ligne['ins']->transport().'" data-prixligne="'.$prixLigne.'"';?>
				<tr class='ligneInsc sejour' data-id="<?php echo $i;?>" <?php echo $dataPriceLigne;?>>
			<?php else: ?>
			<?php $dataPriceLigne.= ' data-transport="0" data-prixligne="'.$ligne['sej']->prix().'" ';?>
				<tr class='ligneInsc' data-id="<?php echo $i;?>" <?php echo $dataPriceLigne;?>>

			<?php endif; ?>
				<td class="tdlibele bleu">
					<div class="intitule"><span ><?php echo $ligne['sej']->intitule(); ?></span></div>
						
					<div class="block">
						<h4>Dates</h4>
						<?php //DATES 
						echo $ligne['htmlDates']; ?>
					</div>

					<?php if ($ligne['ins']->id_article()<100 && $noddjs) { 
						//echo '<p> Merci de renseigner les champs suivant</p>';
						echo $RecapitulatifController->getHtmlBafa($ligne['sej']->intitule(), $time, $ddjs);
						$noddjs = false;
					} ?>


					<?php if ($ligne['ins']->id_article()>100) : //sejour?>
					<div class="block form-group">
						<h4>Enfant</h4>
						<?php $j=0; if (count($inscr['enfants'])>0): ?>
							<select class="insc_enf form-control" name="insc_enf<?php echo $ligne['ins']->ligne_cde(); ?>" >
								<?php if( $ligne['ins']->id_enf() >0 ){ echo '<option value="'.$ligne['ins']->id_enf().'">'.$ligne['ins']->enfant().'</option>'; }
								$nbDisabled = 0;
								for ($j=0; $j<count($inscr['enfants']); $j++){
										$disabled = '';
										if (($ligne['sej']->age_min()-1) >= $inscr['enfants'][$j]->age() || ($ligne['sej']->age_max()+1) <= $inscr['enfants'][$j]->age()) {
											$disabled = 'disabled';
											$nbDisabled++;
										}
										echo '<option '.$disabled.' value="'.$inscr['enfants'][$j]->enf_enf_id().'">'.$inscr['enfants'][$j]->enf_nom().' '.$inscr['enfants'][$j]->enf_prenom().' '.$inscr['enfants'][$j]->age().' ans </option>';
									} ?>
								</select>
								<span class="infoEnfant">i</span>
								<div class="infoBulle">Pour changer les informations sur les enfants inscrits <a href="./identification.php">cliquez ici</a></div>
							<?php 
							 else :
								 $hasNoEnfant =true;
							endif; ?>
							<div>
								<?php 
									$compt=$j +$enfantAdded; $errorMessDate = '';
									include('inc/html/bloc-inscr-enfant.php');
									$enfantAdded++;
								?>
							</div>
							<p><i>Tranche d'âge du séjour : <?php echo $ligne['sej']->age_min().' - '.$ligne['sej']->age_max(); ?></i></p>

							<!--<h4 class="btn btn-blue">
									<span class="glyphicon glyphicon-plus-sign"></span>
									<a href="panier.php3">Inscrire un autre enfant au séjour</a>
							</h4>-->
						</div>
						<div class="block">
							<h4>Transport*</h4>
							<select  name="transp<?php echo $ligne['ins']->ligne_cde(); ?>" class="select-transp form-control" >
								<?php	
									if($ligne['ins']->id_transp() > 0) { echo'<option value="'.$ligne['ins']->id_transp().'" selected="selected" data-price="'.$ligne['ins']->transport().'">'.$ligne['ins']->transportLieu().' '.$ligne['ins']->transport().'&#8364</option>';}
									for ($j=0; $j<count($inscr['transport']); $j++){
										$classCss = ($inscr['transport'][$j]->duree2()==0 ? 'class="disable"' : '');
										echo'<option '.$classCss.' value="'.$inscr['transport'][$j]->id_transp().'" data-price="'.$inscr['transport'][$j]->prix_transp().'">'.$inscr['transport'][$j]->lieu().' '.$inscr['transport'][$j]->prix_transp().'&#8364</option>';
									} ?>
							</select>
						</div>
					</td>
					<td class="bleu">
						<div class="prix-transp">
							<div><?php echo $ligne['ins']->transportLieu(); ?>
							<div>
								<span class="price"><?php echo $ligne['ins']->transport(); ?> 
								</span>&#8364
							</div>
						</div>
					</td>
					<?php endif; ?>
			
				<?php
				//si c'est une formation et que il y a des gosses(donc un séjour quelque part)on ajoute des <td>
					if($inscr['hasSejour'] && $ligne['ins']->id_article()<100 ) { echo '<td class="bleu"></td>';}
				?>
				<?php if ($ligne['ins']->id_article()>100) : ?>
					<td class="tdprix bleu">
							<div class="prix-duree1 <?php if($ligne['ins']->duree2()<2){ echo 'open';}?>">
								<?php 
									if($ligne['ins']->hasCaf() && $ligne['sej']->prix_reducCaf()>0) {
											echo '<small>Réduction EE64 comprise***</small>';
											echo '<span class="price">'.$ligne['sej']->prix_reducCaf().'</span>'; 
											//$prixLigne = $ligne['sej']->prix_reducCaf();
									} else { 
										if($ligne['ins']->hasCaf()){echo '<small>Réduction EE64 comprise***</small>';}
										echo '<span class="price">'.$ligne['sej']->prix().'</span>';
									//$prixLigne = ($ligne['ins']->duree2()>1 ? $ligne['sej']->prix_duree2():$ligne['sej']->prix());
									}
									?>  &#8364
							</div>
							<div class="prix-duree2 <?php if($ligne['ins']->duree2()>1){ echo 'open';}?>">
								<?php 
									if($ligne['ins']->hasCaf() && $ligne['sej']->prix_reducCaf()>0) {
											echo '<small>Réduction EE64 comprise***</small>';
											echo '<span class="price">'.$ligne['sej']->prix_reducCafD2().'</span>'; 
										//	$prixLigne = $ligne['sej']->prix_reducCaf();
									} else { 
										if($ligne['ins']->hasCaf()){echo '<small>Réduction EE64 comprise***</small>';}
										echo '<span class="price">'.$ligne['sej']->prix_duree2().'</span>';
								//	$prixLigne = ($ligne['ins']->duree2()>1 ? $ligne['sej']->prix_duree2():$ligne['sej']->prix());
									}
									?>
								&#8364
							</div>  						 
					</td>
				<?php else :
					$prixLigne = $ligne['sej']->prix();
					if($inscr['hasSejour']) { echo '<td class="bleu"></td>';} ?>
				
				<?php endif; ?>

				<td class="col_price"><span class="border-dashed"><span class="ligne-price price" data-price="<?php echo ($prixLigne + $ligne['ins']->transport()); ?>"><?php echo ($prixLigne + $ligne['ins']->transport()); ?></span>&#8364</span></td>
				
				<!--<td class='suppr'>
					<span class="hideDesktop"><i>Effacer </i></span>
					<a href=<?php //echo htmlspecialchars("recapitulatif.php?action=effacer&l=".rawurlencode( $ligne['ins']->ligne_cde()));?>>
						<img src="../images/banners/bin.png"/>
					</a>
				</td>-->
				</tr>
				<tr class="space"></tr>
				<?php $multiple2 = $ligne['ins']->multiple();?>
				
			<?php $prixTotal += $prixLigne; 
				  $totalTransp += $ligne['ins']->transport();


					endfor; ?>


			</table>
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="orderid" value="<?php echo $orderid;?>">

			<div class="block-prix">
			
				<?php // inscription multiple
				if($inscr['hasSejour']): ?>
						<?php 
								$cssMultiple= '';
								$remise= ($prixTotal*5)/100;
								$prixavantReduc = $prixTotal;
								//echo $multiple2 ;
								if(count($inscr["inscriptions"])>1 || $multiple2 != null){
										$cssMultiple=  'hasMultiple';
										$prixTotal-=$remise; 
										$cssMultiple = (count($inscr["inscriptions"])>1?$cssMultiple.' mult' : $cssMultiple.' ami' );
								}
							?>
						<div class="block-multiple <?php echo $cssMultiple;?>">
							<div class="block-Caf-sanspdf" id="block-Caf-sanspdf">
								<p><i>Réduction Caf non appliquée !</i>
									<br/>Vous avez renseigné votre quotient familial mais n'avez pas envoyé le justificatif demandé.. Rendez vous dans l'encart 'Reduction ee64' plus haut sur la page pour envoyer votre justificatif.
								</p>
							</div>
							<div class="block-mult-insc">
								<p><i>L'option inscription multiple **</i><br/>Vous bénéficiez d'une réduction sur le prix des séjours
									car vous achetez plus d'un séjour.
								</p>
							</div>
							<div class="block-mult-ami">
								<p><i>L'option inscription multiple **</i><br/>
									Elle est automatique si vous achetez plus d'un séjour, mais vous pouvez en bénéficier en entrant 
									le nom et prénom d'un(e) copain(e) de votre enfant qui participe au séjour.
								</p>
								<small><i>Merci d'entrer un nom et un prénom</i></small>
								<input class="form-control" id="mul" type="text" name="multiple" <?php if($multiple2 != null){ echo 'value="'.$multiple2.'"';} ?>/>	
								<div class="btn btn-blue updateSoustotal" style="margin:10px;"><span class="glyphicon glyphicon-arrow-right"></span> Mettre à jour le total</div>					
							</div>
							<p class="ligne">Réduction de 5%(sur <span id="prixavantReduc"><?php echo $prixavantReduc;?></span>&#8364) : <span class="lig"><span id="remise"><?php echo $remise;?></span>&#8364</span></p>
							<p class="ligne">Sous Total : <span class="lig"><span id="soustotal"><?php echo $prixTotal;?></span>&#8364</span></p>
						</div>
						<p class="ligne">Transport : <span class="lig"><span id="totaltransp"><?php echo $totalTransp;?></span>&#8364</span></p>
				<? endif; ?>
			
				<?php // CONDITION PAGE SUIVANTE 
				if ($inscr['stopComplet']){
						$stopEnregistrement['action'] = true;
						$stopEnregistrement['errors'][] = "<i>article complet, plus de dates disponibles</i>";
				}
				else {
					echo '<p id="prixTotal" class="ligne">Total : <span class="lig"><span id="totaltotal">'.($prixTotal +$totalTransp).'</span>&#8364</span></p>';
				}
				
				if( !$stopEnregistrement['action']) :?>
					<button type="submit" class="btn btn-blue sub-form"><span class="glyphicon glyphicon-arrow-right"></span> Enregistrer</button>
				<?php else : 
					for( $i=0; $i < count($stopEnregistrement['errors']) ; $i++){
						echo $stopEnregistrement['errors'][$i];
					}?>
					<div class="btn btn-blue"><span class="glyphicon glyphicon-arrow-right"></span> Enregistrer <i>désactivé</i></div>
				<?php endif;?>	
			
			</div>


<?php // fin condition count($inscriptions)
	endif;?>	

</form>


<?php
//topo transport pr séjour
if($inscr['hasSejour']):?>
		<div class="infosOptSejour">

			<h3>>> L'option transport *</h3>
			<p>Depuis Pau ou directement au centre : Gratuit</p>
			<p>Depuis Bordeaux: aller-retour : 84 &#8364 ; aller simple ou retour simple : 42 &#8364 </p>
			<p>Depuis Paris: aller-retour : 168 &#8364 ; aller simple ou retour simple : 84 &#8364 <br/></p>
			
			<h3>>> L'option inscription multiple **</h3>
			<p>Une inscription conjointe avec un(e) copain(e) ou un membre de sa famille donne droit à 5% de réduction.
				(Les nom et prénom sont donnés à titre informatif et ne constitue pas une inscription.)</p>
			<?php if($inscr['hasCaf']): ?>
				<h3>>> La réduction EE64 ***</h3>
				<p>Les prix affichés et la réduction EE64 sont sous réserve de votre quotient familial CAF, ils vous seront confirmés une fois votre justificatif étudié par nos soins.</p>
			<?php endif; ?>
			<div class="espace"></div>
		</div>
<?php endif; ?>


<div class="clr"></div>
<div class="espace"></div>

<a href="panier.php" class="preced"> <p> < Etape précedente</p> <img src="../templates/educ_env_2/images/panier_precedent.png"  width="136" height="95" alt="Etape précédente"/></a>
</div>


<?php 
$scriptJS = '<script type="text/javascript" src="../js/ftn.js"></script> ';
$scriptJS .= '<script type="text/javascript" src="../js/validator.js"></script> ';
$scriptJS .= '<script type="text/javascript" src="js/recap.js"></script>';
include('inc/html/footer.php');
?>
