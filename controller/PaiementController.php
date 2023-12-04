<?php

$rootPath = './../';
include($rootPath."models/InscrLigne.php");
include($rootPath."models/Sejour.php");
include($rootPath."models/Enfant.php");
include($rootPath."models/User.php");
include($rootPath."models/Dates.php");
include($rootPath."models/Transport.php");


class PaiementController{
	
    private $_db, 
			$_idUser,
			$_orderid; 
	  
	public function __construct($db)  { $this->setDb($db);}

	//Set db
	public function setDb(PDO $db)  { $this->_db = $db; 
									  $this->_idUser = (is_numeric($_SESSION['idla']) ? $_SESSION['idla'] : 0 );
									  $this->_orderid = (is_numeric($_SESSION["orderid"]) ? $_SESSION["orderid"] : 0 );
									  }
   		
//upadte		
	public function updateInscr( $post){
		$lignes = array();
		$erreur = array();	
		
		$query = 'SELECT DISTINCT hasCaf, ligne_cde, id_article, remise FROM inscription WHERE id_user = '.$this->_idUser.' AND orderid =:orderid';
		$prep = $this->_db->prepare($query);
		$prep->bindValue('orderid', $this->_orderid, PDO::PARAM_INT);
		$prep->execute();

			while ($donnees =$prep->fetch())
				{
					$lignes[]= $donnees;
				}
		$prep->closeCursor();
		$prep = NULL;		

		$isSeveralSejour = ((count($lignes))>1? true : false); //cherche dans $lignes les articles avec id > 100

		if(isset($lignes)){
			if (isset($post['updateUser'])){
				$erreur = $this->updateUser($post); 
                echo $erreur;
			}
			$errorAgeEnfant = false;
			$errorAgeEnfantInfo = '';

			for($i=0; $i<count($lignes); $i++){
				$ligneCde = $lignes[$i]['ligne_cde'];

				$sejour = $this->getSejourById($lignes[$i]['id_article']); 
				$article= $sejour->intitule();

				$iddates = htmlentities($post['dates'.$ligneCde],ENT_QUOTES);
				$datesData = $this->getdatesfromId($iddates);
				$dates = $datesData->dates();
				unset($enfant );

				$prixTotal = $sejour->prix(); 
				
				$query ='UPDATE inscription SET id_dates= :iddates, article =:article, prixTotal=:prixTotal, dates=:dates WHERE id_user = '.$this->_idUser.' AND ligne_cde= :ligne_cde';


				if($lignes[$i]['id_article']>100){ //sejour
		
					$multiple =(isset($post['multiple']) ? htmlentities($post['multiple'],ENT_QUOTES) : '');
										
					$insc_enf = (isset($post['insc_enf'.$ligneCde]) ? htmlentities($post['insc_enf'.$ligneCde],ENT_QUOTES) : null );

					if(isset($_SESSION['hasUpdateEnfant']['ligne_cde'])){
						$key = array_search($ligneCde, $_SESSION['hasUpdateEnfant']['ligne_cde']);
						if ($key !== false) {
							$enfant =  $this->getEnfantById($_SESSION['hasUpdateEnfant']['enf_enf_id'][$key]);
							if (($sejour->age_min()-1) >= $_SESSION['hasUpdateEnfant']['age'][$key] || ($sejour->age_max()+1) <= $_SESSION['hasUpdateEnfant']['age'][$key]) {
								$errorAgeEnfant = true;
								$errorAgeEnfantInfo .= '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>L\'âge de '.$enfant.'('.$_SESSION['hasUpdateEnfant']['age'][$key].'ans) n\'est pas dans la tranche d\'âge de '.$article.' ('.$sejour->age_min().'-'.$sejour->age_max().'ans).<br/> ';
								$errorAgeEnfantInfo .= '<p>Vérifier les informations personnelles enregistrées sur vous et les enfants en <a href="./identification.php">cliquant ici</a></p></div>';
								$enfant = '';
							}

						}
					}
					if(!isset($enfant)){
						$enfant =  $this->getEnfantById($insc_enf);
					}	

					$idtransp= (isset( $post['transp'.$ligneCde]) ? htmlentities($post['transp'.$ligneCde],ENT_QUOTES) :0 );
					$transport = $this->gettransportById($idtransp);
					$transportLieu=$transport['lieu'];
					
					// prix..
					$duree2 =1;
					if($datesData->id_duree()>1){

						$duree2 =2;
						$montant = (($lignes[$i]['hasCaf'] && $sejour->prix_reducCafD2()>0)? $sejour->prix_reducCafD2() : $sejour->prix_duree2()); 
					}
					else {
						$montant = (($lignes[$i]['hasCaf'] && $sejour->prix_reducCaf()>0)? $sejour->prix_reducCaf() : $sejour->prix() ); 
					}
					$remisePerc = 0;
					//echo 'mul'
					if(!empty($multiple) ||$isSeveralSejour){
						$remisePerc = 5;
						$remise= ($montant*$remisePerc)/100;
						$montant-=$remise;

					}
					$prixTotal = ($montant  + $transport['prix']);

					//query
					$query ='UPDATE inscription 
						SET id_enf= :id_enf, enfant=:enfant, id_transp= :id_transp, 
						multiple= :multiple, id_dates= :iddates, article =:article, transportLieu=:transportLieu,
						transport=:transport, prixTotal =:prixTotal, dates =:dates, duree2 =:duree2, remise=:remise
						WHERE id_user= '.$this->_idUser.' AND ligne_cde= :ligne_cde';
				}

				$prep = $this->_db->prepare($query);
				$prep->bindValue('dates', $dates, PDO::PARAM_STR);
				$prep->bindValue('ligne_cde', $ligneCde, PDO::PARAM_INT);
				$prep->bindValue('article', $article, PDO::PARAM_STR);
				$prep->bindValue('prixTotal', $prixTotal, PDO::PARAM_INT); 
				$prep->bindValue('iddates', $iddates, PDO::PARAM_STR);
				
				if($lignes[$i]['id_article']>100){ //sejour
					$prep->bindValue('id_enf', $insc_enf, PDO::PARAM_INT);
					$prep->bindValue('id_transp', $idtransp, PDO::PARAM_STR);
					$prep->bindValue('multiple', $multiple, PDO::PARAM_STR);
					$prep->bindValue('enfant', $enfant, PDO::PARAM_STR);
					$prep->bindValue('transportLieu', $transport['lieu'], PDO::PARAM_STR);
					$prep->bindValue('transport', $transport['prix'], PDO::PARAM_INT);
					$prep->bindValue('duree2', $duree2, PDO::PARAM_INT);
					$prep->bindValue('remise', $remisePerc, PDO::PARAM_INT);
					
				}
				$prep->execute();
				$prep->closeCursor();
				$prep = NULL;

			}
			if($errorAgeEnfant){
					$_SESSION['mess']= $errorAgeEnfantInfo;
					header("location:recapitulatif.php");
			}
			elseif(isset($_SESSION['hasUpdateEnfant']['error'])){
				$_SESSION['mess']=$_SESSION['hasUpdateEnfant']['error'];
				header("location:recapitulatif.php");
			}
			elseif($enfant = ''){
				$_SESSION['mess']='<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>Il manque les informations sur un enfant pour compléter l\'inscription';
				header("location:recapitulatif.php");				
			}
		}
		return $erreur;	
	}

