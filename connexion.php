<?php
session_start(); 

if($_SESSION['isLocalMode']){
	include('inc/dev/variablejoomla-dev.php');
}
else {
	include('inc/variablejoomla.php');	
}

if(isset($_GET["deconnect"])){
	unset($_SESSION['idla']);
}

if(isset($_SESSION['idla'])){
	header("location:recapitulatif.php");
}
//$_SESSION['derniereaction']=time();
//verou de identification pour ne pas inserer sans arret ds la bd
if(isset($_SESSION['verrou'])){unset($_SESSION['verrou']);}
if(isset($_POST["article"])){
	$_SESSION['artid']=array();
	for ($i=0; $i<count($_POST["article"]); $i++){
				$_SESSION['artid'][] = htmlentities($_POST["article"][$i],ENT_QUOTES);
	}
}

if(isset($_POST["article"]) && !$_SESSION['isLocalMode']){	
	$session = & JFactory::getSession();	
	$joomlaVariable=array();
	for ($i=0; $i<count($_POST["article"]); $i++){
		$joomlaVariable[]=htmlentities($_POST["article"][$i],ENT_QUOTES);					
	}
	$session->set('selection',$joomlaVariable);

}

//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');

	?>
	<div id="panier">
	<?php if(isset($_SESSION['pageprec'])){echo '<a href="'.$_SESSION['pageprec'].'">Retour au site</a>';}?>
	<div id="identif_blocks">
		<div class="identif_block col-sm-5">
			<div class="border-dashed-rounded">
				<?php if(isset($_SESSION['messidpwd'])){echo $_SESSION['messidpwd']; unset($_SESSION['messidpwd']);} ?>
				<div class="titre"><p> <span></span>  Déjà inscrit ? </p></div>

				<p> Veuillez vous connecter : </p>


				<form action="recapitulatif.php" method="post" id="form" data-toggle="validator" role="form" >
					<div id="block">

						<div class="form-group">
							<label for="email">Email de votre compte* :</label>
							<input type"email" name="email" class="form-control" required data-error="Requis">
							 <div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label for="password">Mot de passe :</label>
							<input type="password" name="password" class="form-control" required data-error="Requis">
							 <div class="help-block with-errors"></div>
						</div>
						
						<input type="hidden" name="connect" value="1">
						<input type="submit" value="Se connecter" class="btn btn-blue"/>

						<small>Mot de passe oublié ? <a href="/espace_perso/reset.php">Cliquez ici</a></small>
						<p></p><small>* Email enregistré pour votre compte, votre email remplace l'ancien 'identifiant' pour vous connecter.</small>
					</div>
				</form>
			</div>
		</div>

		<div class="identif_block col-sm-5  col-sm-offset-1">
			<div class="border-dashed-rounded">
				<div class="titre"><p> <span></span>  Nouveau client ? </p></div>
				<p> Créer un compte utilisateur :</p>
				<a href="panierform.php" class="btn btn-red">S'inscrire</a>
			</div>
		</div>
	</div>
	<div class="clr"></div>
	
<a href="panier.php" class="preced"> <img src="../templates/educ_env_2/images/panier_precedent.png"  width="136" height="95" alt="Etape précédente"/></a>
</div>
<?php include('inc/html/footer.php');?>
