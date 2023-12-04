<?php

	// On affiche l'identite de utilisateur, on en profite pour inserer les enfants dans un tableau pour le select
	$query ='SELECT DISTINCT *, DAY(naiss_adult) AS jour, MONTH(naiss_adult) AS mois, YEAR(naiss_adult) AS annee  FROM site_user WHERE id = ?';
	$prep = $db->prepare($query);
	$prep->bindValue(1, $_SESSION['idla'], PDO::PARAM_INT);
	$prep->execute();

		while ($donnees = $prep->fetch())
			{
				if (!isset($Videntite))
					{
						$Videntite=1;
						$noddjs = false;
						$ddjs ='';
						$time = strtotime($donnees['naiss_adult']);
                        $id=$donnees['id'];
										
						echo '<div id="recap"><p><br/>Vous êtes : <span class="hilight">'.$donnees['nom'].' '. $donnees['prenom'].'</span></p>';
						echo '<p>Adresse : <span class="hilight">'.$donnees['adress'].' '. $donnees['codep'].' '. $donnees['ville'].'</span></p>';
						echo '<p> Email : <span class="hilight">'.$donnees['email'].'</span></p>';
                        echo '<p> Téléphone : <span class="hilight">'.$donnees['telfixe'].'</span></p>';
						if(!empty($donnees['telport'])){ echo '<p> Téléphone portable : <span class="hilight">'.$donnees['telport'].'</span></p>';}
						if(empty($time) && strlen($donnees['ddjs'])<=1 ){
							$noddjs= true;
						}
                        else { echo '<br/>';}
						if(!empty($time)){
							echo'<p> Date de naissance : <span class="hilight">'. date('d', $time).'/'.date('m', $time).'/'.date('Y', $time).'</span></p></div>';
						}
						if(!empty($donnees['ddjs'])){
							$ddjs = $donnees['ddjs'];
                            echo '<p> DDJS : <span class="hilight">'.$donnees['ddjs'].'</span></p>';
						}

                        if(isset($isPagePaiement)){
                        //pour le mail..
                            $nom=$donnees['nom'];
                            $prenom=$donnees['prenom'];
                            $couriel = $donnees['email'];
                            $adress=$donnees['adress'];
                                if(isset($donnees['complement_add'])){$complement=$donnees['complement_add'];}
                            $codep=$donnees['codep'];
                            $ville=$donnees['ville'];

                            if(!empty($donnees['telfixe'])){$telephone=$donnees['telfixe'];}else {$telephone=0;}
                            if(!empty($donnees['ddjs'])){$ddjs=$donnees['ddjs'];}else {$ddjs=0;}
                            if(!empty($time)){ $naiss_adult = date('d', $time).'/'.date('m', $time).'/'.date('Y', $time);}else{$naiss_adult = 0;}
                        }

					}
			}
	$prep->closeCursor();
	$prep = NULL;
    
?>