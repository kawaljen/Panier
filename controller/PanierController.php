<?php

$rootPath = './../';
//include($rootPath."models/Sejour.php");
//include($rootPath."models/Dates.php"); 


class PanierController{
	
    private $_db; 
    private $_artid = array("id"=>array(), "qte"=>array());
    
	  
	public function __construct($db)  { $this->setDb($db);}

	//Set db
	public function setDb(PDO $db)  { $this->_db = $db; }


    private function effaceJoomla($idArticle){
        //on efface dans joomla...
        $tmp=array();
        $mainframe =& JFactory::getApplication('site'); //redondant
        $mainframe->initialise(); 
        $session =& JFactory::getSession();
        $selection = $session->get('selection');

        for ($j=0; $j<count($selection); $j++)
            {
            if($selection[$j]!==$idArticle)
                {$tmp[]=$selection[$j]; }
            }
        $session->clear('selection');
        $session->set('selection',$tmp);

    }

    public function creationPanier(){
        // if (!isset($_SESSION['artid'])){
        //     $_SESSION['artid']=array();;
        //     $_SESSION['artid']['id']=array();
        //     $_SESSION['artid']['qte'] = array();
             $_SESSION['artid']['verrou'] = false;
        // }
        return true;
    }

    public function getArticleDuree2(){
        $hasDuree2 = array();
        $query ='SELECT DISTINCT * FROM dates WHERE ';
        for($j=0;$j<count($this->_artid['id']);$j++){
            if ( is_numeric($this->_artid['id'][$j])){
                $query .= 'id_article = '.$this->_artid['id'][$j] ; 
                if($j < (count($this->_artid['id']) - 1)){
                    $query .= ' OR ';
                }
            }
		}
        $prep = $this->_db->prepare($query);
		$prep->execute();
		while ($donnees = $prep->fetch())
			{	
                if($donnees['id_duree']>1){
                     $hasDuree2[]= $donnees['id_article'];
                }
            }
        return $hasDuree2;
    }



    public function recupvar(){
        if(!$_SESSION['isLocalMode']){
            $mainframe =& JFactory::getApplication('site'); // redondant
            $mainframe->initialise(); 
            $session =& JFactory::getSession();
            $selection = $session->get('selection');
        }
        else { $selection = array (2,2);}
        if ( $this->creationPanier()){
			$lignes = array( "article"=>array(), "hasSejour" => false ) ;
            for ($i=0; $i<count($selection); $i++){	
                $key = array_search($selection[$i], $this->_artid['id']);
                if ($key !== false) {
                     $this->_artid['qte'][$key]++;
                }
                else {        
                      $this->_artid['id'][] = $selection[$i];
                      $this->_artid['qte'][] = 1;
                }
            }

            $query = $this->_db->prepare('SELECT * FROM sejour WHERE id = ?');
            for ($i=0; $i<count($this->_artid['id']); $i++){
                $qte = $this->_artid['qte'][$i];

                $query ->bindValue(1, $this->_artid['id'][$i], PDO::PARAM_INT);												
                $query->execute();

                while ($donnees = $query->fetch()){
                        if($donnees['id']>100){
                            $lignes['hasSejour'] = true;
                        }
                        $lignes['article'][]= array ("info" => $donnees, "qte"=>$qte);
                    }	
                    
                }
                $query->closeCursor();
            return $lignes;
        }
    }


    public function supprimerArticle($idArticle){
        $error ='';
        if( is_numeric($idArticle)){;
            if ($this->creationPanier()){  

                $tmpid = array();
                $qteProduit = array();

                for($i = 0; $i < count($this->_artid['id']); $i++){
                    if ($this->_artid['id'][$i] !== $idArticle){
                        $tmpid[]=$this->_artid['id'][$i];
                        $qteProduit[]=$this->_artid['qte'][$i];
                    }
                }
                $this->_artid['id'] = $tmpid;
                $this->_artid['qte']=$qteProduit;
                $_SESSION['artid']['verrou'] =false;
                if(!$_SESSION['isLocalMode']){
                     $this->effaceJoomla($idArticle);
                }

            }
            else { $error .="Un problème est survenu veuillez contacter l'administrateur du site."; }

        } else {
            $error .= " L'id de l'article n'est pas numérique";
        }
        return $error;
    }

}

?>