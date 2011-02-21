<?php

//clearDir('../data');

if(!file_exists("data/site/vars.php") && !file_exists("../data/site/vars.php"))
{
	$no_params = true;
}
else
{
	$no_params = false;

	if(file_exists("../data/site/vars.php"))
		include("../data/site/vars.php");
	if(file_exists("data/site/vars.php"))
		include("data/site/vars.php");
		
}
function decode($value)
{
	return str_replace("\n", "<br />", str_replace("\'", "'", utf8_decode($value)));
}

function getLastNewPhotoId()
{
	$dirname = 'data/photos/new/';
	$dir = opendir($dirname); 

	$listePhotosAlbum = array();
	$j = 0;
	while($file = readdir($dir)) {
		if($file != '.' && $file != 'Thumbs.db' && $file != '..' && !is_dir($dirname.$file) && strpos($file, '_thumb') == false && strpos($file, '_original') == false && strpos($file, '.JPG') != false)
		{
			$listePhotosAlbum[$j] = substr($file, 4, -4);
			$j++;
		}
	}

	closedir($dir);
	rsort($listePhotosAlbum);

	return $listePhotosAlbum[0];
}

function getNewAlbumId()
{
	$dirname = '../data/photos/';
	$dir = opendir($dirname); 

	$listeAlbums = array();
	$i = 0;
	while($file = readdir($dir)) {
		if($file != '.' && $file != '..' && is_dir($dirname.$file) && is_numeric($file))
		{
			$listeAlbums[$i] = $file;
			$i++;
		}
	}

	closedir($dir);
	rsort($listeAlbums);
	
	return str_pad($listeAlbums[0] + 1, 3, "0", STR_PAD_LEFT);
}

function getListePhotos($admin)
{
	$dirname = 'data/photos/';
	$dir = opendir($dirname); 

	$listeAlbums = array();
	$i = 0;
	while($file = readdir($dir)) {
		if($file != '.' && $file != '..' && is_dir($dirname.$file) && ($admin || is_numeric($file)))
		{
			$listeAlbums[$i] = $file;
			$i++;
		}
	}

	closedir($dir);
	rsort($listeAlbums);
	
	$listePhotos = array();
	$i = 0;
	
	//$isFirstAlbum = true;
	foreach ($listeAlbums as $album)
	{
		$dirname = 'data/photos/'.$album.'/';
		$dir = opendir($dirname); 

		$listePhotosAlbum = array();
		$j = 0;
		while($file = readdir($dir)) {
			if($file != '.' && $file != 'Thumbs.db' && $file != '..' && !is_dir($dirname.$file) && strpos($file, '_thumb') == false && strpos($file, '_original') == false && strpos($file, '.JPG') != false)
			{
				$listePhotosAlbum[$j] = $album.'/'.substr($file, 0, -4);
				$j++;
			}
		}

		closedir($dir);
		
		/*
		if ($isFirstAlbum)
		{
		*/
			sort($listePhotosAlbum);
		/*	$isFirstAlbum = false;
		}
		else
		{
			rsort($listePhotosAlbum);
		}
		*/
		
		foreach ($listePhotosAlbum as $photoAlbum)
		{
			$listePhotos[$i] = $photoAlbum;
			$i++;
		}	
	}
	
	return $listePhotos;
}

function getListePhotosForZip()
{
	$dirname = '../data/photos/';
	$dir = opendir($dirname); 

	$listeAlbums = array();
	$i = 0;
	while($file = readdir($dir)) {
		if($file != '.' && $file != '..' && is_dir($dirname.$file) && is_numeric($file))
		{
			$listeAlbums[$i] = $file;
			$i++;
		}
	}

	closedir($dir);
	sort($listeAlbums);
	
	$listePhotos = array();
	$i = 0;
		
	foreach ($listeAlbums as $album)
	{
		$dirname = '../data/photos/'.$album.'/';
		$dir = opendir($dirname); 

		$listePhotosAlbum = array();
		$j = 0;
		while($file = readdir($dir)) {
			if($file != '.' && $file != 'Thumbs.db' && $file != '..' && !is_dir($dirname.$file) && strpos($file, '_thumb') == false && strpos($file, '_original') == false && strpos($file, '.JPG') != false)
			{
				$listePhotosAlbum[$j] = $album.'/'.$file;
				$j++;
			}
		}

		closedir($dir);
		
		sort($listePhotosAlbum);

		foreach ($listePhotosAlbum as $photoAlbum)
		{
			$listePhotos[$i] = $photoAlbum;
			$i++;
		}	
	}
	
	return $listePhotos;
}

