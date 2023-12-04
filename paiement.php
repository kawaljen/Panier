<?php
session_start(); 
if($_SESSION['isLocalMode']){
	include('inc/dev/variablejoomla-dev.php');
}
else {
	include('inc/variablejoomla.php');	
}

if(!isset($_SESSION['idla'])){
	header("location:deconnexion.php");
}

include('../panier_tools/connectbd.php');
include('../panier_tools/sensitiveinfo.php');

// include('../panier_tools/ControllerUser.php');
include('controller/ControllerUser.php');

include ("./inc/systempay/function.php");

include('./inc/globalVariables.php');
include("controller/PaiementController.php");
$Paiement = new PaiementController($db);
	
//update inscr depuis recap
if(isset($_POST['action']) &&  $_POST['action']=== 'update'){
	$errorUpdateInscr = $Paiement-> updateInscr($_POST);
}
if(isset($_POST["sendMail"])){
	$messmail = $Paiement-> sendInscrEmail($_POST, $setting);
}

$inscriptions = $Paiement-> getInscriptions();

$hasSejour = $inscriptions['hasSejour'];
//includes html
include('./inc/html/head.php');
include('./inc/html/topmenu.php');

?>


	<div id="panier" class="fin">
		<?php if(isset($messmail)){echo '<div class="bg-success text-success"><p style="padding:5px;">'.$messmail.'</p></div>';} ?>
		<?php if ($inscriptions['mailEnv'] && !isset($messmail)):?>
			<div class="bg-success text-success"><p style="padding:5px;">Nous avons déjà envoyé un mail concernant cettte inscription. Si vous n'avez pas reçu de mail, merci de vérifier les courriers indésirables et votre adresse mail. Si le problème persiste n'hésitez pas à nous contacter par email ou téléphone. <br/>Vous avez toutefois encore la possibilité de modifier les informations, nous prendrons en compte le dernier mail envoyé pour l'inscription.</p></div>
		<?php endif;?>
		<?php if(isset($_SESSION['modeTest']) && $_SESSION['modeTest']) { 
			echo $_SESSION['modeTestText'];
			} ?>
		<h2>Paiement</h2>
		<div id="topo">
			<p>Votre inscription est enregistrée dans nos bases de données.<br/><br/></p>
			<p> Afin de rendre votre inscription définitive, vous avez la possibilité
			de régler le montant total en ligne ou vous pouvez choisir
			de régler un acompte d'un montant figurant ci dessous. <br/>

			<p>Dans tous les cas, votre inscription ne sera définitive qu'une fois ce paiement reçu.</p>
			<p><br/>Vous recevrez ensuite par courrier le dossier avec les informations sur le séjour/formation (environ trois semaines avant le départ).<br/><br/></p>

			<?php if($inscriptions['hasCaf'] ):?>
				<p>D'après vos déclarations, vous bénéficiez d'une réduction liée à votre quotient familial de la Caf<br/>
					Les prix affichés et la réduction EE64 sont sous réserve de votre quotient familial de la Caf, ils vous seront confirmés une fois votre justificatif étudié par nos soins.</p>
			<?php endif; ?>
		</div>

		<h2>Votre commande :</h2>
<?php

					$isPagePaiement = true;
					include('inc/bloc/bloc-info-client.php');
// BOUCLE INSCRIPTIONS
if (count($inscriptions["inscriptions"]) <= 0 ):?>
	<p>Le panier est vide</p>
	<p>Si vous venez d'effectuer un paiement en ligne pour cette inscription, celle ci sera désormais consultable dans <a href="https://education-environnement-64.org/espace_perso/">Mon compte</a>.</p>
<?php else : 
				echo '<table>';
				//libelé tableau
				echo '<tr id="fondpan"><td>Libellé</td>';
					if($inscriptions['hasSejour'])
						{ echo '<td>Enfant</td><td>Option transport</td><td>Prix séjour</td>'; }
				echo '<td>Prix TTC</td><td>Prix Acompte</td></tr>';

