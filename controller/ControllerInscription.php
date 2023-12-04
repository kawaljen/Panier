<?php

$rootPath = './../';
include($rootPath."models/InscrLigne.php");
include($rootPath."models/Sejour.php");
include($rootPath."models/Enfant.php");
//include($rootPath."models/User.php");
include($rootPath."models/Dates.php");
include($rootPath."models/Transport.php");

class ControllerInscription{
	
    private $_db, 
			$_idUser; 
	  
	public function __construct($db)  { $this->setDb($db);}

	//Set db
	public function setDb(PDO $db)  { $this->_db = $db; 
									  $this->_idUser = (is_numeric($_SESSION['idla']) ? $_SESSION['idla'] : 0 );}

	
	public function getInscr(){		
			$lignes = array( "inscriptions"=>array(), "hasSejour" => false, "stopComplet"=> true, "hasCaf"=>false ) ;
			$temp = array();

			$q = $this->_db->query('SELECT DISTINCT * FROM inscription JOIN sejour ON id_article=id WHERE id_user = '.$this->_idUser.' AND etat =\'en attente\' ');
			 while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
						// if($donnees['id_article']<100){
						// 	$lignes['form'][] = array ('ins' => new InscrLigne($donnees), 'sej' => new Sejour($donnees)); 
						// }
						// else {
						// 	$lignes['sejour'][] = array ('ins' => new InscrLigne($donnees), 'sej' => new Sejour($donnees));
						// 	$lignes['hasSejour'] = true;
						// }	
						if($donnees['id_article']>100){
							$lignes['hasSejour'] = true;
						}
						if($donnees['remise']=== 'caf'){
							$lignes['hasCaf'] = true;
						}
						$temp[] = array ('ins' => new InscrLigne($donnees), 'sej' => new Sejour($donnees));
					}
			$q->closeCursor();
			$q = NULL;	


			//recuperer les dates 
			$isThereComplet = array();
			//$dates = array();
			if(isset($temp)){
				for( $i=0; $i < count($temp) ; $i++){
					 $htmlDates = array();
					 $dates = $this->getdatesArticle( $temp[$i]['ins']->id_article() );
					 if (count($dates)>0){
						$htmlDates = $this->getHtmlDates($temp[$i]['ins'],$dates, $temp[$i]['sej']);
						$isThereComplet[] = $htmlDates[1];
					 }
					 else{
						 $isThereComplet[] = true;
						 $htmlDates[]= 'complet - il n\'y a plus de dates disponible';
					 }
					$lignes["inscriptions"][]= array_merge((array)$temp[$i], array('htmlDates' => $htmlDates[0]));						 
				}
			}
							
			$lignes['stopComplet'] = in_array(true, $isThereComplet);

			//va chercher les suplement info pour les sejours
			if ($lignes['hasSejour']){
				$lignes = array_merge($lignes, $this->getInfoSejour());
			}
					