function getAlbumInfos($idAlbum)
{
	if ($idAlbum != '')
	{
		$fichier = 'data/photos/'.$idAlbum.'/album.txt';
		$contenu = file_get_contents($fichier);
		$array_album = explode(';', $contenu);
		
		$result = array();
		$result['date'] = decode($array_album[0]);
		$result['titre'] = decode($array_album[1]);
		return $result;
	}
}

function getAlbumInfosFromFunctions($idAlbum)
{
	$fichier = '../data/photos/'.$idAlbum.'/album.txt';
	$contenu = file_get_contents($fichier);
	$array_album = explode(';', $contenu);
	
	$result = array();
	$result['date'] = decode($array_album[0]);
	$result['titre'] = decode($array_album[1]);
	return $result;
}

function getDescriptionPhoto($idPhoto)
{
	$fichier = 'data/photos/'.$idPhoto.'.txt';
	$contenu = @file_get_contents($fichier);
	
	return decode($contenu);
}

function getCommentsPhoto($idPhoto)
{
	$fichier = 'data/photos/'.$idPhoto.'_comments.txt';
	$contenu = @file_get_contents($fichier);
	
	$list_comments = array();

	if ($contenu != '')
	{
		$separator = "[;;;]";

		$array_comments = explode($separator, $contenu);
		
		$i_comments = 0;
		for ($i=0; $i<count($array_comments)-1; $i = $i + 3)
		{
			$comment = array();
			$comment['login'] = decode($array_comments[$i]);
			$comment['date'] = decode($array_comments[$i+1]);
			$comment['commentaire'] = decode($array_comments[$i+2]);
			
			$list_comments[$i_comments] = $comment;
			$i_comments++;
		}
	}
	return $list_comments;
}

function setTitreAlbum($idAlbum, $titre)
{
	$nom_fichier = '../data/photos/'.$idAlbum.'/album.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode(date('d/m/Y').';'.$titre));
	fclose($fichier);
	
	return $nom_fichier;
}

function setTitreAlbumFromIndex($idAlbum, $titre)
{
	$nom_fichier = 'data/photos/'.$idAlbum.'/album.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode(date('d/m/Y').';'.$titre));
	fclose($fichier);
	
	return $nom_fichier;
}

function updateDateNewAlbum()
{
	$albumInfos = getAlbumInfosFromFunctions('new');
	setTitreAlbum('new', $albumInfos['titre']);
}

function publishAlbum()
{
	//On met à jour la date de publication de l'album :
	//=================================================
	updateDateNewAlbum();

	//On crée le nouvel album :
	//=========================
	// - Récupération de l'ID du nouvel album :
	$newAlbumId = getNewAlbumId();		
	// - Renommage des fichiers, en bouchant les trous :
	renameAllAlbumPhotos('new', $newAlbumId);
	// - Renommage du dossier de l'album :
	rename("../data/photos/new", "../data/photos/".$newAlbumId);

	//On prépare le prochain nouvel album :
	//=====================================
	// - Le dossier "new" doit être présent et vide
	mkdir("../data/photos/new");
	// - On donne le titre "Nouvel album sans titre" pour le dossier "new"
	setTitreAlbum('new', 'Nouvel album sans titre');
	
	//On regénère le ZIP contenant toutes les photos :
	//================================================
	generateZip();
	
	//On envoie les mails :
	//=====================
	sendAllMailsNewAlbum($newAlbumId);
}

function generateZip()
{
	include('createzip.inc.php');
	
	// Creation du nom du fichier zip
	$nomFichierZip = '../data/photos/PhotosTimothe.zip';

	// Si le zip a déjà été généré il faut l"effacer de suite pour eviter de creer un zip 
	// contenant le zip précédent
	if(file_exists($nomFichierZip))
	 @unlink($nomFichierZip);

	// instanciation de l'objet createZip
	$timotheZip = new createZip;  

	$listePhotos = getListePhotosForZip();
	
	foreach ($listePhotos as $photo)
	{
		$fileContents = file_get_contents('../data/photos/'.str_replace('.JPG', '_original.JPG', $photo));
		$fileName = substr($photo, 4);
		$timotheZip -> addFile($fileContents, $fileName); 
	}	
	
	$fd = fopen ($nomFichierZip, "wb");
	$out = fwrite ($fd, $timotheZip -> getZippedfile());
	fclose ($fd);
}

