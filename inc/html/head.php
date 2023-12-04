<!DOCTYPE html>

<head>
	<title>Votre panier</title>
	<link href='https://fonts.googleapis.com/css?family=Amatic+SC' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="../templates/educ_env_2/css/bootstrap.css" type="text/css"/>
	<link rel="stylesheet" href="../templates/educ_env_2/css/template.css" type="text/css"/>
	<link rel="stylesheet" href="../templates/educ_env_2/css/templatepanier.css" type="text/css"/>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
	<script type="text/javascript" src="../templates/educ_env_2/js/jquery-1.11.3.min.js"></script>
</head>
<body>
<?php 
if(isset($_SESSION['modeTest'])&& $_SESSION['modeTest']) { 
	echo '<span style="color:red">Le panier est en mode Test</span>';
}
if(isset($_SESSION['isLocalMode'])&& $_SESSION['isLocalMode']) { 
	echo "<p style=\"color:red;\">mode local</p>";
}
?>
<div id="cadre_exterieur" class="cadre-panier pan-sp">
	<div id="cadre2">
	<?php include("menu-mobile.php"); ?>
