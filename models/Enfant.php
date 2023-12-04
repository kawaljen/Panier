<?php
class Enfant {

	private
		  $_enf_nom,
		  $_enf_prenom,
		  $_enf_id,
		  $_enf_gender,
		  $_enf_enf_id,
		  $_enf_birthday,
		  $_age;

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

	public function enf_nom()  {return $this->_enf_nom;}
	public function enf_prenom()  {return $this->_enf_prenom;}
	public function enf_id()  {return $this->_enf_id;}
	public function enf_gender()  {return $this->_enf_gender;}
	public function enf_birthday()  {return $this->_enf_birthday;}
	public function enf_enf_id()  {return $this->_enf_enf_id;}
	public function age()  {return $this->_age;}


	public function setenf_nom($e) { $this->_enf_nom = $e; }
	public function setenf_prenom($e) { $this->_enf_prenom = $e; }
	public function setenf_id($e) { $this->_enf_id = $e; }
	public function setenf_gender($e) { $this->_enf_gender = $e; }
	public function setenf_birthday($e) { $this->_enf_birthday = $e; }
	public function setenf_enf_id($e) { $this->_enf_enf_id = $e; }
	public function setage($e) { $this->_age = $e; }

}
?>