function sendAllMailsNewAlbum($idAlbum)
{
	$fichier = '../data/mails/mailing_list.txt';
	$contenu = file_get_contents($fichier);
	$array_mails = explode(';', $contenu);
	
	foreach ($array_mails as $mail)
	{
		if ($mail != '')
			sendEmailFile(sendMailNewAlbum($idAlbum, $mail));
	}
}

function sendMailAdmin($mail, $pass)
{
	sendEmailFile(sendMailPassAdmin($mail, $pass));
}

function clearDir($dossier) 
{
	$ouverture=@opendir($dossier);
	if (!$ouverture) return;
	while($fichier=readdir($ouverture)) {
		if ($fichier == '.' || $fichier == '..') continue;
			if (is_dir($dossier."/".$fichier)) {
				$r=clearDir($dossier."/".$fichier);
				if (!$r) return false;
			}
			else {
				$r=@unlink($dossier."/".$fichier);
				if (!$r) return false;
			}
	}
	closedir($ouverture);
	$r=@rmdir($dossier);
	@rename($dossier,"trash");
	return true;
}

function renameAllAlbumPhotos($oldId, $newId)
{
	$dirname = '../data/photos/'.$oldId.'/';
	$dir = opendir($dirname); 

	$listePhotosAlbum = array();
	$j = 0;
	while($file = readdir($dir)) {
		if($file != '.' && $file != 'Thumbs.db' && $file != 'album.txt' && $file != '..' && !is_dir($dirname.$file))
		{
			$listePhotosAlbum[$j] = $file;
			$j++;
		}
	}

	closedir($dir);	
	sort($listePhotosAlbum);

	$i = 0;
	$lastId = 0;
	foreach ($listePhotosAlbum as $photo)
	{
		if (intval(substr($photo, 4, 3)) != $lastId)
		{
			$i++;
			$lastId = intval(substr($photo, 4, 3));
		}
	
		$oldName = '../data/photos/'.$oldId.'/'.$photo;
		$newName = '../data/photos/'.$oldId.'/'.$newId.'_'.str_pad($i, 3, "0", STR_PAD_LEFT).substr($photo, 7);
		rename($oldName, $newName);
	}	
}

function setDescriptionPhoto($idPhoto, $description)
{
	$nom_fichier = '../data/photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode($description));
	fclose($fichier);
	
	return $nom_fichier;
}

function setTitreSite($titre)
{
	$nom_fichier = '../data/site/titre.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode($titre));
	fclose($fichier);
	
	return $nom_fichier;
}

function getTitreSite()
{
	$nom_fichier = 'data/site/titre.txt';
	
	if (file_exists($nom_fichier))
		$contenu = @file_get_contents($nom_fichier);
	
	return decode($contenu);
}

function setFooterSite($footer)
{
	$nom_fichier = '../data/site/footer.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode($footer));
	fclose($fichier);
	
	return $nom_fichier;
}

function getFooterSite()
{
	$nom_fichier = 'data/site/footer.txt';
	
	if (file_exists($nom_fichier))
		$contenu = @file_get_contents($nom_fichier);
	
	return decode($contenu);
}

function setParametresSite($mailAdmin, $passAdmin, $urlSite)
{
	$nom_fichier = '../data/site/vars.php';
	
	$contenu  = "<?php\n";
	$contenu .= "//Administrateur :\n";
	$contenu .= "\$mail_admin = \"".$mailAdmin."\";\n";
	$contenu .= "\$pass_admin = \"".$passAdmin."\";\n";
	$contenu .= "//Url du site :\n";
	$contenu .= "define('URL_SITE',\"".$urlSite."\");\n";
	$contenu .= "//Dossier des mails :\n";
	$contenu .= "define('PATH_MAIL',\"data/mails/\");\n";
	$contenu .= "//Envoi de mails :\n";
	$contenu .= "\$email_from = \"".$mailAdmin."\";\n";
	$contenu .= "define('MAIL_ADMIN',\$email_from);\n";
	$contenu .= "\$urlSendMail = 'http://int-musicdestock.fr/radioclashMailing/sendMail.php';\n";
	//$contenu .= "\$urlSendMail = URL_SITE.'functions/sendMail.php';\n";
	$contenu .= "define('URL_SEND_MAIL',\$urlSendMail);\n";
	$contenu .= "?>\n";	
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode($contenu));
	fclose($fichier);
	
	$crypted = base64_encode(crypt($mailAdmin, $passAdmin));
	
	return $crypted;
}

