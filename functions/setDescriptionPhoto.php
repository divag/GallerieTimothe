<?phpinclude('functions.php');if (isset($_POST) && isset($_POST['photo']) && isset($_POST['description'])){	$fichier = setDescriptionPhoto($_POST["photo"], $_POST["description"]);	//echo $_POST["variable"]." = '".$fichier.$_POST["photo"].$_POST["login"].$_POST["commentaire"]."';";}?>