<?php
	class InscrLigne {

	private $_id_user,
			$_orderid,
			$_id_article,
			$_id_dates,
			$_article,
			$_dates,
			$_enfant,
			$_id_enf,
			$_transport,
			$_id_transp,
			$_transportLieu,
			$_duree2,
			$_prixTotal,
			$_dernieres_modif,
			$_remise,
			$_ligne_cde,
			$_multiple,
			$_hasCaf;

	public function __construct(array $donnees) {
	$this->hydrate($donnees);
	}

	public function hydrate(array $donnees)  {
	foreach ($donnees as $key => $value) {
	  $method = 'set'.ucfirst($key);

	  if (method_exists($this, $method))  {
		$this->$method($value);
	  }
	}
	}

	public function orderid()  {return $this->_orderid;}
	public function id_user()  {return $this->_id_user;}
	public function id_article()  {return $this->_id_article;}
	public function article()  {return $this->_article;}
	public function dates()  {return $this->_dates;}
	public function prixTotal()  {return $this->_prixTotal;}
	public function transport()  {return $this->_transport;}
	public function id_transp()  {return $this->_id_transp;}
	public function enfant()  {return $this->_enfant;}
	public function id_enf()  {return $this->_id_enf;}
	public function lieu()  {return $this->_lieu;}
	public function transportLieu()  {return $this->_transportLieu;}
	public function duree2()  {return $this->_duree2;}
	public function dernieres_modif()  {return $this->_dernieres_modif;}
	public function remise()  {return $this->_remise;}
	public function multiple()  {return $this->_multiple; }
	public function ligne_cde()  {return $this->_ligne_cde; }
	public function id_dates()  {return $this->_id_dates; }
	public function hasCaf()  {return $this->_hasCaf; }


	public function setid_user($id) { $this->_id_user = $id; }
	public function setorderid($id) { $this->_orderid = $id; }
	public function setid_article($id_article) { $this->_id_article = $id_article; }
	public function setarticle($intitule) { $this->_article = $intitule; }
	public function setdates($dates) { $this->_dates = $dates; }
	public function setprixTotal($prix) { $this->_prixTotal = $prix; }
	public function settransport($a) { $this->_transport = $a; }
	public function setid_transp($a) { $this->_id_transp = $a; }
	public function setenfant($e) { $this->_enfant = $e; }
	public function setid_enf($e) { $this->_id_enf = $e; }
	public function setlieu($l) { $this->_lieu = $l; }
	public function settransportLieu($p) { $this->_transportLieu = $p; }
	public function setduree2($u) { $this->_duree2 = $u; }
	public function setdernieres_modif($u) { $this->_dernieres_modif = $u; }
	public function setremise($e) { $this->_remise = $e; }
	public function setligne_cde($e) { $this->_ligne_cde = $e; }
	public function setmultiple($e) { $this->_multiple = $e; }
	public function setid_dates($e) { $this->_id_dates = $e; }
	public function sethasCaf($e) { $this->_hasCaf = $e; }


}	
?>
