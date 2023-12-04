
<?php

if(isset($_POST['connect'])){ //field from connexion, connection a compte existant
	
	if (isset($_POST['password'])){
		//on verifie le couple email OR dentifiant/password
		//hash_equals($hashed_password, crypt($user_input, $hashed_password))
		$query = 'SELECT clefsecrete,id FROM site_user WHERE email= :email';
		$resultat = $db->prepare($query);
		$resultat -> bindValue('email', $_POST['email'], PDO::PARAM_STR);
		$resultat -> execute();
		$donnees = $resultat->fetch();
		$resultat->closeCursor();
		$resultat = NULL;
		
		if(!empty($donnees)){
			//mode local car j'ai pas hash-equal sur ma version de php local...
			if(isset($_SESSION['isLocalMode'])&& $_SESSION['isLocalMode']) {
				$_SESSION['idla']= $donnees['id'];
			}
			else {
				if(!hash_equals($donnees['clefsecrete'], crypt($_POST['password'], $donnees['clefsecrete']))){
					$Vlogin = false;
					$_SESSION['messidpwd']= '<p>Le couple email/mot de passe est incorrect. <br />Veuillez recommencer.</p>';
					header("location:connexion.php");
					exit();
				}
				else{
					$_SESSION['idla']= $donnees['id'];
				}
			}
		}
		else{
			$_SESSION['messidpwd']= '<p>Cet email n\'existe pas dans notre base de donnée.</p>';
			header("location:connexion.php");
		}	

	}else{
        $_SESSION['messidpwd'] = "pas de mot de passe envoyé";
		header("location:connexion.php");
	}
}
else {
	$_SESSION['messidpwd'] = "pas de post['connect'] envoyé";
}



?>