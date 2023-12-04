<?php
include('../panier_tools/connectbd.php');

if (isset($_REQUEST))
{
	if($_REQUEST['vads_result'] !== "00")
		{header("location:/");}
	else if($_REQUEST['vads_auth_mode']!=="FULL")
		{header("location:/");}

}
else{header("location:/");}

//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');
?>
		<div id="espace"></div>
		<h2>Vous venez de vous inscrire</h2>
		<p> Vous venez de nous payer la totalit&eacute; ou l'acompte pour votre s&eacute;jour/formation, votre inscription est confirm&eacute;e. Celui ci est remboursable en cas d'annulation 30 jours avant le d&eacute;but de la formation ou du s&eacute;jour.<br/><br/>
		Vous avez re&ccedil;u un email de confirmation de la part de notre banque ainsi que de notre part sur votre boite mail. <br/>(V&eacute;rifier vos couriers ind&eacute;sirables, si vous ne n'avez pas re&ccedil;u notre email. Votre inscription est tout de m&ecirc;me enregistr&eacute;e, vous pouvez aussi <a href="/contact">nous contacter directement</a>).<br/><br/>
		Vous recevrez ensuite par courier le dossier avec les informations sur le s&eacute;jour/formation (environ trois semaines avant le d&eacute;part). Si vous avez choisi de ne payer que l'acompte pour le moment, le solde pourra se faire par ch&egrave;que, par virement bancaire ou en vous connectant<a href='/espace_perso/'> à votre compte.</a><br/><br/></p>
				<div id="espace"></div>
						<div id="espace"></div>
								<div id="espace"></div>
		<small style="padding-top:40px;">**Cette page ne constitue pas une preuve de paiement.</small>
</div>
<?php include('inc/html/footer.php'); ?>
