<?php
//~ TESTmode pour fin.php et return_payment et espace perso

$setting= array();
if($_SESSION['modeTest']){
	$setting['email']=false;
	$setting['systemPayMode']='TEST';
	$setting['systemPayckey']="000000000000";
	$setting['copieEmail']= true;
}
else {
	$setting['email']=true;
	$setting['systemPayMode']='PRODUCTION';
	$setting['systemPayckey']=isset($setting_systemPayckey)? $setting_systemPayckey : "000000000000";
	$setting['copieEmail']= false;
}

// MESSAGES ESPACE PERSO
$messInscComptabilise = "*L'enregistrement des paiements effectués a commencé à être comptabilisé à partir du 20 decembre 2015, si vous avez effectué un paiement avant cette date, le paiement n'apparaitra pas dans cette liste, veuillez nous contacter si vous avez un doute quant à votre solde.";
$messInscNoPreuve = "*Renseignement donné à titre indicatif, ne concerne que les paiements en ligne effectués depuis le site internet et ne constitue pas une preuve de paiement.";


?>
