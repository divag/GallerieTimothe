<?php
$file = $_FILES['file'];
//description
echo '{"name":"'.$file['name'].'","type":"'.$file['type'].'","size":"'.$file['size'].'"}';

//$nom_fichier = 'upload-'.$file['name'].'.txt';
$nom_fichier = 'upload-'.$_POST['description'].'.txt';

$fichier = fopen($nom_fichier, 'w') or die("can't open file");
fwrite($fichier, utf8_encode($description));
fclose($fichier);



?>