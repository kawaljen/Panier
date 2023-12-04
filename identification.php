<?php
session_start();
include('../panier_tools/connectbd.php');

// include('../panier_tools/ControllerUser.php');
include('controller/ControllerUser.php');



//les actions possibles de la page : modif user modif enfant et ajout enfant via un champ hidden
// if(isset($_POST['action']))
// 	{
		if(isset($_POST['action-ajoutEn']))
			{$ajoutEnf=1;}

	// 	else if($_POST['action']==='ajoutddjs')
	// 		{$ajoutddjs=1;}
	// }



//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');

?>
<div id="panier" class="container identification">
	<?php
	//message d'erreur
	if(isset($message)){echo $message;}
	if(isset($_SESSION['messUpdate'])){echo $_SESSION['messUpdate']; unset($_SESSION['messUpdate']);}

//if(!isset($message)):
	// On récupère tout le contenu de la table site_user
	$query ='SELECT * FROM site_user WHERE id = ?';
	$prep = $db->prepare($query);
	$prep->bindValue(1, $_SESSION['idla'], PDO::PARAM_INT);
	$prep->execute();
	while ($donnees = $prep->fetch()): ?>
		<h2>Les informations enregistrées  </h2>

		<p style="font-style: italic;">Vérifiez les informations :</p>

		<form method="post" action="recapitulatif.php" role="form" class="maxWidthed with-dates" data-toggle="validator">
			<div class="form-block">

			<div class="f-editable">
				<div class="form-group">
					<label for="nom" class="control-label">Nom de famille* :</label>
					<input type="text" class="form-control" name="nom" id="inputName" placeholder="Nom de famille" value="<?php echo $donnees['nom']; ?>" required>
				</div>

				<div class="form-group">
					<label for="prenom" class="control-label">Prénom* :</label>
					<input type="text" class="form-control" name="prenom" id="inputFName" placeholder="Prénom"  value="<?php echo $donnees['prenom']; ?>" required>
				</div>

				<div class="form-group">
					<label for="prenom" class="control-label">Adresse* :</label>
					<input type="text" class="form-control" name="adress" id="inputAd" placeholder="Adresse" required value="<?php echo $donnees['adress']; ?>">
				</div>

				<div class="form-group">
					<label for="compl" class="control-label">Complement :</label>
					<input type="text" class="form-control" name="compl" id="inputComp" placeholder="Complement" value="<?php echo $donnees['complement_add']; ?>">
				</div>

				<div class="form-group">
					<label for="codep" class="control-label">Code postal* :</label>
					<input type="text" class="form-control" name="codep" id="inputPC" placeholder="Code postal" data-minlength="5" required value="<?php echo $donnees['codep']; ?>" data-error="5 caractères minimum.">
					<div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					<label for="ville" class="control-label">Ville* :</label>
					<input type="text" class="form-control" name="ville" id="inputCity" placeholder="Ville" required value="<?php echo $donnees['ville']; ?>">
				</div>

				<div class="form-group">
					<label for="telfixe" class="control-label">Tel principal* :</label>
					<input type="number" class="form-control" name="telfixe" id="inputTprinc" placeholder="Tel principal" data-minlength="9" data-error="Ce numéro semble invalide (min 10 caractères et chiffres)." required value="<?php echo $donnees['telfixe']; ?>">
					 <div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					<label for="telport" class="control-label">Tel secondaire :</label>
					<input type="number" class="form-control" name="telport" id="inputTSec" placeholder="Tel secondaire" value="<?php echo $donnees['telport']; ?>">
				</div>

				<div class="form-group">
					<label for="couriel" class="control-label">E-mail* :</label>
					<input type="email" class="form-control" name="couriel" id="inputEmail" placeholder="E-mail" required data-error="Cet email est invalide." value="<?php echo $donnees['email']; ?>" >
					<div class="help-block with-errors"></div>
					<input type="hidden" name="oldEmail" value="<?php echo $donnees['email']; ?>">
				</div>
			</div>

			<input type="hidden" name="action-modif" value="1">


			<?php for($i=0; $i<count($_SESSION['artid']['id']); $i++): ?>
				<?php if($_SESSION['artid']['id'][$i]<100): ?>
					<h3>Pour les formations </h3>

					<div class="form-group">
						<?php $time = strtotime($donnees['naiss_adult']); ?>
						<label for="datead_f" class="control-label">Date de naissance (Au format JJ/MM/AAAA):</label>
						<div class="form-group dates">
							<input class="form-control" type="number" required data-day="day" name="datead_j" value="<?php if(!empty($time)){ echo date('d', $time);} ?>" size="2"/>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group dates">
							<span>/</span><input class="form-control" type="number" required data-month="month" name="datead_m" value="<?php if(!empty($time)){ echo date('m', $time);} ?>" size="2"/>
							<div class="help-block with-errors"></div>
						</div>
						<div class="form-group dates">
							<span>/</span><input class="form-control" type="number" required data-year="year" name="datead_a" value="<?php if(!empty($time)){  echo date('Y', $time);} ?>" size="4"/>
							<div class="help-block with-errors"></div>
						</div>
					<!--	<div class="help-block with-errors">(Au format JJ/MM/AAAA)</div> -->
					</div>

					<div class="form-group">
						<label for="ddjs" class="control-label">Numéro de dossier DDJS :</label>
						<p>Le numéro de dossier DDJS est nécessaire pour les inscriptions à un BAFA approfondissment ou un BAFD.</p>
						<input type="text" class="form-control" name="ddjs" id="inputddjs" placeholder="DDJS" value="<?php echo $donnees['ddjs']; ?>" >
					</div>



					<?php $i= $i +100; $Vddjs=1; ?>
				<?php endif ?>
			<?php endfor ?>

		</div>
	<?php $_SESSION['idla']= $donnees['id'];
	 endwhile;

	$prep->closeCursor();
	$prep = NULL; // Termine le traitement de la requête


	//-------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------ENFANTS--------------------------------------------------
	//-------------------------------------------------------------------------------------------------------------------

	//on teste la présence du panier, car la suite nécessite sa présence
	if (isset($_SESSION['artid']['id'])):
		//Les enfants
		//On teste si il s'agit d'un séjour c'est à dire id inférieur à 100
		$Nodoublon= true;
		$nbArticles=count($_SESSION['artid']['id']);
		for($j=0;$j<$nbArticles;$j++): //au cas ou il y aurait une formation et un sejour : on verifie le tableau donc boucle.. ?>
			<?php if($_SESSION['artid']['id'][$j]>100 && !isset($Nodoublon2)):
				$errorMessDate = ' '; //data-error="Le format de la date est incorrect, ex: 14/05/2010" ?>

				<h2> Pour les séjours de vacances : enfants enregistrés</h2>
				<p style="font-style: italic;">Enregistrer les informations de l'enfant à inscrire ci dessous.<br/><br/></p>

				<?php
				// On récupère tout le contenu de la table site_enfant
				$Nodoublon2=false;
				$query ='SELECT * FROM site_enfant WHERE enf_id= ?';
				$prep = $db->prepare($query);
				$prep->bindValue(1, $_SESSION['idla'], PDO::PARAM_STR);
				$prep->execute();
				$compt=0;

				while ($f_donnees = $prep->fetch()): ?>
					<?php if (isset($f_donnees['enf_nom'])):
						$time = strtotime($f_donnees['enf_birthday']);

						?>

						<div class="form-block">
							<!-- <div class="non-editable">
								<p><strong>Nom de Famille de l'enfant : </strong> <?php // echo $f_donnees['enf_nom']; ?></p>
								<p><strong>Prénom : </strong><?php // echo $f_donnees['enf_prenom']; ?></p>
								<p><strong><?php //echo $f_donnees['enf_gender']; ?></strong></p>
								<p><strong>Date de naissance : </strong><?php //echo $f_donnees['jour']; ?>/<?php //echo $f_donnees['mois']; ?>/<?php echo $f_donnees['annee']; ?></p>
								<br/><span class="toogle-hide btn btn-blue"><span class="glyphicon glyphicon-edit"></span>  Editer</span>
							</div> -->
							<div class="f-editable">
								<div class="form-group">
									<label for="nom_f" class="control-label">Nom de Famille de l'enfant : </label>
									<input required type="text" class="form-control" name="nom_f[]" value="<?php echo $f_donnees['enf_nom']; ?>"/>
								</div>
								<div class="form-group">
									<label for="pren_f" class="control-label">Prénom :</label>
									<input class="form-control" type="text" name="pren_f[]" required value="<?php echo $f_donnees['enf_prenom']; ?>"/>
								</div>
								<?php //sexe
								if ($f_donnees['enf_gender']=== "fille"):?>
										<div class="form-group" id="radioS">
											<input type="radio" name="sex_f[]" value="fille" id="fille" checked="checked" />
											<label for="fille"> Fille</label>
											<input type="radio" name="sex_f[]" value="garco" id="garco" />
											<label for="garco"> Garçon</label>
										</div>
								<? else: ?>
										<div id="radioS" class="form-group"> 
											<input type="radio" name="sex_f[]" value="fille" id="fille" />
											<label for="fille"> Fille</label>
											<input type="radio" name="sex_f[]" value="garco" id="garco" checked="checked"/>
											<label for="garco"> Garçon</label>
										</div>
								<? endif; ?>
								<div class="clr"></div>
								<label for="daten_f">Date de naissance : <br/><small>(Au format JJ/MM/AAAA)</small></label>
								<div class="form-group dates">
									<input class="form-control" type="number" required data-day="day" <?php echo $errorMessDate; ?> name="date_j[]" value="<?php echo date('d', $time); ?>" size="2"/>
									<div class="help-block with-errors"></div>
								</div>
								<div class="form-group dates">
									<span>/</span><input class="form-control" type="number" required data-month="month" <?php echo $errorMessDate; ?> name="date_m[]" value="<?php echo date('m', $time); ?>" size="2"/>
									<div class="help-block with-errors"></div>
								</div>
								<div class="form-group dates">
									<span>/</span><input class="form-control" type="number" required data-year="year" <?php echo $errorMessDate; ?> name="date_a[]" value="<?php echo date('Y', $time); ?>" size="4"/>
									<div class="help-block with-errors"></div>
								</div>
								<input type="hidden" name="inscenf[]" value="<?php echo $compt; ?>">
								<input type="hidden" name="action-modifEnf" value="1" class="trigger-form">

							</div>
							<hr style="border-top: 1px solid #b4bbf5;"/>
							<hr style="border-top: 1px solid #b4bbf5;"/>
						</div>

						<?php $compt++; ?>
						<?php endif; ?>
					<?php endwhile;
				$prep->closeCursor();
				$prep = NULL; // Termine le traitement de la requête ?>


				<?php include('inc/html/bloc-inscr-enfant.php'); ?>


			<?php endif;?>

		<?php endfor;?>

	<?php endif;?>
	<div class="form-group">
		<button style="float:right" type="submit" class="btn btn-red sub-form"><span class="glyphicon glyphicon-ok"></span> Etape suivante</button>
	</div>
	</form>
<?php //endif; // mess erreur?>


<div class="espace"></div>
<div class="clr"></div>
<a href="connexion.php" class="preced"><img src="../templates/educ_env_2/images/panier_precedent.png"  width="136" height="95" alt="Etape précédente"/></a>


</div>
<?php 
$scriptJS = '<script type="text/javascript" src="../js/ftn.js"></script> ';
$scriptJS .= '<script type="text/javascript" src="../js/validator.js"></script>';

include('inc/html/footer.php');
?>
