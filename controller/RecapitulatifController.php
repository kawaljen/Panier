<?php

$rootPath = './../';
include($rootPath."models/InscrLigne.php");
include($rootPath."models/Sejour.php");
include($rootPath."models/Enfant.php");
//include($rootPath."models/User.php");
include($rootPath."models/Dates.php");
include($rootPath."models/Transport.php");

class RecapitulatifController{
	
    private $_db, 
			$_idUser,
			$_orderid; 
	  
	public function __construct($db)  { $this->setDb($db);}

	//Set db
	public function setDb(PDO $db)  { $this->_db = $db; 
									  $this->_idUser = (is_numeric($_SESSION['idla']) ? $_SESSION['idla'] : 0 );
									  $this->_orderid = (isset($_SESSION['orderid']) && is_numeric($_SESSION['orderid']) ? $_SESSION['orderid'] : 0 );
									  }

	
	public function getInscr(){		
			$lignes = array( "inscriptions"=>array(), "hasSejour" => false, "stopComplet"=> true, "hasCaf"=>false, "mailEnv"=>false, "noCafReduc" => true ) ;
			$temp = array();
	
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
						if($donnees['prix_reducCaf']>0 || $donnees['prix_reducCafD2']>0){
							$lignes['noCafReduc'] = false;
						}
						$temp[] = array ('ins' => new InscrLigne($donnees), 'sej' => new Sejour($donnees));
					}
			$prep->closeCursor();
			$prep = NULL;	


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
		

	public function ajoutCaf($post, $limit, $fichierCaf){

		$rep = '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Réduction non applicable :';
		$ok = false;

		if(isset($post['quotientCaf']) && $post['quotientCaf'] < $limit){

			$dossier = '/home/xndclicsp/www/uploads-justcaf/';
			$fichier = 'inscr-'.$_SESSION["orderid"].'.pdf';
			$taille_maxi = 10000000;
			$taille = filesize($fichierCaf['tmp_name']);
			$extensions = array('pdf');
			$extension = strtolower(  substr(  strrchr($fichierCaf['name'], '.')  ,1)  );
			//Début des vérifications de sécurité...
			if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
			{
				$erreur = 'Vous devez uploader un fichier de type pdf.';
			}
			if($taille>$taille_maxi)
			{
				$erreur = 'Le fichier est trop gros...';
			}		
			if ($fichierCaf['error'] > 0) $erreur = "Erreur lors du transfert";


			if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
			{
				//On formate le nom du fichier ici...
				$fichier = strtr($fichier, 
					'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
					'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
				if(move_uploaded_file($fichierCaf['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
				{
					$rep = '<div class="bg-success"><i>Nous avons bien reçu votre justificatif<br/></i>';
					$ok = true;

					$query =$this->_db->query('UPDATE inscription SET hasCaf =1 WHERE id_user ='.$this->_idUser.' AND etat =\'en attente\' ');
					$query->closeCursor();
					$query = NULL;
				}
				else {  //Sinon (la fonction renvoie FALSE)
					$this->resetCafInscr();
					$rep .= 'Echec de l\'upload !';
				}
			}
			else {
				$this->resetCafInscr();
				$rep .= $erreur;
			}
		}
		else {
			$this->resetCafInscr();
			$rep .= 'vous n\'avez pas rempli les champs du formulaire';
		}
		return array('ok' => $ok , 'rep'=> $rep.'</div>');
	}

	// public function deleteInscr($lgncde){
	// 	if(isset($lgncde) && is_numeric($lgncde)){
	// 			$query ='DELETE FROM inscription WHERE id_user= '.$this->_idUser.' AND ligne_cde= :ligne_cde';
	// 			$prep = $this->_db->prepare($query);
	// 			$prep->bindValue('ligne_cde', $lgncde, PDO::PARAM_INT);
	// 			$prep->execute();
				
	// 			$prep->closeCursor();
	// 			$prep = NULL;

	// 	 }
	// 	 else {
	// 		 echo 'une erreur est survenue';
	// 	 }
	// }

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
		
		$htmlDate .= '<select class="select-dates form-control" name="dates'.$ins->ligne_cde().'">';

		if( $ins->id_dates() > 0){ 
			$htmlDate .= '<option value="'.$ins->id_dates().'">'.$ins->dates().'</option>';
			// if( $ins->duree2() > 1){ $duree2 = $ins->duree2(); }
		}

		if(!empty($dates['duree2'])){
			$htmlDate .='<option disabled>Séjour '.$sej->intituleD1().' </option>';
		}
		for ($i = 0 ; $i < count($dates['duree1']); $i++) {
			$disabled = '';
			$complet = '';
			if ($dates['duree1'][$i]->etatComplet() ==="complet"){$disabled= "disabled"; $complet =' -COMPLET-';}
			else {$isThereComplet[] = false ;}
			$htmlDate .='<option value="'.$dates['duree1'][$i]->id_dates().'" '.$disabled.'>'.$dates['duree1'][$i]->dates().$complet.'</option>';
		}
		if(!empty($dates['duree2'])){
			$htmlDate .='<option disabled>Séjour '.$sej->intituleD2().' </option>';
			if ($dates['duree2'][$i]->etatComplet() ==="complet"){$disabled= "disabled"; $complet =' -COMPLET-';}
			else {$isThereComplet[] = false ;}
			for ($i = 0 ; $i < count($dates['duree2']); $i++) {
				$htmlDate .='<option class="duree2" value="'.$dates['duree2'][$i]->id_dates().'" '.$disabled.'>'.$dates['duree2'][$i]->dates().$complet.'</option>';
			}
		}

		$htmlDate .= '</select>';

		$stopComplet = !in_array(false, $isThereComplet);
		return array ( $htmlDate, $stopComplet);
	}

	private function resetCafInscr(){
			$query =$this->_db->query('UPDATE inscription SET hasCaf =0 WHERE id_user ='.$this->_idUser.' AND etat =\'en attente\' ');
			$query->closeCursor();
			$query = NULL;
	}

	public function ajoutNewpanier() {
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
		$this->_orderid = $order_id;
			
		$ajoutlignecde=0;//var pour definir ou commencer ligne_cdesuivant l'ensemble inscr user
		$query = $this->_db->query('SELECT ligne_cde FROM inscription WHERE id_user ='.$this->_idUser);							
			while ($donnees = $query->fetch())
				{ 
					$ajoutlignecde = $donnees['ligne_cde'];
				}
		$query->closeCursor();
		$query = NULL;

		//insere les entrées articles
		if(isset($_SESSION['artid'])){ 
				for($j=0;$j<count($_SESSION['artid']);$j++)
					{	
					//on traite la qte avec la boucle for
					// for($a=0; $a<$_SESSION['artid']['qte'][$j]; $a++)
					// 	{								
								//---------------------------------------------------------------------------------------------------------------------------
								$ajoutlignecde++; 
								$query ='INSERT INTO inscription (id_user, id_article, ligne_cde, etat, orderid) VALUES (:id_user, :id_article, :ligne_cde, :etat, :orderid)';
								$prep = $this->_db->prepare($query);
								$prep->bindValue('id_user', $this->_idUser, PDO::PARAM_INT);
								$prep->bindValue('id_article',$_SESSION['artid'][$j],PDO::PARAM_INT);
								$prep->bindValue('ligne_cde', $ajoutlignecde, PDO::PARAM_INT);
								$prep->bindValue('etat', 'en attente', PDO::PARAM_STR);
								$prep->bindValue('orderid', $order_id , PDO::PARAM_INT);
								$prep->execute();

								$prep->closeCursor();
								$prep = NULL;
								
						// }
					
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

