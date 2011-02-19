<?php  

// nom du fichier contenant les informations
$file=$_GET['fileurl'];
if(empty($file))
	echo "ERREUR";
else
{
	// on récupère le contenu du fichier 
	$filecontent=file_get_contents($file);
	if (empty($filecontent))
		echo "ERREUR";
	else
	{
		// on parse le fichier
		$params=explode("|||",$filecontent);

		$mailAdmin = $params[0];
		$mailDest=$params[1];
		$mailTitle=$params[2];
		$mailText=$params[3];
		$mailHtml=$params[4];

		 if (mail($mailDest, $mailTitle, $mailText)) 
			echo"<p>Message successfully sent!</p>";
		 else 
			echo "<p>Message delivery failed...</p>";
	}
}

?>