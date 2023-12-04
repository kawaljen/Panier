<?php



class User{

	  private $_nom,
			  $_prenom,
			  $_ddjs,
			  $_adress,
			  $_complement_add,
			  $_codep,
			  $_ville,
			  $_telfixe,
			  $_telport,
			  $_email,
			  $_naiss_adult,
				$_caf;



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


	public function nom()  {return $this->_nom;}
	public function prenom()  {return $this->_prenom;}
	public function ddjs()  {return $this->_ddjs;}
	public function adress()  {return $this->_adress;}
	public function complement_add()  {return $this->_complement_add;}
	public function codep()  {return $this->_codep;}
	public function ville()  {return $this->_ville;}
	public function telfixe()  {return $this->_telfixe;}
	public function telport()  {return $this->_telport;}
	public function email()  {return $this->_email;}
	public function naiss_adult()  {return $this->_naiss_adult;}
	public function caf()  {return $this->_caf;}

	public function setnom($nom) { $this->_nom = $nom; }
	public function setprenom($prenom) { $this->_prenom = $prenom; }
	public function setddjs($ddjs) { $this->_ddjs = $ddjs; }
	public function setadress($adress) { $this->_adress = $adress; }
	public function setcomplement_add($complement_add) { $this->_complement_add = $complement_add; }
	public function setcodep($codep) { $this->_codep = $codep; }
	public function setville($ville) { $this->_ville = $ville; }
	public function settelfixe($telfixe) { $this->_telfixe = $telfixe; }
	public function settelport($telport) { $this->_telport = $telport; }
	public function setemail($email) { $this->_email = $email; }
	public function setnaiss_adult($naiss_adult) { $this->_naiss_adult = $naiss_adult; }
	public function setcaf($i) { $this->_caf = $it; }

}
	
	
?>
