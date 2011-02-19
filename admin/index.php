<?php

if (!file_exists("../data/site/vars.php"))
{
	echo "<script>";
	echo "window.location.href = '../?admin';";
	echo "</script>";
	exit;
}

include("../functions/functions.php");
$titreSite = getTitreSite();

if (isset($_GET["send_mail"]) || (isset($_POST["email"]) && isset($_POST["password"])))
{
	include("../data/site/vars.php");
	
	if (isset($_GET["send_mail"]))
		sendMailAdmin($mail_admin, $pass_admin);
	else
	{
		$crypted = crypt($mail_admin, $pass_admin);
		
		if ($_POST["email"] == $mail_admin && $_POST["password"] == $pass_admin)
		{
			echo "<script>";
			echo "window.location.href = '../?admin=".base64_encode($crypted)."';";
			echo "</script>";
		}
		else
			echo "<script>alert('Les informations de connexion saisies sont incorrectes !\\n\\nVeuillez retenter votre chance.');</script>";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $titreSite ?> : Administration</title>
	<style>
	body {
		background-color:#FFF;
	}
	#divWait {
		text-align: center;
	}

	#divWait div form {
		text-align: center;
	}

	#divWait div form table {
		text-align: left;
		margin-left: auto;
		margin-right: auto;
		border: 2px double rgb(199, 199, 199);
		padding: 4px;
		background-color: rgb(242, 242, 242);
	}

	#divWait div form table input {
		width: 170px;
	}

	.libelle {
		text-align: right;
	}

	#body {
		font-family: sans-serif;
		font-size: 9pt;
	}

	#divWait div form table tbody tr td.libelle {
		font-size: 9pt;
		color: rgb(94, 94, 94);
	}

	</style>
</head>
<body>
<div id="divWait" class="divWait">
	<div>
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br />
	<br />
		<form id="identification" method="post" action="index.php">
			<table>
				<tr><td class="libelle">Nom : </td><td><input type="text" name="email" /></td></tr>
				<tr><td class="libelle">Mot de passe : </td><td><input type="password" name="password" /></td></tr>
				<tr><td class="libelle"></td><td><input type="submit" value=" >>>>>  G O  >>>>>" /></td></tr>
				<tr><td></td><td><a href="index.php?send_mail" title="Envoyer le mot de passe à l'administrateur" >Recevoir mon mot de passe</a></td></tr>
			</table>			
		</form>
		<!--
		<br />
		<form id="sendMotDePasse" method="post" action="index.php">
			<span id="boutonEnvoiMotDePasse">>> <a href="#" onclick="document.getElementById('boutonEnvoiMotDePasse').style.display = 'none'; document.getElementById('divEnvoiMotDePasse').style.display = 'block';">Mot de passe oublié ?</a> <<</span>
			<br />
			<div style="display:none;" id="divEnvoiMotDePasse">
				<table>
					<tr><td class="libelle">Adresse mail : </td><td><input type="text" id="nomCompte" name="nomCompte" /></td></tr>
					<tr><td class="libelle"></td><td><input type="submit" value="Envoyer le mot de passe" /></td></tr>
				</table>
			</div>
		</form>
		-->
	</div>
</div>
</body>
</html>
