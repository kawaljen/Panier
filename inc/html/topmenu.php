<div id="haut">
			<div id="burgermenu" class="glyphicon glyphicon-menu-hamburger"></div>
			<div id="logo"><a href="../index.php"></a></div>
			<div id="menuhaut">

				
				<div class="moduletable_menu">

					<ul class="menu">
						<li class="item-101"><a href="../index.php" >ACCUEIL</a></li>
						<li class="item-102"><a href="../index.php/presentation" >PRESENTATION de l'association</a></li>
						<li class="item-131"><a href="../index.php/publications" >PUBLICATIONS-centre de ressources</a></li>
						<li class="item-106"><a href="../index.php/contact" >CONTACT/ Comment venir</a></li>
						<li class="glyphicon glyphicon-user dfg"><a href="../espace_perso/" >Mon compte</a></li>
						<?php if (isset($_SESSION['idla'])) :?>
							<li class="item-106 dfg"><a href="./connexion.php?deconnect" >Deconnexion</a></li>
						<?php endif;?>
					</ul>
				</div>
			</div>
		</div>
		<div id="cadre3">
			<div id="menu">
				<div class="petittiret">
					<ul >
						<li><a href="panier.php" ><span id="gdeli1"></span>Votre panier</a></li>
						<li><span id="gdeli2"></span>Connexion</li>
						<li><span id="gdeli2"></span>Renseignements personnels</li>
						<?php if (isset($hasSejour) && $hasSejour):?>
							<li><span id="gdeli3"></span>Choix des dates et options</li>
						<?php else :?>
							<li><span id="gdeli3"></span>Choix des dates</li>
						<?php endif;?>
						<li><span id="gdeli4"></span>Paiement</li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
