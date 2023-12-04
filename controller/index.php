<!-- ------------------------------------------------- 
Exemple de code php - Formulaire de paiement unitaire
VERSION 1.0b - Lyra Network
------------------------------------------------------ -->

<?php
	header('Content-type: text/plain; charset=utf-8');
		
		//envoi du mail au client
		
		define('MAIL_DESTINATAIRE',$_REQUEST['vads_cust_email']); 
		define('MAIL_SUJET','Education Environnement 64 inscription');

		$mail_entete = "MIME-Version: 1.0". "\r\n";
		$mail_entete .= "From: webmaster@education-environnement-64.org" . "\r\n";
		$mail_entete .= "Reply-To: education.environnement.64@wanadoo.fr" . "\r\n";
		$mail_entete .= 'Content-Type: text/HTML; charset=utf-8' . "\r\n";
		$mail_entete .= 'Content-Transfer-Encoding: 8bit'. "\n\r\n";

			
				// préparation du corps du mail
				$mail_corps = "Bonjour Mme, M ";
				$mail_corps .="<br/><br/> Vous venez d'effectuer une inscription à l'une de nos formations ou à l'un de nos séjours de vacance.";
				$mail_corps .="<br/> Vous venez de régler Euros, qui correspondent à l'acompte (et aux eventuels frais de transport pour les séjours). Cet acompte est remboursable en cas d'annulation 30 jours avant le debut du séjour/formation." ;
				$mail_corps .="<br/>Vous recevrez par courier le dossier avec les informations sur le séjours/formation (environ trois semaines avant le départ). Le solde pourra se faire par chèque ou par virement bancaire.<br/><br/>";
				$mail_corps .= "--------------------------------------<br/>";
				$mail_corps .= "Identification<br/>";
				$mail_corps .= "---------------------------------------<br/><br/>";

				

				$mail_corps .= "<br/><br/><i>Email:</i> ";
				$mail_corps .= "<br/><br/>-----------------<br/>";
				$mail_corps .= "Votre commande <br/>";
				$mail_corps .= "-----------------<br/><br/>";
				$mail_corps .= "Numéro de commande : ";
				//$mail_corps .= "\n MONTANT TOTAL : ".$_POST['total'].' €';
				$mail_corps .= "<br/><b> Acompte  :</b> €";



		//envoi du mail à ee64
		
		define('COPIE_MAIL_DESTINATAIRE','education.environnement.64@wanadoo.fr'); 
		define('COPIE_MAIL_SUJET','test email');


			// envoi du mail
			//if (mail(COPIE_MAIL_DESTINATAIRE,COPIE_MAIL_SUJET,$mail_corps,$mail_entete)) {
			//Le mail est bien expédié
			//	echo "ok";
			//} else {
			//Le mail n'a pas été expédié
			//echo "Une erreur est survenue lors de l'envoi du formulaire par email";
			//}
		//envoi du mail à ee64
		
		define('COPIE_MAIL_DESTINATAIREE','kawaljen@hotmail.fr'); 
		define('COPIE_MAIL_SUJETT','test email inscription en ligne');


		// envoi du mail
		if (mail(COPIE_MAIL_DESTINATAIREE,COPIE_MAIL_SUJETT,$mail_corps,$mail_entete)) {
		//Le mail est bien expédié
			echo "ok";
		} else {
		//Le mail n'a pas été expédié
		echo "Une erreur est survenue lors de l'envoi du formulaire par email";
		}		
		



?>
