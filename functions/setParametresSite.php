<?phpinclude('functions.php');if (isset($_POST) && isset($_POST['mail']) && isset($_POST['pass']) && isset($_POST['url'])){	$fichier = setParametresSite($_POST["mail"], $_POST["pass"], $_POST["url"]);	//echo $_POST["variable"]." = '".$fichier.$_POST["mail"].$_POST["pass"]."';";}?>