<?php
class Transport{
	
   private $_id_transp,
           $_prix_transp,
           $_lieu,
           $_duree2;

 
  
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



  public function id_transp()  {return $this->_id_transp;}
  public function prix_transp()  {return $this->_prix_transp;}
  public function lieu()  {return $this->_lieu;}
  public function duree2()  {return $this->_duree2;}

  public function setid_transp($i) { $this->_id_transp = $i; }
  public function setprix_transp($i) { $this->_prix_transp = $i; }
  public function setlieu($i) { $this->_lieu = $i; }
  public function setduree2($i) { $this->_duree2 = $i; }
 
}	
?>