function deletePhoto($idPhoto)
{
	$nom_fichier 			 = '../data/photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.JPG';
	$nom_fichier_original 	 = '../data/photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'_original.JPG';
	$nom_fichier_thumb 		 = '../data/photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'_thumb.JPG';
	$nom_fichier_description = '../data/photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.txt';
	
	if (file_exists($nom_fichier))
		unlink($nom_fichier);
	if (file_exists($nom_fichier_original))
		unlink($nom_fichier_original);
	if (file_exists($nom_fichier_thumb))
		unlink($nom_fichier_thumb);
	if (file_exists($nom_fichier_description))
		unlink($nom_fichier_description);
	
	//S'il s'agit d'une photo publiée, on regénère le ZIP :
	if (substr($idPhoto, 0, 3) != 'new')
		generateZip();
			
	return $nom_fichier;
}

function addComment($idPhoto, $login, $commentaire)
{
	$separator = "[;;;]";

	$nom_fichier = '../data/photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'_comments.txt';
	
	if (file_exists($nom_fichier))
		$contenu = @file_get_contents($nom_fichier);

	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, $contenu.utf8_encode($login.$separator.date("d/m/Y").$separator.$commentaire.$separator));
	fclose($fichier);
	
	return $nom_fichier;
}

function addMail($mail)
{
	$separator = utf8_encode(";");

	$nom_fichier = '../data/mails/mailing_list.txt';
	
	if (file_exists($nom_fichier))
		$contenu = @file_get_contents($nom_fichier);
	else
		$contenu = $separator;
	
	$mail = strtolower($mail);
	
	if (strpos($contenu, $separator.$mail.$separator) === FALSE)
	{
		$fichier = fopen($nom_fichier, 'w') or die("can't open file");
		fwrite($fichier, $contenu.$mail.$separator);
		fclose($fichier);
	}

	chmod($nom_fichier, 0775);
	
	return $nom_fichier;
}

function deleteMail($mail)
{
	$separator = utf8_encode(";");

	$nom_fichier = 'data/mails/mailing_list.txt';
	
	if (file_exists($nom_fichier))
	{
		$contenu = @file_get_contents($nom_fichier);
	
		$mail = strtolower($mail);
		
		$fichier = fopen($nom_fichier, 'w') or die("can't open file");
		fwrite($fichier, str_replace($separator.$mail.$separator, $separator, $contenu));
		fclose($fichier);
	}
	
	return $nom_fichier;
}

