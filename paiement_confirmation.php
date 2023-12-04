
<?php
session_start();
include("controller/connectbd.php");
include ("/panier/inc/systempay/function.php");

$Connexion = new Connexion($_POST);
if(!isset($_SESSION['connexion'])){
	header("location:index.php");
	}
else{
	$gestion = new Gestion($db);
	}

		
//HTML head
	$titrepage="Paiement confirmation";
	include('inc/html/head.php');
	
	echo'<div id="cadre_exterieur"><div id="cadre2">';
	
	include('../panier/inc/html/topmenu.php');

?>
<body>
	


<?php 					
	if( isset($_SESSION['connexion']) && is_numeric($_SESSION['connexion']) ):				
		
		if(isset($_POST['amount']) && is_numeric($_POST['amount']) && isset($_POST['orderid']) && is_numeric($_POST['orderid'])):?>
		<?php
			$infoUser=$gestion->selectUser();
			if(count($ligne)>1) { $_SESSION['errorMess'] = 'une erreur est survenue'; header("location:index.php");}
			else{
					foreach ($infoUser as $donnees){
						// info pour systempay
						$couriel = $donnees->email();
					}?>			
			
			
			
			<?php 	$Inscription= $gestion->inscriptionEnligne($_POST['orderid']); 
					$montantDejaPaye=0;	$solde=0;?>
			<?php

				echo'<h2>Confirmer le montant </h2>';
				foreach ($Inscription['info']as $donnees):?>
					<p><?php echo 'Le '.$donnees-> dernieres_modif().' -- '.$donnees-> article().', du '.$donnees->dates();
							 if($donnees->enfant() !== ' ') echo ', enfant inscrit : '.$donnees->enfant();
							 if( $donnees-> transport() > 0 ) echo ', Transport: depuis '.$donnees->transportLieu().', '.$donnees-> transport().'&#8364';
							 echo ', Prix total: '.$donnees-> prixTotal().'&#8364';
							 
							 $solde+=$donnees-> prixTotal(); 
							 
							// info pour systempay
							$order_id=$donnees-> orderid();
							$article=$donnees-> article();
							$iduser=$donnees-> id_user();	
							$montant_en_euro = 	$_POST['amount'];					 
						?>										
					</p>
					<p></p>																
														
				<?php endforeach;
				echo '<p>Paiement(s) en ligne deja effectue(s)*</p>';
				if(count($Inscription['payment']) === 0) echo 'Pas de paiement en ligne enregistre a ce jour.';
				
				for($j=0; $j<count($Inscription['payment']); $j++){
					foreach ($Inscription['payment'][$j] as $donnees):?>
						<p><?php echo $donnees-> amount().'&#8364 '.$donnees->timestamp();?></p>
						<?php $montantDejaPaye+= $donnees-> amount(); ?>
						<p></p>										
					<?php endforeach;							
					}
					$solde = $solde - $montantDejaPaye - $_POST['amount'];

					if($solde >= 0){?>						
						<p>Vous allez payer <?php echo $_POST['amount'];?> &#8364. Le solde pour ce sejour/formation sera de <?php echo $solde;?> &#8364 *</p>
						
						<?php
						//systemppay form
						$montant_en_euro = $montant_en_euro*100;
					    include("inc/systempayform.php");
						
						if($montant_en_euro > 0)
						{
							$params['vads_amount']=$montant_en_euro;

							// CREATION DU FORMULAIRE DE PAIEMENT  encodé en UTF8
							$form = get_formHtml_request(uncharm($params),$key); 
						}
						echo $form;
						
						?>


					<?php } else { ?>
						<p>Le montant que vous avez entre est trop grand et depasse le solde que nous avons enregistre dans notre base de donnees pour cette inscription, merci de verifier le montant. Si le probleme persiste contactez nous. <a href="recapitulatif.php">Revenir a la page de recapitulatif</a>.</p>
					<?php }?>			
					
		<?php else: ?>
				<p>Une erreur est survenue, le montant entre ne semble pas valide, <a href="recapitulatif.php">revenir a la page de recapitulatif</a> et reessayer. Si le probleme persiste contactez nous.</p>
					
		<?php	endif;?>
		
<?php
	else : 
	$_SESSION['errorMess'] = 'une erreur est survenue'; header("location:index.php");
?>
	
		
<?php	endif;?>


	<div class="clr"></div>
	<small style="padding-top:40px;">*Renseignements donnes a titre indicatif, ne concerne que les paiements effectues en ligne depuis le site internet et ne constitue pas une preuve de paiement.</small>
	<!--info ferme la div categorie-->
	</div>

<!--fin div container-->		
</div>	

</body>
</html>


