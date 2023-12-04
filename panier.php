<?php
session_start(); 
$error = '';
include('../panier_tools/conf.php');
include('inc/globalVariables.php');

if(!$_SESSION['isLocalMode']){
	include('inc/variablejoomla.php');	
}
include('../panier_tools/conf.php');
include('../panier_tools/connectbd.php');

include("controller/PanierController.php");
$Panier = new PanierController($db);

//verou de identification pour ne pas inserer sans arret ds la bd
if(isset($_SESSION['verrou'])){unset($_SESSION['verrou']);}


//les renvoies à fonction_panier
$action = (isset($_POST['action'])? $_POST['action']:  (isset($_GET['action'])? $_GET['action']:null ));
if(in_array($action, array('ajout', 'suppression', 'resetPanier'))) {
   switch($action){
         Case "ajout":
			//recuperer l'url de la page précedente
				$_SESSION['pageprec']=$_SERVER['HTTP_REFERER'];
         break;
		Case "suppression":
			$lgncde= (isset($_POST['l'])? $_POST['l']:  (isset($_GET['l'])? $_GET['l']:null )) ;
			$error .= $Panier->supprimerArticle($lgncde);
         break;

      Default:
         break;
   }
}

$lignes = $Panier->recupvar();
$hasSejour = $lignes['hasSejour'];

//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');


?>


<div id="panier">
	<?php if(isset($_SESSION['modeTest'])&& $_SESSION['modeTest']) { 
		echo $_SESSION['modeTestText'];;
	}?>
	
	<?php if(isset($_SESSION['pageprec'])){echo '<a href="'.$_SESSION['pageprec'].'">Retour au site</a>';}
	echo $error;
?>

<?php if ($Panier->creationPanier()):?>
<form method="post" id="panier-qte">
	<table id="panier">
		<tr>
			<td id="titrepan" colspan="4"><h2>Votre panier</h2></td>
		</tr>

	<tr id="fondpan">
		<td>Libellé</td>
		<?php if ($hasSejour):?>
			<td>Nombre d'enfants à inscrire</td>
		<?php endif;?>
		<td>Prix TTC</td>
		<td>Supprimer</td>
	</tr>

	<?php

	if (count($lignes['article']) <= 0)
		{	echo "<tr><td>Votre panier est vide </td></tr></table>";	
		}
	else
		{	
			$hasduree2 = $Panier->getArticleDuree2($_SESSION['artid']); 

			for($j=0;$j<count($lignes['article']);$j++){
				
					$donnees = $lignes['article'][$j]["info"];
					$lgncde=$donnees['id'];

					echo '<tr>';
					echo "<td class=\"tdlibele\"><span >".$donnees['intitule']."</span><br/>";
					if( in_array( $donnees['id'], $hasduree2 )){ 
						echo '<i>Ce séjour se décline en deux durées, '.$donnees['intituleD1'].' et '.$donnees['intituleD2'].'.</i>';
					}		
					echo "</td>";

					echo '<td>';
						if($donnees['id']>100){ //sejour
							echo'<input type="number" name="qte" data-artid="'.$donnees['id'].'" value="'.htmlspecialchars($lignes['article'][$j]["qte"]).'"
										data-artprix="'.$donnees['prix'].'" data-artprix2="';
									if (isset($donnees['prix_duree2'])) {echo $donnees['prix_duree2'] ;}else{echo 0;}
							echo '"/>';
						}
					echo '</td>';
					
					
					echo "<td class=\"tdprix\">";
					if( in_array( $donnees['id'], $hasduree2 )){ 
							echo '<i>Pour '.$donnees['intituleD2'].' :</i> <span class="prix2">'.$donnees['prix_duree2'].'</span> &#8364<br/><i>Pour '.$donnees['intituleD1'].' :</i> ';
						}
						echo '<span class="prix1">'.$donnees['prix'].'</span> &#8364';
					echo "</td>";
					echo "<td class='suppr'><a href=\"".htmlspecialchars("panier.php?action=suppression&l=".rawurlencode($lgncde))."\"><img src=\"../images/banners/bin.png\"/></a></td>";
					echo "</tr>";
				}
					
			}

		?>

	</table>
	<?php
	//$remise = MontantGlobal($prix);
	// if(count($hasduree2)<1){
	// 	echo "<div id=\"tdprixtotal\" ><p>Total : <span>".$_SESSION['total'];
	// 	echo " &#8364 </span></p></div>";
	// }
	
	?>
</form>
<?php endif; ?>
	<?php if (count($lignes['article']) > 0) :?>
		<form method="post" action="connexion.php" id="panier">
			<?php
				
				for($i=0;$i<count($lignes['article']);$i++){
					$donnees = $lignes['article'][$i]["info"];
					$lgncde=$donnees['id'];
					for($j=0;$j<$lignes['article'][$i]["qte"];$j++){
						echo '<input type="hidden" name="article[]" value="'.$lgncde.'">' ;
					}
				}
			?>

			<button type="submit"class="btn btn-blue suivant"> Valider la commande</button>
		</form>
	<?php endif; ?>
</div>

<?php 
	$scriptJS = '<script type="text/javascript" src="./js/ftn.js"></script> ';
	//$scriptJS = '<script type="text/javascript" src="./templates/educ_env_2/js/getPanier.js"></script>';
	include('inc/html/footer.php');
?>
