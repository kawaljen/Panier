	<?php
class Dates{
	
  private $_id_dates,
          $_id_article,
          $_dates,
          $_etatComplet,
          $_id_duree;

 
  
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


  public function id_dates()  {return $this->_id_dates;}
  public function id_article()  {return $this->_id_article;}
  public function dates()  {return $this->_dates;}
  public function etatComplet()  {return $this->_etatComplet;}
  public function id_duree()  {return $this->_id_duree;}

  
 
  public function setid_dates($id_dates) { $this->_id_dates = $id_dates; }
  public function setdates($dates) { $this->_dates = $dates; }
  public function setid_article($id_article) { $this->_id_article = $id_article; }
  public function setetatComplet($etatComplet) { $this->_etatComplet = $etatComplet; }  
  public function setid_duree($e) { $this->_id_duree = $e; }
  
}		
?>