		    return $lignes;
	}
		

	public function ajoutCaf($post, $limit){
		if((isset($post['quotientCaf']) && $post['quotientCaf'] < $limit) && isset($_FILES['fichierCaf'])){

			$query =$this->_db->query('UPDATE inscription SET remise =\'caf\' WHERE id_user ='.$this->_idUser.' AND etat =\'en attente\' ');
			$query->closeCursor();
			$query = NULL;
			$dossier = 'upload/';
			$fichier = basename($_FILES['fichierCaf']['name']);
			if(move_uploaded_file($_FILES['fichierCaf']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
			{
				echo 'Upload effectué avec succès !';
			}
			else //Sinon (la fonction renvoie FALSE).
			{
				echo 'Echec de l\'upload !';
			}
		}
		else {
			$query =$this->_db->query('UPDATE inscription SET remise =0 WHERE id_user ='.$this->_idUser.' AND etat =\'en attente\' ');
			$query->closeCursor();
			$query = NULL;
			return  false;
		}
		return true;
	}

	public function deleteInscr($lgncde){
		if(isset($lgncde) && is_numeric($lgncde)){
				$query ='DELETE FROM inscription WHERE id_user= '.$this->_idUser.' AND ligne_cde= :ligne_cde';
				$prep = $this->_db->prepare($query);
				$prep->bindValue('ligne_cde', $lgncde, PDO::PARAM_INT);
				$prep->execute();
				
				$prep->closeCursor();
				$prep = NULL;

		 }
		 else {
			 echo 'une erreur est survenue';
		 }
	}

//selectionner les dates d'un article
	private function getdatesArticle($id){		
			$lignes = array();

			if (is_numeric($id))	{
				    $q = $this->_db->query('SELECT DISTINCT * FROM dates WHERE etatEffacer=0 AND id_article = '.$id.' ORDER BY id_duree');
					 while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
							if ($donnees['id_duree']<2){
								$lignes ['duree1'][] = new Dates($donnees);	
							}
							else{
								$lignes['duree2'][] = new Dates($donnees);	
							}
						}
					$q->closeCursor();
					$q = NULL;
					}

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
				$q = $this->_db->query('SELECT DISTINCT enf_prenom FROM site_enfant WHERE enf_id = '.$this->_idUser .' AND enf_enf_id ='.$id);
				while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
								$enfants = $donnees['enf_prenom'];
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
				$q = $this->_db->query('SELECT DISTINCT prix, prix_duree2, prix_reducCaf, intitule FROM sejour WHERE id = '.$id);
				while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
								$rep = new Sejour($donnees);
						}
				$q->closeCursor();
				$q = NULL;		
			}

			return $rep;

	}

	private function getInfoSejour(){		
			$enfants = array();
			$transport = array();

			$q = $this->_db->query('SELECT DISTINCT * FROM site_enfant WHERE enf_id = '.$this->_idUser );
			while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
							$enf = new Enfant($donnees);
							$enf->setage(round((time() - strtotime($donnees['enf_birthday'])) / 3600 / 24 / 365.242));
							$enfants [] = $enf;
					}
			$q->closeCursor();
			$q = NULL;

			$q = $this->_db->query('SELECT DISTINCT * FROM transport ');
			while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
							$transport [] = new Transport($donnees);
					}
			$q->closeCursor();
			$q = NULL;			


			return array ('enfants' => $enfants, 'transport'=> $transport);
		}

	private function getHtmlDates($ins, $dates, $sej){	
		$htmlDate = '';	
		$isThereComplet = array();
		$isThereComplet[] = true ;
		//$duree2 = 1;
		
		$htmlDate .= '<select class="select-dates" name="dates'.$ins->ligne_cde().'">';

		if( $ins->id_dates() > 0){ 
			$htmlDate .= '<option value="'.$ins->id_dates().'">'.$ins->dates().'</option>';
			// if( $ins->duree2() > 1){ $duree2 = $ins->duree2(); }
		}

		if(!empty($dates['duree2'])){
			$htmlDate .='<option disabled> '.$sej->intituleD1().' </option>';
		}
		for ($i = 0 ; $i < count($dates['duree1']); $i++) {
			$disabled = '';
			$complet = '';
			if ($dates['duree1'][$i]->etatComplet() ==="complet"){$disabled= "disabled"; $complet =' -COMPLET-';}
			else {$isThereComplet[] = false ;}
			$htmlDate .='<option value="'.$dates['duree1'][$i]->id_dates().'" '.$disabled.'>'.$dates['duree1'][$i]->dates().$complet.'</option>';
		}
		if(!empty($dates['duree2'])){
			$htmlDate .='<option disabled> '.$sej->intituleD2().' </option>';
			if ($dates['duree2'][$i]->etatComplet() ==="complet"){$disabled= "disabled"; $complet =' -COMPLET-';}
			else {$isThereComplet[] = false ;}
			for ($i = 0 ; $i < count($dates['duree2']); $i++) {
				$htmlDate .='<option class="duree2" value="'.$dates['duree2'][$i]->id_dates().'" '.$disabled.'>'.$dates['duree2'][$i]->dates().$complet.'</option>';
			}
		}

		$htmlDate .= '</select>';
		// if(!empty($dates['duree2'])){ 
		// 	$htmlDate .= '<input type="hidden" name="duree2" value="'.$duree2.'"/>';
		// }
		$stopComplet = !in_array(false, $isThereComplet);
		return array ( $htmlDate, $stopComplet);
	}



		
// AJOUT DE NOUVEAU ARTICLE ET CREATION D'UNE LIGNE DANS LA TABLE INSCRIPTION EN ETAT EN ATTENTE
	//on efface les insriptons en attente pour eviter les inscirptons perpetuelles dans la bd
		//Si on souhaite changer une inscription qui est en etat mail env
			//on change les inscriptions 'mail env' en 'en attente'
	//On recherche le numero de ligne de commande pour definir ou commencer ligne_cde
	// on commence la traduction du tableau des session en entrée bd