//requete inscription enfant d'abord..
	$article=' ';

	$contmail = ' ';
	$acompte= 0;
	$transprix =0;
	$insc=array();
	$montantSansCaf = 0;
	for ($i=0; $i< count($inscriptions['inscriptions']); $i++){
			$ligne =  $inscriptions['inscriptions'][$i]; 

			echo "<tr><td class=\"tdlibele\"><span class=\"tdlibele\">".$ligne['sej']->intitule()."</span>";
			echo " <br/><span class=\"tddate\"> Du ".$ligne['ins']->dates()."</span></td>";
			$prixLigne = $ligne['sej']->prix();
			
			if($ligne['ins']->id_article()>100){
				echo '<td><span class="hideDesktop"><i>Pour : </i></span>'.$ligne['ins']->enfant().'</td>';
				echo '<td><span class="hideDesktop"><i>Transport : </i></span>'.$ligne['ins']->transportLieu().' '.$ligne['ins']->transport().' &#8364</td>';

				if($ligne['ins']->duree2()>1){
					$prixLigne = (($inscriptions['hasCaf'] && $ligne['sej']->prix_reducCafD2()>0)? $ligne['sej']->prix_reducCafD2(): $ligne['sej']->prix_duree2());
				}
				else{
					$prixLigne = (($inscriptions['hasCaf'] && $ligne['sej']->prix_reducCaf()>0)? $ligne['sej']->prix_reducCaf() : $ligne['sej']->prix());					
				}
				
				echo '<td><span class="hideDesktop"><i>Prix : </i></span>'.$prixLigne.'&#8364</td>';
			}
			elseif ($inscriptions['hasSejour']) {
				echo '<td></td><td></td><td></td>';
			}
			
			$prixTotal = $ligne['ins']->transport() + $prixLigne;
			echo '<td class="tdprix"><span class="hideDesktop"><i>Total: </i></span>'.$prixTotal.'&#8364</td>';
			echo '<td><span class="hideDesktop"><i>Prix de l\'acompte : </i></span>'.$ligne['sej']->acompte().'&#8364</td></tr><tr class="space"></tr>';
			
			$acompte+=$ligne['sej']->acompte(); 
			$transprix+= $ligne['ins']->transport();
			$prix[]= $prixLigne; 


			$article.= $ligne['sej']->intitule().' ';

			//contenu mail formulaire
			$contmail.=' - '.$ligne['sej']->intitule().' '.$ligne['ins']->dates();

			if($ligne['ins']->id_article()>100){
				$contmail.='. Enfant inscrit : '.str_replace(array('é', 'è'), 'e', $ligne['ins']->enfant()).' ';
				//$contmail.= ' ( '.$ligne['ins']->enf_birthday().' )';
				$contmail.='. Transport : '.$ligne['ins']->transportLieu();
				if($ligne['ins']->transport()>0){$contmail.=' : '.$ligne['ins']->transport().' &#8364'; }
			}
			$contmail.='. Prix : '.$prixTotal.' &#8364';
			$multiple = $ligne['ins']->multiple(); 
			$remise=$ligne['ins']->remise();
			$contmail.='<br/>';
			//if()
			$montantSansCaf += (($ligne['ins']->duree2()>1 ) ? $ligne['sej']->prix_duree2() : $ligne['sej']->prix());
								
	}

	echo '</table>';

//calcul du montant
if(count($inscriptions['inscriptions'])>0){
	$hasMultiple = false;

	$montant=0;
		for($k=0; $k<count($prix); $k++)
			{
				$montant+=$prix[$k];
			}
	if(isset ($multiple )&& $multiple != null)
		{
			echo '<div id="multiples"><p>Inscription multiple : '.$multiple.'</div>';
			$hasMultiple = true;
		}

	if(  $remise>0 || $hasMultiple )
			{	$remise= ($montant*$remise)/100;
				$montant-=$remise;
				$remiseSansCaf = ($montantSansCaf*$remise)/100;
				$montantSansCaf-= $remiseSansCaf;
			}
	$montant = round($montant,2);
	$montantSansCaf = round($montantSansCaf,2);

	if($remise > 0 ){	echo "<p id=\"tdremise\">Remise 5% : <span>".round($remise,2)." &#8364 </span></p>";}
	echo "<div id=\"tdprixtotal\"><p>Acompte à verser* : ";
	//(transport : ".$transprix."&#8364  + acompte : ".round($acompte,2)."&#8364 ) <span>".($transprix+round($acompte,2))." &#8364 </span>   Total : <span>".$montant." &#8364 </span></p></div>";}
	if($transprix>0){echo "(transport : ".$transprix."&#8364  + acompte : ".round($acompte,2)."&#8364 ) "; }
	
	echo "<span>".(round($acompte,2)+ $transprix)." &#8364 </span>  Total : <span>".($montant+ $transprix)." &#8364 </span></p></div>";	
}
	$montantbis=$montant;
	
	



