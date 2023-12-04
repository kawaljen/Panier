<?php
class Sejour{
	
   private $_intitule,
          $_id,
          $_prix,
          $_prix_duree2,
          $_prix_reducCaf,
          $_acompte,
          $_age_min,
          $_age_max,
          $_prix_reducCafD2,
          $_intituleD1,
          $_intituleD2;

 
  
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



  public function intitule()  {return $this->_intitule;}
  public function id()  {return $this->_id;}
  public function prix()  {return $this->_prix;}
  public function acompte()  {return $this->_acompte;}
  public function age_min()  {return $this->_age_min;}
  public function age_max()  {return $this->_age_max;}
  public function prix_duree2()  {return $this->_prix_duree2;}
  public function prix_reducCaf()  {return $this->_prix_reducCaf;}
   public function prix_reducCafD2()  {return $this->_prix_reducCafD2;}
   public function intituleD1()  {return $this->_intituleD1;}
   public function intituleD2()  {return $this->_intituleD2;}
  
  public function setintitule($intitule)  { if (is_string($intitule)) { $this->_intitule = $intitule;}}
  
  public function setid($id) { $this->_id = $id; }
  public function setprix($prix) { $this->_prix = $prix; }
  public function setacompte($acompte) { $this->_acompte = $acompte; }
  public function setage_min($age_min) { $this->_age_min = $age_min; }  
  public function setage_max($age_max) { $this->_age_max = $age_max; }
  public function setprix_duree2($e) { $this->_prix_duree2 = $e; }
  public function setprix_reducCaf($e) { $this->_prix_reducCaf = $e; }
  public function setprix_reducCafD2($e) { $this->_prix_reducCafD2 = $e; }
  public function setintituleD1($e) { $this->_intituleD1 = $e; }
  public function setintituleD2($e) { $this->_intituleD2 = $e; }
  
}	
?>