function ajoutNewpanier() {
	$query = $this->_db->query('DELETE FROM inscription WHERE id_user ='.$this->_idUser.' AND etat =\'en attente\'');
	$query->closeCursor();
	$query = NULL;

	// orderid
	$query = $this->_db->query('SELECT MAX(orderid) as max FROM inscription');
	$donnees = $query->fetch();
	$order_id= $donnees['max'];
	$query->closeCursor();
	$query = NULL;
	$order_id++;
	$_SESSION["orderid"] = $order_id;
	
	//if ($possibiliteAjout) {
	$query =$this->_db->query('UPDATE inscription SET etat =\'en attente\', orderid = '.$order_id.' WHERE id_user ='.$this->_idUser.' AND etat =\'mail env\'');
	$query->closeCursor();
	$query = NULL;
	//}
		

	$ajoutlignecde=0;//var pour definir ou commencer ligne_cdesuivant l'ensemble inscr user
	$query = $this->_db->query('SELECT ligne_cde FROM inscription WHERE id_user ='.$this->_idUser);							
		while ($donnees = $query->fetch())
			{ 
				$ajoutlignecde = $donnees['ligne_cde'];
			}
	$query->closeCursor();
	$query = NULL;

	//insere les entrées articles
	if(isset($_SESSION['artid']['id']))
		{ 
			for($j=0;$j<count($_SESSION['artid']['id']);$j++)
				{	
				//on traite la qte avec la boucle for
				for($a=0; $a<$_SESSION['artid']['qte'][$j]; $a++)
					{								
							//---------------------------------------------------------------------------------------------------------------------------
							$ajoutlignecde++; 
							$query ='INSERT INTO inscription (id_user, id_article, ligne_cde, etat, orderid) VALUES (:id_user, :id_article, :ligne_cde, :etat, :orderid)';
							$prep = $this->_db->prepare($query);
							$prep->bindValue('id_user', $this->_idUser, PDO::PARAM_INT);
							$prep->bindValue('id_article',$_SESSION['artid']['id'][$j],PDO::PARAM_INT);
							$prep->bindValue('ligne_cde', $ajoutlignecde, PDO::PARAM_INT);
							$prep->bindValue('etat', 'en attente', PDO::PARAM_STR);
							$prep->bindValue('orderid', $order_id , PDO::PARAM_INT);
							$prep->execute();

							$prep->closeCursor();
							$prep = NULL;
							
					}
				
				}	
			$_SESSION['verrou']=true;
		}
	
	}
	public function getHtmlBafa($intitule, $time, $ddjs){
			$formBafa= '';
			if(  strpos($intitule,'bafa') || strpos($intitule,'BAFA') || strpos($intitule,'BAFD') || strpos($intitule,'bafd') ){
				$d = (!empty($time) ? date("d", $time ): null);
				$m = (!empty($time)? date('m', $time): null);
				$y = (!empty($time) ? date('Y', $time):null);

				$formBafa= '<div class="form-group">
							<label for="datead_f" class="control-label">Date de naissance (Au format JJ/MM/AAAA):</label>
							<div class="form-group dates">
								<input class="form-control" type="number" required data-day="day" name="datead_j" value="'.$d.'" size="2"/>
								<div class="help-block with-errors"></div>
							</div>

							<div class="form-group dates">
								<span>/</span><input class="form-control" type="number" required data-month="month" name="datead_m" value="'.$m.'" size="2"/>
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group dates">
								<span>/</span><input class="form-control" type="number" required data-year="year" name="datead_a" value="'.$y.'" size="4"/>
								<div class="help-block with-errors"></div>
							</div>
						<!--	<div class="help-block with-errors">(Au format JJ/MM/AAAA)</div> -->
						</div>

						<div class="form-group">
							<label for="ddjs" class="control-label">Numéro de dossier DDJS :</label>
							<input type="text" class="form-control" name="ddjs" id="inputddjs" placeholder="DDJS" value="'.$ddjs.'" >
						</div>
						<input type="hidden" name="updateUser" value="true" />';
				}

				return $formBafa;

			}

	

}	
	
?>