/**************************************/
/**   E N V O I   D E   M A I L S    **/
/**************************************/
/*
 * Function generateEmailFile : permet de créer un fichier csv utilisé pour l'envoi de mail
 * Format fichier csv : FROM|||TO|||TITLE|||MESSAGE TEXT|||MESSAGE HTML
 * @params $id : identifiant de l'utilisateur concerné (son mot de passe ou les mails qu'il envoie)
 * @params $from : expediteur
 * @params $to : destinataire
 * @params $title : titre du message à envoyer
 * @params $msg : message à envoyer
 * @returns : path relatif du fichier ou null 
 */
 function generateEmailFile($to,$title,$msgTxt,$msgHtml)
 {
	$from = MAIL_ADMIN;
 	// les séparateur sont "|||" mais on check tout de même dans les chaines
 	while(strpos($msgTxt,"|||"))
 		str_replace("|||","|",$msgTxt);
 	while(strpos($msgHtml,"|||"))
 		str_replace("|||","|",$msgTxt);
 	
 	// on vérifie que le dossier de l'utilisateur a été créé
 	if(!file_exists("../".PATH_MAIL))
 	{
 		if(!mkdir("../".PATH_MAIL))
 		{
 			echo "ERREUR LORS DE LA CREATION DU DOSSIER";
 			return false;
 		}
 	}
 	$file=  "../".PATH_MAIL."/".time()."_".$to.".csv";
 	$fp=fopen($file,"w+");
 	$str=$from."|||".$to."|||".$title."|||".$msgTxt."|||".$msgHtml;
 	fwrite($fp,$str);
 	fclose($fp);
 	return $file;
 }

 /*
  * Function sendEmailFile permet d'envoyer par mail un fichier "email" généré au préalable 
  * @param : $fileEmail : path relatif vers le fichier contenant les infos pour le mail
  */
 function sendEmailFile($file_email)
 {
 	$url_file=str_replace("../",URL_SITE,$file_email);
 	return file_get_contents(URL_SEND_MAIL.'?fileurl='.$url_file);
 }
  
 /*
  * Function sendMailNewAlbum permet d'envoyer le mail d'une newsletter à un utilisateur
  * @params : $idAlbum = ID de l'album à annoncer dans la newsletter
  * @params : $mail = email de l'utilisateur
  */
 function sendMailNewAlbum($idAlbum, $mail)
 {
 	$infosAlbum = getAlbumInfosFromFunctions($idAlbum);
	
	$mail_text  = "Coucou !\r\n\r\nLa série de photos suivante à été ajoutée :\r\n\r\n";
	$mail_text .= " - ".$infosAlbum['titre']."\r\n\r\n";
	$mail_text .= "Rendez-vous ici :\r\n";
	$mail_text .= " - ".URL_SITE." (copier-coller le lien dans votre navigateur)\r\n\r\n";
	$mail_text .= "Bonne visite !\r\n\r\n";
	$mail_text .= "Bisous bisous,\r\n";
	$mail_text .= "Gaëtan, Julie et Timothé.\r\n";
	$mail_text .= "\r\n";
	$mail_text .= "\r\n";
	$mail_text .= "\r\nPour vous désabonnez, ouvrez la page suivante : ".URL_SITE."unregister.php?id=".base64_encode($mail);
	
	$mail_html="Coucou !<br /><br />
	La s&eacute;rie de photos suivante &agrave; &eacute;t&eacute; ajout&eacute;e :
	 <ul>
			<li>".$infosAlbum['titre']."</li>
	</ul>
	<p><u>Rendez-vous ici :</u></p>
	<ul>
		<li><a href=\"".URL_SITE."\" target=\"blank\"><b>".URL_SITE."</b></a></li>
	</ul>
	<p>
		Bonne visite !<br /><br />
		Bisous bisous,<br />
		Gaetan, Julie, et Timoth&eacute;.
	</p>
	<p style=\"color:gray\">
		Pour se d&eacute;sabonner, <a href=\"".URL_SITE."unregister.php?id=".base64_encode($mail)."\" target=\"blank\">cliquez ici</a>.
	</p>";
	
	return generateEmailFile($mail,"De nouvelles photos de Timothé sont en ligne !",utf8_decode($mail_text),utf8_decode($mail_html));
 }
 
 /*
  * Function sendMailNewAlbum permet d'envoyer le mail d'une newsletter à un utilisateur
  * @params : $idAlbum = ID de l'album à annoncer dans la newsletter
  * @params : $mail = email de l'utilisateur
  */
 function sendMailPassAdmin($mail, $pass)
 {
 	$mail_text  = "Voici vos informations de connexion :\r\n\r\n";
	$mail_text .= " - Site         : ".URL_SITE."admin/\r\n";
	$mail_text .= " - Mail         : ".$mail."\r\n";
	$mail_text .= " - Mot de passe : ".$pass."\r\n";
	$mail_text .= "\r\n";
	$mail_text .= "Bonne utilisation !\r\n";
	
	$mail_html="Voici vos informations de connexion :
	 <ul>
			<li>Site : ".URL_SITE."admin/</li>
			<li>Mail : ".$mail."</li>
			<li>Mot de passe : ".$pass."</li>
	</ul>
	<p>
		Bonne utilisation !
	</p>";
	
	return generateEmailFile($mail,"Votre gallerie : Informations de connexion",utf8_decode($mail_text),utf8_decode($mail_html));
 }
 
/*
 * Function isMail permet de checker que l'adresse email est pseudo valide
 * @params: email, la chaine à tester
 * @return: true / false
 */
 function isMail($email)
 {
 	return preg_match('/^[a-z0-9]+[._a-z0-9-]*@[a-z0-9]+[._a-z0-9-]*\.[a-z0-9]+$/ui', $email);
 } 
 
 ?>