	public function getInscriptions(){		
			$lignes = array("inscriptions"  => array(), "hasSejour" => false, "hasCaf"=>false, "mailEnv"=>false ) ;

			$query = 'SELECT DISTINCT * FROM inscription JOIN sejour ON id_article=id WHERE id_user = '.$this->_idUser.' AND etat !=\'en ligne\' AND orderid =:orderid';
			$prep = $this->_db->prepare($query);
			$prep->bindValue('orderid', $this->_orderid, PDO::PARAM_INT);
			$prep->execute();			 
			 while ($donnees = $prep->fetch(PDO::FETCH_ASSOC)){	
						if($donnees['id_article']>100){
							$lignes['hasSejour'] = true;
						}
						if($donnees['hasCaf']){
							$lignes['hasCaf'] = true;
						}
						if($donnees['etat']==='mail env'){
							$lignes['mailEnv'] = true;
						}
						$lignes['inscriptions'][] = array('ins' => new InscrLigne($donnees), 'sej' => new Sejour($donnees));
					}
			$prep->closeCursor();
			$prep = NULL;	
					
		    return $lignes;
	}



	private function getdatesfromId($id){		
			if (is_numeric($id))	{
				    $q = $this->_db->query('SELECT dates, id_duree FROM dates WHERE id_dates = '.$id);
					 while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
							$lignes  = new Dates($donnees);	

						}
					$q->closeCursor();
					$q = NULL;
					}

