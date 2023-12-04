<?php
$annee=(date('Y'))-5; //calcul de l'année pour verifier la validité de l'age

//nouvel utlisateur, ajout depuis panierform.php
// ou Modifier les entrée User
if(isset($_POST['addUSer'])|| !empty($_POST['action-modif'])){

   //les inscriptions
   if(empty($_POST['action-modif']))
     {
      $ControllerUser = new ControllerUser($db);
	  $message = $ControllerUser -> addUser($_POST);
	  
     }

   //les updates
   else {
      $ControllerUser = new ControllerUser($db);
	  $message = $ControllerUser -> updateUser($_POST);

     }

}


//modifier info enfant
if(isset($_POST['action-modifEnf']) && $_POST['action-modifEnf']=1){
 
    for($j=0; $j<count($_POST['inscenf']); $j++){
      echo $j;
       if(!empty($_POST['nom_f'][$j])){$nom_f=htmlspecialchars($_POST['nom_f'][$j]);}else{$stopenf=true;}
       if(!empty($_POST['pren_f'][$j])){$pren_f=htmlspecialchars($_POST['pren_f'][$j]);}else{$stopenf=true;}
       if(isset($_POST['sex_f'][$j])){ $sex_f=htmlentities($_POST['sex_f'][$j],ENT_QUOTES); }else{$sex_f="garco";}
       if(!empty($_POST['date_j'][$j]) && $_POST['date_j'][$j]<32){$date_j=htmlentities($_POST['date_j'][$j],ENT_QUOTES);}else{$stopenf=true;}
       if(!empty($_POST['date_m'][$j]) && $_POST['date_m'][$j]<13){$date_m=htmlentities($_POST['date_m'][$j],ENT_QUOTES);}else{$stopenf=true;}
       if(!empty($_POST['date_a'][$j])){$date_a=htmlentities($_POST['date_a'][$j],ENT_QUOTES);
                                    $date_b=$date_a.'-'.$date_m.'-'.$date_j;}else{$stopenf=true;}

       if(!isset($stopenf)){
           
           $query = 'UPDATE site_enfant SET enf_nom= :nom_f, enf_prenom= :pren_f, enf_gender= :enf_sex, enf_birthday= :enf_birthday WHERE enf_enf_id= :enf_enf_id AND enf_id= :enf_id';
           $prep = $db->prepare($query);
           $prep->bindValue('nom_f',$nom_f,PDO::PARAM_STR);
           $prep->bindValue('pren_f',$pren_f,PDO::PARAM_STR);
           $prep->bindValue('enf_sex',$sex_f,PDO::PARAM_STR);
           $prep->bindValue('enf_birthday',$date_b);
           $prep->bindValue('enf_enf_id',$j, PDO::PARAM_INT);
           $prep->bindValue('enf_id',$_SESSION['idla'], PDO::PARAM_INT);
           $prep->execute();

           $prep->closeCursor();
           $prep = NULL;
         }
        else {
          echo "champ incomplet";
        }
     }
 }

//ajout un inscrit enfant
if(isset($_POST['action-ajoutEn'])){
  // $_SESSION['hasUpdateEnfant']= array('ligne_cde'=> array(), 'enf_enf_id' => array());
    $_SESSION['hasUpdateEnfant']=array();
    $_SESSION['hasUpdateEnfant']['ligne_cde']=array();
    $_SESSION['hasUpdateEnfant']['enf_enf_id'] = array();
    $_SESSION['hasUpdateEnfant']['age'] = array();

    $stop = false;

   for ($i=0; $i<count($_POST['action-ajoutEn']); $i++){
     unset($stopenf);
     if($_POST['action-ajoutEn'][$i]>0){
        $j = $_POST['inscenfaj'][$i];
        if(!empty($_POST['nom_faj'][$i])){$nom_f=htmlspecialchars($_POST['nom_faj'][$i]);}else{$stopenf=true;}
        if(!empty($_POST['pren_faj'][$i])){$pren_f=htmlspecialchars($_POST['pren_faj'][$i]);}else{$stopenf=true;}
        if(!empty($_POST['sex_faj'][$i])){$sex_f=htmlentities($_POST['sex_faj'][$i],ENT_QUOTES);} else {$sex_f='garco';}
        if(!empty($_POST['date_jaj'][$i]) && $_POST['date_jaj'][$i]<32 ){$date_j=htmlentities($_POST['date_jaj'][$i],ENT_QUOTES);}else{$stopenf=true;}
        if(!empty($_POST['date_maj'][$i]) && $_POST['date_maj'][$i]<13){$date_m=htmlentities($_POST['date_maj'][$i],ENT_QUOTES);}else{$stopenf=true;}
        if(!empty($_POST['date_aaj'][$i]) ){$date_a=htmlentities($_POST['date_aaj'][$i],ENT_QUOTES);
                                      $date_b=$date_a.'-'.$date_m.'-'.$date_j;}else{$stopenf=true;}
     
        //si aucun des champs n'est nuls
        if(!isset($stopenf)){
            $query ='INSERT INTO site_enfant (enf_id, enf_nom, enf_prenom, enf_gender, enf_enf_id, enf_birthday) VALUES (:enf_id, :nom_f, :pren_f, :enf_gender, :enf_enf_id, :enf_birthday)';
            $prep = $db->prepare($query);
            $prep->bindValue('nom_f',$nom_f,PDO::PARAM_STR);
            $prep->bindValue('pren_f',$pren_f,PDO::PARAM_STR);
            $prep->bindValue('enf_id',$_SESSION['idla'],PDO::PARAM_INT);
            $prep->bindValue('enf_gender',$sex_f,PDO::PARAM_STR);
            $prep->bindValue('enf_birthday',$date_b);
            $prep->bindValue('enf_enf_id',$j, PDO::PARAM_INT);
            $prep->execute();
            if(isset($_POST['lgne_cde'][$i])){
              $_SESSION['hasUpdateEnfant']['ligne_cde'][] = $_POST['lgne_cde'][$i];
              $_SESSION['hasUpdateEnfant']['enf_enf_id'][] = $j;
              $_SESSION['hasUpdateEnfant']['age'][] = round((time() - strtotime($date_b)) / 3600 / 24 / 365.242);
            }

            $prep->closeCursor();
            $prep = NULL;
          }
          else{
            $stop = true;
          }

     }
  }
  if($stop){
           $_SESSION['hasUpdateEnfant']['error'] = '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>Des informations manquent sur les enfants que vous voulez inscrire, vérifier les informations personnelles enregistrées sur vous et les enfants en <a href="./identification.php">cliquant ici</a></div>';
  }
  return false;
 }

 ?>
