<?php

function decode($value)
{
	return str_replace("\n", "<br />", str_replace("\'", "'", utf8_decode($value)));
}

function getListePhotos()
{
	$dirname = 'photos/';
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

function setDescriptionPhoto($idPhoto, $description)
{
	$nom_fichier = '../photos/'.substr($idPhoto, 0, 3).'/'.$idPhoto.'.txt';
	
	$fichier = fopen($nom_fichier, 'w') or die("can't open file");
	fwrite($fichier, utf8_encode($description));
	fclose($fichier);
	
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


 ?>