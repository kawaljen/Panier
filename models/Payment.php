<?php
class Payment {

	private
		  $_idpayment,
		  $_orderid,
		  $_iduser,
		  $_amount,
		  $_acompte,
		  $_timestamp;

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

	public function idpayment()  {return $this->_idpayment;}
	public function orderid()  {return $this->_orderid;}
	public function iduser()  {return $this->_iduser;}
	public function amount()  {return $this->_amount;}
	public function acompte()  {return $this->_acompte;}
	public function timestamp()  {return $this->_timestamp;}


	public function setidpayment($e) { $this->_idpayment = $e; }
	public function setorderid($e) { $this->_orderid = $e; }
	public function setiduser($e) { $this->_iduser = $e; }
	public function setamount($e) { $this->_amount = $e; }
	public function setacompte($e) { $this->_acompte = $e; }
	public function settimestamp($e) { $this->_timestamp = $e; }


}

?>
