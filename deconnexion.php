<?php 
session_start();

//includes html
include('inc/html/head.php');
include('inc/html/topmenu.php');
?>

			
<div id="panier">
<?php echo $_SESSION['messidpwd'];?>
<p>Votre session a expiré. Vous pouvez vous reconnecter en cliquant sur l'icône mes inscription.</p>
</div>
</div>
<div class="espace"></div>
</div>
<?php include('inc/html/footer.php'); ?>