		    return $lignes;
		}	
	private function getEnfantById($id){		
			$enfants='';
			if (is_numeric($id))	{
				$q = $this->_db->query('SELECT DISTINCT enf_prenom, enf_nom, enf_birthday FROM site_enfant WHERE enf_id = '.$this->_idUser .' AND enf_enf_id ='.$id);
				while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
								$enfants = $donnees['enf_prenom'].' '.$donnees['enf_nom']. ' ('. date("d-m-Y",strtotime($donnees['enf_birthday'])).')';
						}
				$q->closeCursor();
				$q = NULL;		
			}

			return $enfants;
	}	
	private function gettransportById($id){		
			$rep = array('prix'=> 0, 'lieu' => 'au centre');
			if (is_numeric($id))	{
				$q = $this->_db->query('SELECT DISTINCT lieu, prix_transp  FROM transport WHERE id_transp = '.$id);
				while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
								$rep =  array('prix'=> $donnees['prix_transp'], 'lieu' => $donnees['lieu']);
						}
				$q->closeCursor();
				$q = NULL;		
			}

			return $rep;
	}	

	private function getSejourById ($id){
			if (is_numeric($id))	{
				$q = $this->_db->query('SELECT DISTINCT * FROM sejour WHERE id = '.$id);
				while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
								$rep = new Sejour($donnees);
						}
				$q->closeCursor();
				$q = NULL;		
			}

			return $rep;

	}

    private function updateUser($post){ 
		//print_r($post);
		$ddjs =(isset($post['ddjs']) ? htmlentities($post['ddjs'],ENT_QUOTES) : 0);
		//date
		if(!empty($post['datead_j']) && $post['datead_j']<32 ){$date_j=htmlentities($post['datead_j'],ENT_QUOTES);}else{$date_j=0;}
		if(!empty($post['datead_m']) && $post['datead_m']<13){$date_m=htmlentities($post['datead_m'],ENT_QUOTES);}else{$date_m=0;}
		if(!empty($post['datead_a'])){
			$date_a=0;
			$date_a=htmlentities($post['datead_a'],ENT_QUOTES);
			$date_ad=$date_a.'-'.$date_m.'-'.$date_j;}
		else {$date_ad='0-0-0';}
		echo $date_ad;
        $query ='UPDATE site_user SET ddjs= :ddjs, naiss_adult =:naiss_adult WHERE id = '.$this->_idUser;
        $prep = $this->_db->prepare($query);
        $prep->bindValue('naiss_adult',$date_ad);
        $prep->bindValue('ddjs',$ddjs, PDO::PARAM_INT);
		$prep->execute();
        $rowAffected = $prep->rowCount();
			
		$prep->closeCursor();
		$prep = NULL;
        return $rowAffected;
    }

	public function sendInscrEmail($post, $setting){

		$query = 'UPDATE inscription SET etat=\'mail env\' WHERE orderid= ?';

		$prep = $this->_db->prepare($query);
		$prep->bindValue(1, $this->_orderid, PDO::PARAM_INT);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;

		//envoie du mail

		define('MAIL_DESTINATAIRE',$post['couriel']);
		define('MAIL_SUJET','Education Environnement 64 préinscription');

		$mail_entete = "MIME-Version: 1.0". "\r\n";
		$mail_entete .= "From: webmaster@education-environnement-64.org" . "\r\n";
		$mail_entete .= "Reply-To: education.environnement.64@wanadoo.fr" . "\r\n";
		$mail_entete .= 'Content-Type: text/HTML; charset=utf-8' . "\r\n";
		$mail_entete .= 'Content-Transfer-Encoding: 8bit'. "\n\r\n";


		// préparation du corps du mail
		$mail_corps = "Bonjour Mme, M ".$post['nom']." ".$post['prenom'].",<br/><br/>";
		$mail_corps .="Vous venez de vous pré-inscrire à l'une de nos formations ou à l'un de nos séjours de vacances. <br/><br/>";
		$mail_corps .= "Pour confirmer votre inscription, vous devez maitenant nous faire parvenir ce document imprimé ainsi que le montant de l'acompte à notre adresse dans les 4 jours :<br/><br/>";
		$mail_corps .= "     EDUCATION ENVIRONNEMENT <br/>      2 rue Pats <br/>      64260 BUZY <br/><br/>";
		$mail_corps .= "Le solde pourra se faire par chèque, par virement bancaire ou en ligne en vous connectant à <a href='https://education-environnement-64.org/espace_perso/index.php'>votre compte personnel</a>.<br/><br/>";
		$mail_corps .= "---------------------<br/>";
		$mail_corps .= "Identification<br/>";
		$mail_corps .= "---------------------<br/>";
		$mail_corps .= $post['nom']." ".$post['prenom'];
		$mail_corps .= "<br/>".$post['adress']."<br/> ".$post['codep']."  ".$post['ville'];
		$mail_corps .= "<br/><br/><i>Email:</i> ".$post['couriel'];
		if($post['telephone'] != 0){$mail_corps .= "<br/><i> Téléphone:</i>".$post['telephone'];}
		if($post['ddjs']!= 0){$mail_corps .= "<br/><i>Numéro de dossier DDJS:</i> ".$post['ddjs'];}
		if($post['naiss_adult']!= 0){$mail_corps .= "<br/><i> date de naissance: </i>".$post['naiss_adult'];}
		$mail_corps .= "<br/><br/>---------------------<br/>";
		$mail_corps .= "Votre commande <br/>";
		$mail_corps .= "---------------------<br/><br/>";
		$mail_corps .= "  ".str_replace(array('/', '\\'), '', $post['contmail']);
		if($post['multiple'] != 0){$mail_corps .= "<br/><br/> Vous vous inscrivez conjointement avec ".$post['multiple'].". Vous bénéficierez donc d'une remise de 5% <br/>";}
		$mail_corps .= "<br/><br/> <b>MONTANT TOTAL:</b> ".$post['total'].' &#8364';
		$mail_corps .= "<br/><b> Acompte:</b> ".$post['acompte'].' &#8364';
		$mail_corps .= "<br/><br/><br/><small> Numéro de commande: ".$post['orderid']."</small>";
		if(isset($post['hasCaf']) && $post['hasCaf']> 0){
			$mail_corps .= "<br/><br/>*<small> D'après vos déclarations, vous bénéficiez d'une réduction liée à votre quotient Caf<br/>";
			$mail_corps .= 'Les prix affichés sont sous réserve de celui ci, ils vous seront confirmés une fois votre justificatif étudié par nos soins.';
			$mail_corps .= 'Le prix de votre inscription, sans le transport, s\'éléve à '.$post['hasCaf'].' &#8364 sans réduction.</small>';
		}
		if($test){
			$mail_corps .= 'Test : ';
			foreach($setting as $val){

				$mail_corps .= substr($val, 0, 5).' ';

			}

		}


		// envoi du mail
		if (mail(MAIL_DESTINATAIRE,MAIL_SUJET,$mail_corps,$mail_entete)) {
		//Le mail est bien expédié
		$messmail= '<p><b>// Le mail a bien &eacute;t&eacute; exp&eacute;di&eacute; ! //</b></p><p> Si vous n\'avez pas reçu ce mail, v&eacute;rifiez vos couriers ind&eacute;sirables, s\'il n\'y est pas, vous pouvez nous <a href="../contact" style="color:#6C5353;"> contacter directement</a>.</p>';
		} else {
		//Le mail n'a pas été expédié
		$messmail='<p>!! Une erreur est survenue lors de l\'envoi du formulaire par email, vous pouvez nous <a href="../contact"> contacter directement</a>.</p></p>';
		}


		//envoie du mail à ee64
		if($setting['email']){
			define('COPIE_MAIL_DESTINATAIRE','education.environnement.64@wanadoo.fr');
			define('COPIE_MAIL_SUJET','Préinscription par mail');
		}else {
			define('COPIE_MAIL_DESTINATAIRE','kawaljen@hotmail.fr');
			define('COPIE_MAIL_SUJET','Préinscription par mail-- copie');
		}
			


		//~ // envoi du mail
		if($setting['email'] || $setting['copieEmail']){
			if(isset($post['hasCaf']) && $post['hasCaf']> 0){
				$mail_corps .= "<p>Ce client déclare pouvoir bénéficier d'une réduction caf.</p>";
				$mail_corps .= "<p>Son justificatif est consultable <a href=\"https://education-environnement-64.org/uploads-justcaf/inscr-".$post['orderid'].".pdf\">en cliquant ici</a></p>";
			}
			if (mail(COPIE_MAIL_DESTINATAIRE,COPIE_MAIL_SUJET,$mail_corps,$mail_entete)) {
			//Le mail est bien expédié
			$ok='ok';
			}
		}
		return $messmail;

	}
}

?>
