<?phpinclude('functions.php');if (isset($_POST) && isset($_POST['mail'])){	$fichier = addMail($_POST["mail"]);	//echo $_POST["variable"]." = '".$fichier.$_POST["mail"]."';";}?>