<?php
$file = $_FILES['file'];

include('functions.php');

$filename = strtoupper($file['name']);

if (strpos($filename, '.JPG') === FALSE && strpos($filename, '.JPEG') === FALSE)
{
	echo "error";
}
else
{
	$idPhoto = str_pad($_POST['ordre'], 3, "0", STR_PAD_LEFT);

	$nom_fichier = '../photos/new/new_'.$idPhoto.'.JPG';
	$nom_fichier_original = '../photos/new/new_'.$idPhoto.'_original.JPG';
	$nom_fichier_thumb = '../photos/new/new_'.$idPhoto.'_thumb.JPG';

	//===============================
	// ENREGISTREMENT  DU  FICHIER //
	//===============================

	if (move_uploaded_file($_FILES['file']['tmp_name'], $nom_fichier_original)) 
	{	
		echo "success";
		chmod($nom_fichier_original, 0777);
		
		//REDIMENSIONNEMENT DE L'IMAGE :
		//==============================
		
		// Set a maximum height and width
		$width = 500;
		$height = 500;

		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($nom_fichier_original);

		$ratio_orig = $width_orig/$height_orig;

		if ($width/$height > $ratio_orig) {
		   $width = $height*$ratio_orig;
		} else {
		   $height = $width/$ratio_orig;
		}

		// Redimensionnement
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($nom_fichier_original);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

		// Output
		imagejpeg($image_p, $nom_fichier);
		chmod($nom_fichier, 0777);

		//CREATION DU THUMBNAIL :
		//=======================
		
		// Set a maximum height and width
		$thumbsize = 75;

		// Redimensionnement
		$image_p = imagecreatetruecolor($thumbsize, $thumbsize);
		$image = imagecreatefromjpeg($nom_fichier_original);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $thumbsize, $thumbsize, $width_orig, $height_orig);

		// Output
		imagejpeg($image_p, $nom_fichier_thumb);
		chmod($nom_fichier_thumb, 0777);

	} else {
	  // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
	  // Otherwise onSubmit event will not be fired
	  echo "error";
	}
}
?>