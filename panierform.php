<?php
session_start();

if($_SESSION['isLocalMode']){
	include('inc/dev/variablejoomla-dev.php');
}
else {
	include('inc/variablejoomla.php');	
}

//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');
?>

<div id="panier" class="container">

<?php
//ajout de la mention "responsable légal" si c'est un séjour
$nbArticles=count($_SESSION['artid']);
//au cas ou il y aurait une formation et un sejour dans le panier: on verifie le tableau avec une boucle
for($j=0;$j<$nbArticles;$j++)
{
	if($_SESSION['artid'][$j]>100 && !isset($verifdoublon))
		{echo "<h2>Coordonnées du responsable légal :</h2>";
		 $verifdoublon=true;}
}

//message d'erreur
if(isset($_SESSION['message']))
 {echo $_SESSION['message'];
  unset ($_SESSION['message']);
}
if(isset($_SESSION['messpasword']))
 {echo $_SESSION['messpasword'];
 unset($_SESSION['messpasword']);
}
if(isset($_SESSION['message2']))
 {echo $_SESSION['message2'];
  unset ($_SESSION['message2']);
}
?>

<form method="post" action="recapitulatif.php" data-toggle="validator" role="form" class="maxWidthed">

<div class="form-group">
    <label for="nom" class="control-label">Nom de famille* :</label>
    <input type="text" class="form-control" name="nom" id="inputName" placeholder="Nom de famille" required>
</div>

<div class="form-group">
    <label for="prenom" class="control-label">Prénom* :</label>
    <input type="text" class="form-control" name="prenom" id="inputFName" placeholder="Prénom" required>
</div>

<div class="form-group">
    <label for="prenom" class="control-label">Adresse* :</label>
    <input type="text" class="form-control" name="adress" id="inputAd" placeholder="Adresse" required>
</div>

<div class="form-group">
    <label for="compl" class="control-label">Complement :</label>
    <input type="text" class="form-control" name="compl" id="inputComp" placeholder="Complement">
</div>

<div class="form-group">
    <label for="codep" class="control-label">Code postal* :</label>
    <input type="text" class="form-control" name="codep" id="inputPC" placeholder="Code postal" required data-minlength="5" data-error="5 caractères minimum.">
    <div class="help-block with-errors"></div>
</div>

<div class="form-group">
    <label for="ville" class="control-label">Ville* :</label>
    <input type="text" class="form-control" name="ville" id="inputCity" placeholder="Ville" required>
</div>

<div class="form-group">
    <label for="telfixe" class="control-label">Tel principal* :</label>
    <input type="number" class="form-control" name="telfixe" id="inputTprinc" placeholder="Tel principal" required data-minlength="10" data-error="Ce numéro semble invalide (min 10 caractères numériques)." >
	<div class="help-block with-errors"></div>
</div>

<div class="form-group">
    <label for="telport" class="control-label">Tel secondaire :</label>
    <input type="number" class="form-control" name="telport" id="inputTSec" placeholder="Tel secondaire">
</div>

<div class="form-group">
    <label for="couriel" class="control-label">E-mail* :</label>
    <input type="email" class="form-control" name="couriel" id="inputEmail" placeholder="E-mail" required data-error="Cet email est invalide.">
    <div class="help-block with-errors"></div>
</div>

<div class="form-group">
    <label for="password" class="control-label">Choisissez un mot de passe* :</label>
    <div class="form-group col-sm-6">
		<input type="password" class="form-control" name="password" id="inputPwd" placeholder="Mot de passe" required data-minlength="6" data-error="6 caractères minimum.">
		<div class="help-block with-errors"></div>
	</div>
	<div class="form-group col-sm-6">
		<input type="password" class="form-control" name="password2" id="inputPwd2" placeholder="Confirmer" required data-match="#inputPwd" data-match-error="Les mots de passe ne sont pas identiques.">
		<div class="help-block with-errors"></div>
	</div>
</div>
<div class="form-group">
	<input type="hidden" name="addUSer" value="1">
	<input type="submit" value="Envoyer" class="btn btn-red"/>
</div>


</form>
<div class="clr"></div>
<div class="espace"></div>
<a href="connexion.php" class="preced"> <img src="../templates/educ_env_2/images/panier_precedent.png"  width="136" height="95" alt="Etape précédente"/></a>
</div>
<?php include('inc/html/footer.php');
if(isset($_SESSION['messidpwd2']))
 {echo '<small>'.$_SESSION['messidpwd2'].'</small>';
  unset ($_SESSION['messidpwd2']);
}
?>

