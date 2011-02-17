<?php

function decode($value)
{
	return str_replace("\n", "<br />", str_replace("\'", "'", utf8_decode($value)));
}

function getLastNewPhotoId()
{
	$dirname = '../photos/new/';
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
	$dirname = '../photos/';
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
	$dirname = 'photos/';
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
		
	foreach ($listeAlbums as $album)
	{
		$dirname = 'photos/'.$album.'/';
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
	$fichier = 'photos/'.$idAlbum.'/album.txt';
	$contenu = file_get_contents($fichier);
	$array_album = explode(';', $contenu);
	
	$result = array();
	$result['date'] = decode($array_album[0]);
	$result['titre'] = decode($array_album[1]);
	return $result;
}

function getAlbumInfosFromFunctions($idAlbum)
{
	$fichier = '../photos/'.$idAlbum.'/album.txt';
	$contenu = file_get_contents($fichier);
	$array_album = explode(';', $contenu);
	
	$result = array();
	$result['date'] = decode($array_album[0]);
	$result['titre'] = decode($array_album[1]);
	return $result;
}

function getDescriptionPhoto($idPhoto)
{
	$fichier = 'photos/'.$idPhoto.'.txt';
	$contenu = @file_get_contents($fichier);
	
	return decode($contenu);
}

function getCommentsPhoto($idPhoto)
{
	$fichier = 'photos/'.$idPhoto.'_comments.txt';
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
	$nom_fichier = '../photos/'.$idAlbum.'/album.txt';
	
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
	rename("../photos/new", "../photos/".$newAlbumId);

	//On prépare le prochain nouvel album :
	//=====================================
	// - Le dossier "new" doit être présent et vide
	mkdir("../photos/new", 0777);
	// - On donne le titre "Nouvel album sans titre" pour le dossier "new"
	setTitreAlbum('new', 'Nouvel album sans titre');
	
	//On regénère le ZIP contenant toutes les photos :
	//================================================
	generateZip();
	
	//On envoie les mails :
	//=====================
	sendMailNewAlbum($newAlbumId);
}

function generateZip()
{

}

function sendMailNewAlbum($IdAlbum)
{

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
	$dirname = '../photos/'.$oldId.'/';
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
	
		$oldName = '../photos/'.$oldId.'/'.$photo;
		$newName = '../photos/'.$oldId.'/'.$newId.'_'.str_pad($i, 3, "0", STR_PAD_LEFT).substr($photo, 7);
		rename($oldName, $newName);
	}	
}

function setDescriptionPhoto($idPhoto, $description)
{
	$nom_fichier = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode($description));
	fclose($fichier);
	
	return $nom_fichier;
}

function deletePhoto($idPhoto)
{
	$nom_fichier 			 = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.JPG';
	$nom_fichier_original 	 = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'_original.JPG';
	$nom_fichier_thumb 		 = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'_thumb.JPG';
	$nom_fichier_description = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.txt';
	
	if (file_exists($nom_fichier))
		unlink($nom_fichier);
	if (file_exists($nom_fichier_original))
		unlink($nom_fichier_original);
	if (file_exists($nom_fichier_thumb))
		unlink($nom_fichier_thumb);
	if (file_exists($nom_fichier_description))
		unlink($nom_fichier_description);
	
	return $nom_fichier;
}

function addComment($idPhoto, $login, $commentaire)
{
	$separator = "[;;;]";

	$nom_fichier = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'_comments.txt';
	
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

	$nom_fichier = '../mails/mailing_list.txt';
	
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

	return $nom_fichier;
}

 ?>