//mail
$totalaccompte = $transprix +round($acompte,2);
$totaltotalite = $transprix +round($montant,2);
$montantSansCaf += $transprix;
$order_id = $_SESSION["orderid"] ;

?>

<div id="espace"></div>
<p><br/><br/>*Acompte à verser pour valider l'inscription, remboursé si annulation 30 jours avant le séjour/formation.</p>




<?php
// FORMULAIRES
$montant_en_euro= $totalaccompte;
$montant_en_euro_accompte = $montant_en_euro*100;
$montant_en_euro_total = $totaltotalite*100;
$article= str_replace(array('é', 'è'), 'e', $article);
$article= strtr(utf8_decode($article), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
$article = str_replace('"', " ", $article);
$article = str_replace('\'', " ", $article);


/* --------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------
CREATION DU FORMULAIRE DE PAIEMENT
Le formulaire de paiement est composé de l'ensemble des champs vads_xxxxx contenu dans le tableau $params
Celui-ci est envoyé à la plateforme de paiement à l'url suivante :https://secure.payzen.eu/vads-payment/

---------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------- */
// Initialisation des paramètres
$params=array();
// tableau des paramètres du formulaire
$params['vads_site_id']= isset($params_vads_site_id) ? $params_vads_site_id : 0000000 ;
// en cents
$params['vads_currency']="978";
// norme ISO 4217
$params['vads_ctx_mode']=$setting['systemPayMode'];
$params['vads_page_action']="PAYMENT";
$params['vads_action_mode']="INTERACTIVE";
// saisie de carte réalisée par la plateforme
$params['vads_payment_config']="SINGLE";
$params['vads_version']="V2";

//valeurs facultatives
//url de la boutique
$params['vads_shop_url']="https://education-environnement-64.org/";
//personalisation : infos client
$params['vads_cust_email'] = $couriel;
$params['vads_order_id'] = $order_id;
$params['vads_order_info'] = $article;
$params['vads_cust_id']=$_SESSION['idla'];

$params['vads_return_mode']="GET";

// recupération du certificat // certificat production commence par 220...
$key = $setting['systemPayckey']; 

if($montant_en_euro_accompte > 0)
{
	$params['vads_amount']=$montant_en_euro_accompte;
	// CREATION DU FORMULAIRE DE PAIEMENT  encodé en UTF8
	$formAccompte = get_formHtml_request(uncharm($params),$key);
}
if($montant_en_euro_total > 0)
{
	$params['vads_amount']=$montant_en_euro_total;
	// CREATION DU FORMULAIRE DE PAIEMENT  encodé en UTF8
	$formTotalite = get_formHtml_request(uncharm($params),$key);
}
?>
<p><br/>
	<div class="optionRegelement">
		<h2>JE SOUHAITE REGLER LA TOTALITE </h2>
		<div id="contenair_form">
			<p><br/>Vous choisissez de payez le solde d'un montant de <?php echo $totaltotalite; ?>&#8364 en accédant à une plateforme de paiement sécurisée. Vous recevrez par courrier le dossier avec les informations sur le séjour/formation (environ trois semaines avant le départ).<br/><br/></p>
			<?php echo $formTotalite; ?>
		</div>
	</div>

	<div class="optionRegelement">
		<h2>JE SOUHAITE REGLER UN ACOMPTE</h2>

		Vous choissisez de ne régler que l'acompte pour le moment, c'est à dire <?php echo  $montant_en_euro;?>&#8364. <br/> <br/>
		Vous pouvez le payer, soit par chèque en nous envoyant le formulaire d'inscription, soit en ligne en accédant à une plateforme de paiement sécurisée.
		<br/> Le solde pourra se faire par chèque, par virement bancaire ou en ligne.<br/>
		<p>
		<div class="cols2">
			<p><h3>- Je souhaite régler l'acompte par chèque</h3>
			<p>
			Cliquez sur le bouton ci-dessous pour recevoir par email un formulaire d'inscription à nous renvoyer rempli avec un chèque d'acompte adressé à :
			<br/>(Inscription définitive une fois ce paiement reçu)</p>
			<p>
			<br/>Education Environnement 64
			<br/>2 rue Pats
			<br/>BUZY 64260<br/></p>

			<br/>
			<?php $contmail = str_replace(array('/', '\\'), '', $contmail); ?>
			<form method="post" action="paiement.php"/>
				<input type="hidden" name="nom" value="<?php echo $nom;?>">
				<input type="hidden" name="couriel" value="<?php echo $couriel;?>">
				<input type="hidden" name="prenom" value="<?php echo $prenom;?>">
				<input type="hidden" name="adress" value="<?php echo $adress;?>">
				<input type="hidden" name="complement" value="<?php echo $complement;?>">
				<input type="hidden" name="codep" value="<?php echo $codep;?>">
				<input type="hidden" name="ville" value="<?php echo $ville;?>">
				<input type="hidden" name="telephone" value="<?php echo $telephone;?>">
				<input type="hidden" name="ddjs" value="<?php echo $ddjs;?>">
				<input type="hidden" name="total" value="<?php echo $totaltotalite;?>">
				<input type="hidden" name="acompte" value="<?php echo $totalaccompte;?>">
				<input type="hidden" name="contmail" value="<?php echo $contmail;?>">
				<input type="hidden" name="orderid" value="<?php echo $order_id;?>">
				<input type="hidden" name="multiple" value="<?php echo $multiple;?>">
				<input type="hidden" name="naiss_adult" value="<?php echo $naiss_adult;?>">
				<input type="hidden" name="sendMail" value="true">
				<?php if($inscriptions['hasCaf'] ):
					//$montantSansCaf = ($montantSansCaf< $totaltotalite ? $montantSansCaf : 0);?>
					<input type="hidden" name="hasCaf" value="<?php echo $montantSansCaf; ?>">
				<?php endif;?>
				<?php foreach ($insc as $key => $donnees):?>
						<input type="hidden" name="ligneInsc[<?php echo $key;?>]" value="<?php echo $donnees;?>">

				<?php endforeach;?>
				<button id="buttun-mail" type="submit" width="336" height="41"  align="middle" ><span></span>Recevoir le formulaire par mail</button>
			</form>

		</div>

		<div class="cols2">
			<h3>- Je souhaite régler l'acompte en ligne</h3>
			<p> Cliquez sur le bouton ci-dessous pour accéder à notre plateforme de paiement.</p>
			<div id="contenair_form">
				<?php echo $formAccompte;?>
			</div>
		</div>
	</div>



	<div class="topo">
		<h3>Infos paiements en ligne</h3>
		<p>
		Pour la sécurité des paiements en ligne, nous utilisons Systempay, la solution de paiement par carte bancaire mise au point par notre banque, la Caisse d'épargne.<br/>
		Ainsi les numéros de carte ne transitent jamais en clair sur le réseau Internet : ils sont cryptés selon le procédé SSL (Secure Socket Layer). Ce procédé propose une sécurité maximale et est utilisé par l'ensemble des boutiques en ligne pour les transactions de ce type. En cliquant sur le lien "accéder à la plateforme de paiement" de notre panier, vous serez redirigés vers les pages de paiement Systempay Paiement (adresse sécurisée en https). Une fois vos coordonnées bancaires renseignées, elles seront envoyées par une connexion cryptée à la Caisse d'épargne. La Caisse d'épargne pourra alors effectuer les demandes d'autorisation relatives à tous paiements.</br>
		La Caisse d'épargne est le seul destinataire des informations carte ; Nous ne sommes pas en mesure de les connaître. Vous pouvez ainsi effectuer votre paiement en ligne en toute sécurité.
		</p>
	</div>

<?php // fin condition count($inscriptions)
	endif;?>
</div>
<div class="espace"></div>
<a href="recapitulatif.php" class="preced"> <p> < Etape précedente</p> <img src="../templates/educ_env_2/images/panier_precedent.png"  width="136" height="95" alt="Etape précédente"/></a>
<?php include('inc/html/footer.php');?>
