<?php

include('functions/functions.php');
$admin = (isset($_GET['admin']));
if ($admin)
	$admin_code = $_GET['admin'];

function getFolderErrors($folderName, $rights)
{
	$erreur_message = '';
	if(!file_exists($folderName))
 		$erreur_message .= "<span style=\"color:red;\">Erreur : Veuillez créer le dossier \"".$folderName."\" à la racine de votre site.</span><br />";
	else
	{
		@mkdir($folderName."/test");
		if (!file_exists($folderName."/test"))
			$erreur_message .= "<span style=\"color:red;\">Erreur : Le dossier \"".$folderName."\" n'a pas les droits nécessaires au bon fonctionnement du site. Il lui faut les droits \"".$rights."\".</span><br />";	
		else
			@rmdir($folderName."/test");
	}
	return $erreur_message;
}

if ($admin && $no_params)
{
	if(!file_exists("data"))
 		@mkdir("data", 0777);
	
	$erreur_initialisation = '';
	$erreur_initialisation .= getFolderErrors('data', '777');
}

if (!$no_params)
{
	$titreSite = getTitreSite();
	
	if ($admin)
	{
		include("data/site/vars.php");
		$crypted = base64_encode(crypt($mail_admin, $pass_admin));
		 if ($admin_code != $crypted)
		{
			echo "<script>";
			echo "window.location.href = '".URL_SITE."admin/';";
			echo "</script>";
			exit;
		}
	}
}

if ($erreur_initialisation != '')
{
	echo "<h3>".$erreur_initialisation."</h3>";
	exit;
}
else
{
	if(!file_exists("data/site"))
 		@mkdir("data/site", 0775);
	if(!file_exists("data/mails"))
 		@mkdir("data/mails", 0775);
	if(!file_exists("data/photos"))
 		@mkdir("data/photos", 0775);
	if(!file_exists("data/photos/new"))
 		@mkdir("data/photos/new", 0775);
	if(!file_exists("data/photos/new/album.txt"))
 		setTitreAlbumFromIndex('new', 'Nouvel album sans titre');
}

?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title><?php echo $titreSite ?></title>
		<link rel="stylesheet" href="css/basic.css" type="text/css" />
		<link rel="stylesheet" href="css/galleriffic-5.css" type="text/css" />
		
		<!-- <link rel="stylesheet" href="css/white.css" type="text/css" /> -->
		<link rel="stylesheet" href="css/black.css" type="text/css" />
		<link rel="stylesheet" href="css/jquery-ui.css" id="theme">
		<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
		<link rel="stylesheet" href="css/my.css" type="text/css" />
		
		<script type="text/javascript" src="js/jquery-1.5.min.js"></script>
		<script type="text/javascript" src="js/jquery.history.js"></script>
		<script type="text/javascript" src="js/jquery.galleriffic.js"></script>
		<script type="text/javascript" src="js/jquery.opacityrollover.js"></script>
		<script type="text/javascript" src="js/my.js"></script>
		<!-- We only want the thunbnails to display when javascript is disabled -->
		<script type="text/javascript">
			document.write('<style>.noscript { display: none; }</style>');
			var urlBase = '<?php if (!$no_params) echo URL_SITE ?>';
			if (urlBase == '')
			{
				var url = window.location.href;
				urlBase = url.substring(0, url.lastIndexOf('/') + 1);
			}
			var urlBaseFunction = urlBase + 'functions/';
			var debug = <?php echo (isset($_GET['debug']) ? 'true' : 'false') ?>;
			var admin = <?php echo ($admin ? 'true' : 'false') ?>;
			var noParams = <?php echo ($no_params ? 'true' : 'false') ?>;
			<?php
				if ($admin)
					echo "var admin_code = '".$admin_code."';";
			?>
		</script>
	</head>
	<body>
		<div id="page">
			<?php
			
				if (!$no_params)
				{
					$listePhotos = getListePhotos($admin);
					$idLastAlbum = substr($listePhotos[0], 0, 3);	
					$lastAlbum = getAlbumInfos($idLastAlbum);
					$dateLastAlbum = $lastAlbum['date'];
					$titreLastAlbum = $lastAlbum['titre'];
					
					if (count($listePhotos) == 0)
					{
						$havePhotos = false;
					}
					else
					{
						$havePhotos = true;
					}
				}
			?>
			<div id="container">
				<div id="create-site" style="display:none;text-align:center;">
					<h1><a href="#">Paramétrage de votre site</a></h1>
					<p>
					Bienvenue sur votre nouvelle gallerie !<br />
					Ce site vous permettra de publier très simplement vos photos sous la forme d'une jolie gallerie.<br /><br />
					<span class="red"><u>Pour mettre en route votre site, il vous faudra remplir les informations suivantes :</u></span><br />					
					</p>
					<div style="width:400px;margin-left:auto;margin-right:auto;">
						Adresse mail de l'administrateur :
						<input id="mail-admin-text" type="text" class="comment-text" />
						Mot de passe de l'administrateur :
						<input id="pass-admin-text" type="password" class="comment-text" />
						Saisissez à nouveau le mot de passe :
						<input id="pass-admin-text-2" type="password" class="comment-text" />
						<br />
						<br />
						<input type="button" id="start_site" class="button" value="OK" onclick="setParametresSite();" />
					</div>
				</div>
				<div id="no-album" style="display:none;text-align:center;">
					<h1 class="red">Site en construction</h1>
					Ce site est actuellement en construction.<br />
					Veuillez repasser ultérieurement.
				</div>
				<div id="add-album" style="display:none;text-align:center;">
					<h1><a href="#">Ajout de photos</a></h1>
					<p>
					Pour ajouter des photos sur votre gallerie, cliquez sur le bouton ci-dessous, ou glissez directement vos fichiers sur ce bouton.<br />
					<span class="red">Uniquement les fichiers d'extension "<b>.jpg</b>" seront pris en compte.</span>
					</p>
					<form id="file_upload" action="functions/upload.php" method="POST" enctype="multipart/form-data">
						<input type="file" name="file" multiple>
						<button>Uploader des photos</button>
						<div>Uploader des photos</div>
					</form>

					<table id="files"></table>
					
					<br />
					<br />
					<input type="button" id="start_uploads" class="button" value="OK" />
					<div id="while_uploads" class="wait">
					Traitement en cours, veuillez patienter...
					</div>
					<!--
					<script src="js/jquery-1.5.min.js"></script>
					-->
					<script src="js/jquery-ui.min.js"></script>
					<script src="js/jquery.fileupload.js"></script>
					<script src="js/jquery.fileupload-ui.js"></script>
					<script src="js/jquery.tablednd_0_5.js"></script>
					<script src="js/application.js"></script>
					<script>
					$('#file_upload').fileUploadUI({
						uploadTable: $('#files'),
						downloadTable: $('#files'),
						buildUploadRow: function (files, index) {
							return $('<tr id="' + index + '"><td class="file_upload_preview"><\/td>' +
									'<td class="dragHandle">' + files[index].name + '<\/td>' +
									'<td class="file_upload_order"><input type="text" id="orderItem' + index + '" title="File order" value="1"><\/td>' +
									'<td class="file_upload_progress"><div><\/div><\/td>' +
									'<td class="file_upload_start" style="display:none;">' +
									'<button class="ui-state-default ui-corner-all" title="Start Upload">' +
									'<span class="ui-icon ui-icon-circle-arrow-e">Start Upload<\/span>' +
									'<\/button><\/td>' +
									'<td class="file_upload_cancel">' +
									'<button class="ui-state-default ui-corner-all" title="Cancel">' +
									'<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
									'<\/button><\/td><\/tr>');
						},
						buildDownloadRow: function (file) {
							return $('<tr><td>' + file.name + '<\/td><\/tr>');
						},
						beforeSend: function (event, files, index, xhr, handler, callBack) {
							handler.uploadRow.find('.file_upload_start button').click(function () {
								handler.formData = {
									ordre: handler.uploadRow.find('.file_upload_order input').val()
								};
								callBack();
							});
							
							$("#files").tableDnD({
								onDrop: function(table, row) {
									updateOrdrePhotos();
								},
								dragHandle: "dragHandle"
							});
							
							updateOrdrePhotos();
						},
						onComplete: function (event, files, index, xhr, handler) {
							handler.onCompleteAll(files);
							//$('#result').html($('#result').html() + '<br />' + files[index].name);
						},
						onAbort: function (event, files, index, xhr, handler) {
							handler.removeNode(handler.uploadRow);
							handler.onCompleteAll(files);
						},
						onCompleteAll: function (files) {
							// The files array is a shared object between the instances of an upload selection.
							// We extend it with a uploadCounter to calculate when all uploads have completed:
							if (!files.uploadCounter) {
								files.uploadCounter = 1;  
							} else {
								files.uploadCounter = files.uploadCounter + 1;
							}
							if (files.uploadCounter === files.length) {
								// your code after all uplaods have completed
								//$('#result').html($('#result').html() + '<br />Upload terminé');
							}
						}//,
						//imageTypes: /^image\/(gif|jpeg|png)$/
						//imageTypes: /^image\/(jpeg)$/
						//,
						//dropZone: $('#file_upload_container')
					});

					function updateOrdrePhotos() {
						var ordrePhoto = <?php echo getLastNewPhotoId() + 1; ?>;
						
						$('#files .file_upload_order input').each(function()
						{
							$(this).val(ordrePhoto);
							ordrePhoto++;
						});
					}

					$('#start_uploads').click(function () {
						if ($('#files tr').length != 0)
						{
							$('#start_uploads').hide();
							$('#while_uploads').show();
							updateOrdrePhotos();	
							$('.file_upload_start button').click();

							setInterval(function() {
								if ($('#files tr').length == 0)
								{
									window.location.href = urlBase + '?admin=' + admin_code;
								}
							}, 500);
						}
						else
						{
							displaySite();
						}
					});
					</script> 
				</div>
				<div id="site">
					<?php
						if (!$no_params)
						{
							if ($admin)
							{
								echo "<div style=\"padding-bottom:15px;\">";
								echo "<input type=\"button\" class=\"button\" value=\"Ajouter de nouvelles photos\" onclick=\"displayAddAlbum();\" />";
								if ($idLastAlbum == 'new')
								{
									echo "<br />";
									echo "<input id=\"start_publish\" type=\"button\" class=\"button\" value=\"Publier l'album en cours de création\" onclick=\"publishAlbum();\" />";
									echo "<div id=\"while_publish\" class=\"wait\">";
									echo "Traitement en cours, veuillez patienter...";
									echo "</div>";

								}
								echo "</div>";
							}
						}
					?>
					<?php
						if ($admin)
						{
							echo "<br /><textarea id=\"site-titre-text\" class=\"comment-text\" rows=\"1\">".str_replace("<br />", "\n", $titreSite)."</textarea><br />";
							echo "<input type=\"button\" class=\"button\" value=\"Modifier le titre du site\" onclick=\"setTitreSite();\" />";
							echo "<br /><br />";
						}
					?>
					<div class="header">
						<h1><a href=""><?php echo $titreSite ?></a></h1>
						<div id="lastUpdate" <?php if ($idLastAlbum == 'new') echo "style=\"display:none;\"" ?>>
							<span>Dernier album ajouté le <?php echo $dateLastAlbum ?> >> <b><?php echo $titreLastAlbum ?></b> <<</span>
						</div>
						<br />
					</div>
					<div class="links" <?php if ($admin) echo "style=\"display:none;\"" ?>>
						<a id="linkMail" class="mailLink" onclick="initialiseEmail();"><img src="css/mail.gif" alt="Recevoir un mail quand il y a du nouveau" /><span>Recevoir un mail quand il y a du nouveau</span></a>
						<span><form id="spanMail" style="display:none;" onsubmit="if (validateEmail()) saveEmail(); return false;"><input id="mail-text" type="text" class="comment-txt-login" name="email" style="width:79%;" onfocus="if (this.value == 'Saisissez votre email') this.value = '';" onblur="validateEmail()" value="Saisissez votre email" /><input type="button" value="OK" class="buttonMail" style="width:19%;margin-left:1px;" onclick="if (validateEmail()) saveEmail();" /></form></span>
						<span id="spanMailOk" style="display:none;">
							Adresse enregistrée : <span id="spanMailAdresse"></span><br />
							<a class="mailLink" onclick="initialiseEmail();"><img src="css/mail.gif" alt="Enregistrer une autre adresse email" /><span>Enregistrer une autre adresse email</span></a>
						</span>
						<br />
						<a href="data/photos/PhotosTimothe.zip" class="zipLink"><img src="css/zip.gif" alt="T&eacute;l&eacute;charger toutes les photos" /><span>T&eacute;l&eacute;charger toutes les photos</span></a>
					</div>
					<br class="clear" />
					<!-- <h2>Nom de la gallerie...</h2> -->
					<!-- Start Advanced Gallery Html Containers -->				
					<div class="navigation-container">
						<div id="thumbs" class="navigation">
							<a class="pageLink prev" style="visibility: hidden;" href="#" title="Page précédente"></a>
						
								<ul class="thumbs noscript">
								<?php
									if (!$no_params)
									{
										$idAlbum = $idLastAlbum;
										$album = $lastAlbum;
										$dateAlbum = $lastAlbum['date'];
										$titreAlbum = $lastAlbum['titre'];
										
										$idTempAlbum = $idLastAlbum;
										$i = 0;
										$newAlbumHaveAllDescriptions = true;
										
										$scriptAlbumShortcuts = "var albumShortcuts = new Array();\n";

										$scriptAlbumShortcuts .= "albumShortcuts['".$idAlbum."'] = new Array();\n";
										$idPrevAlbumPhoto = substr($listePhotos[0], 4);
										$idLastAlbum = $idAlbum;
										$idLastAlbumPhoto = $idPrevAlbumPhoto;

										foreach ($listePhotos as $photo)
										{
											$idAlbum = substr($photo, 0, 3);
											$idPhoto = substr($photo, 4);
											if ($idAlbum != $idTempAlbum)
											{
												$album = getAlbumInfos($idAlbum);
												$dateAlbum = $album['date'];
												$titreAlbum = $album['titre'];
																								
												$scriptAlbumShortcuts .= "albumShortcuts['".$idAlbum."'] = new Array();\n";
												$scriptAlbumShortcuts .= "albumShortcuts['".$idAlbum."']['prev'] = '".$idPrevAlbumPhoto."';\n";
												$scriptAlbumShortcuts .= "albumShortcuts['".$idTempAlbum."']['next'] = '".$idPhoto."';\n";
												$idPrevAlbumPhoto = $idPhoto;
												$idFirstAlbum = $idAlbum;
												$idFirstAlbumPhoto = $idPhoto;
												
												$idTempAlbum = $idAlbum;
											}
										
											$descriptionPhoto = getDescriptionPhoto($photo);
										
											echo "<li>";
											echo "<a class=\"thumb".($admin && trim($descriptionPhoto) == "" ? " no-description" : "").($admin && trim($descriptionPhoto) != "" ? " have-description" : "").($idAlbum == "new" ? " new" : "")."\" href=\"data/photos/".$photo.".JPG\" name=\"".$idPhoto."\" title=\"".$titreAlbum."\">";
											echo "	<img src=\"data/photos/".$photo."_thumb.JPG\" alt=\"".$titreAlbum."\" />";
											echo "</a>";
											echo "<div class=\"caption right-part\">";
											echo "<div class=\"album-shortcuts\">";
											echo "	<a class=\"next-album ".$idAlbum."-next\" href=\"#\" onclick=\"var destination = albumShortcuts['".$idAlbum."']['prev'];this.href='#' + destination;$.galleriffic.gotoImage('#' + destination);\">&lsaquo; Album suivant</a>";
											echo "	<a class=\"prev-album ".$idAlbum."-prev\" href=\"#\" onclick=\"var destination = albumShortcuts['".$idAlbum."']['next'];this.href='#' + destination;$.galleriffic.gotoImage('#' + destination);\">Album précédent &rsaquo;</a>";
											echo "</div>";
											
											if ($admin)
											{
												echo "<input type=\"button\" class=\"button\" value=\"Supprimer cette photo\" onclick=\"deletePhoto('".$idPhoto."');\" />";
												echo "	<br />";
												echo "	<br />";
											}
											
											echo "	<div class=\"image-title\">".$titreAlbum."</div>";

											if ($admin)
											{
												echo "<br /><textarea id=\"titre-text-".$idAlbum."\" class=\"comment-text\" rows=\"1\">".str_replace("<br />", "\n", $titreAlbum)."</textarea><br />";
												echo "<input type=\"button\" class=\"button\" value=\"Modifier le titre de l'album\" onclick=\"setTitreAlbum('".$idAlbum."');\" />";
											}
											
											/**/
											if (trim($descriptionPhoto) != "")
												echo "	<div class=\"image-desc\">".$descriptionPhoto."</div>";
											/**/
											if ($admin)
											{
												echo "<br /><textarea id=\"description-text-".$idPhoto."\" class=\"comment-text\" rows=\"2\">".str_replace("<br />", "\n", $descriptionPhoto)."</textarea><br />";
												echo "<input type=\"button\" class=\"button\" value=\"Modifier la description\" onclick=\"setDescriptionPhoto('".$idPhoto."');\" />";
												
												if ($idAlbum == 'new' && trim($descriptionPhoto) == "")
													$newAlbumHaveAllDescriptions = false;
											}
											/*
											else
											{
												if (trim($descriptionPhoto) != "")
													echo "	<div class=\"image-desc\">".$descriptionPhoto."</div>";
											}
											*/
												
											echo "	<div class=\"download\">";
											echo "		<a href=\"data/photos/".$photo."_original.JPG\">T&eacute;l&eacute;charger l'original</a>";
											echo "	</div>";
											echo "	<br />";
											
											if ($idAlbum != 'new')
											{
												$commentsPhoto = getCommentsPhoto($photo);
												
												echo "	<div class=\"comment-title\">Commentaires (".count($commentsPhoto).")</div>";									
												echo "	<div class=\"comment-list\">";
												
												foreach ($commentsPhoto as $comment)
												{
													echo "		<div class=\"comment\">";
													echo "			<span class=\"comment-date gray\">Posté par </span><span class=\"comment-login orange\">".$comment['login']."</span><span class=\"comment-date gray\"> le ".$comment['date']." :</span>";
													echo "			<br class=\"comment-clear\" />";
													echo "			<div class=\"comment-content\">";
													echo $comment['commentaire'];
													echo "			</div>";
													echo "		</div>";
													echo "		<br />";
												}
												
												echo "	</div>";
												echo "</div>";
											}
											echo "</li>";
											
											$i++;
										}	
										
										$scriptAlbumShortcuts .= "$('a.".$idLastAlbum."-next').hide();\n";
										$scriptAlbumShortcuts .= "$('a.".$idFirstAlbum."-prev').hide();\n";

									}							
								?>
							</ul>
							<a class="pageLink next" style="visibility: hidden;" href="#" title="Page suivante"></a
						</div>
					</div>
					<script>
						<?php echo $scriptAlbumShortcuts; ?>
					</script>
					<div class="content">
						<br />
						<div class="slideshow-container">
							<div id="controls" class="controls"></div>
							<div id="loading" class="loader"></div>
							<div id="slideshow" class="slideshow"></div>
						</div>
						<div id="caption" class="caption-container">
							<div class="add-comment">
								<div class="comment-title">Ajouter un commentaire</div><br />
								<input id="comment-login" type="text" value="Saisissez votre nom" onfocus="if (this.value == 'Saisissez votre nom') this.value = '';" onblur="validateComment();" /><br />
								<textarea id="comment-text" class="comment-text" rows="3" onfocus="if (this.value == 'Saisissez votre commentaire...') this.value = '';" onblur="validateComment();">Saisissez votre commentaire...</textarea><br />
								<input type="button" class="button" value="Ajouter le commentaire" onclick="if (validateComment()) saveComment();" />
								<input id="comment-photo" type="hidden" />
							</div>
							<script>
							
								function displayCreateSite()
								{
									$('#site').fadeTo('fast', 0.0);
									$('#site').hide();
									$('#footer').hide();
									$('#create-site').show();
								}
							
								function displayNoAlbum()
								{
									$('#site').fadeTo('fast', 0.0);
									$('#site').hide();
									$('#footer').hide();
									$('#no-album').fadeTo('slow', 1.0);
								}
							
								function displayAddAlbum()
								{
									$('#site').fadeTo('fast', 0.0);
									$('#site').hide();
									$('#footer').hide();
									$('#add-album').fadeTo('slow', 1.0);
								}
							
								function displaySite()
								{
									$('#add-album').fadeTo('slow', 0.0);
									$('#add-album').hide();
									$('#no-album').hide();
									$('#create-site').hide();
									$('#footer').show();
									$('#site').fadeTo('fast', 1.0);
								}
							
								function initialiseComment(idPhoto)
								{
									if (idPhoto.indexOf('new') == 0)
									{
										$('.add-comment').hide();
									}
									else
									{
										$('.add-comment').show();
										$('#comment-login').removeClass('error');
										$('#comment-text').removeClass('error');
										$('#comment-login').val('Saisissez votre nom');
										$('#comment-text').val('Saisissez votre commentaire...');
									}
								}
							
								function validateComment()
								{
									var result = true;
									$('#comment-login').removeClass('error');
									$('#comment-text').removeClass('error');
									
									if ($('#comment-login').val() == '' || $('#comment-login').val() == 'Saisissez votre nom')
									{
										$('#comment-login').val('Saisissez votre nom');
										$('#comment-login').addClass('error');
										result = false;
									}
									if ($('#comment-text').val() == '' || $('#comment-text').val() == 'Saisissez votre commentaire...')
									{
										$('#comment-text').val('Saisissez votre commentaire...');
										$('#comment-text').addClass('error');
										result = false;
									}
									
									return result;
								}
								
								function saveComment()
								{
									getDatas('addComment', 'resultAddComment', 'photo=' + $('#comment-photo').val() + '&login=' + encode($('#comment-login').val()) + '&commentaire=' + encode($('#comment-text').val()));
									//alert(resultAddComment);
									window.location.reload();
								}							

								function setTitreAlbum(album)
								{
									getDatas('setTitreAlbum', 'resultSetTitreAlbum', 'album=' + album + '&titre=' + encode($('#titre-text-' + album).val()));
									//alert(resultSetTitreAlbum);
									window.location.reload();
								}							
								
								var newAlbumHaveAllDescriptions = <?php echo ($newAlbumHaveAllDescriptions ? 'true' : 'false') ?>;
								function publishAlbum()
								{
									if ($('#titre-text-new').val() == 'Nouvel album sans titre')
									{
										alert('Veuillez saisir un titre pour le nouvel album !');
									}
									else
									{
										if (newAlbumHaveAllDescriptions || confirm("Certaines photos n'ont pas de description !\nVoulez-vous tout de même publier cet album ?"))
										{
											$('#start_publish').hide();
											$('#while_publish').show();
											getDatas('publishAlbum', 'resultPublishAlbum', 'album=new');
											//alert(resultPublishAlbum);
											window.location.href = urlBase;
										}
									}
								}							
								
								function setDescriptionPhoto(photo)
								{
									getDatas('setDescriptionPhoto', 'resultSetDescriptionPhoto', 'photo=' + photo + '&description=' + encode($('#description-text-' + photo).val()));
									//alert(resultSetDescriptionPhoto);
									window.location.reload();
								}			

								function setParametresSite()
								{
									$('#mail-admin-text').removeClass('error');
									$('#pass-admin-text').removeClass('error');
									$('#pass-admin-text-2').removeClass('error');
									
									if ($('#mail-admin-text').val() == '' || !isValidEmailAddress($('#mail-admin-text').val()))
									{
										$('#mail-admin-text').addClass('error');
										return false;
									}
									else
									{
										if ($('#pass-admin-text').val() != $('#pass-admin-text-2').val())
										{
											$('#pass-admin-text').addClass('error');
											$('#pass-admin-text-2').addClass('error');
											return false;
										}
										else
										{
											getDatas('setParametresSite', 'resultSetParametresSite', 'mail=' + encode($('#mail-admin-text').val()) + '&pass=' + encode($('#pass-admin-text').val()) + '&url=' + urlBase);
											window.location.href = urlBase + '?admin=' + resultSetParametresSite;
										}
									}
								}			

								function setTitreSite()
								{
									getDatas('setTitreSite', 'resultSetTitreSite', 'titre=' + encode($('#site-titre-text').val()));
									//alert(resultSetTitreSite);
									window.location.reload();
								}			

								function setFooterSite()
								{
									getDatas('setFooterSite', 'resultSetFooterSite', 'footer=' + encode($('#site-footer-text').val()));
									//alert(resultSetFooterSite);
									window.location.reload();
								}			

								function deletePhoto(photo)
								{
									if (confirm('Etes-vous certain de vouloir supprimer cette photo ?'))
									{
										getDatas('deletePhoto', 'resultDeletePhoto', 'photo=' + photo);
										//alert(resultSetDescriptionPhoto);
										window.location.href = urlBase + '?admin=' + admin_code;
									}
								}			

								function initialiseEmail()
								{
									$('#mail-text').val('Saisissez votre email');
									$('#linkMail').hide();
									$('#spanMail').fadeTo('slow', 1.0);
									$('#spanMailOk').hide();
								}
								
								function isValidEmailAddress(emailAddress) 
								{
									var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
									return pattern.test(emailAddress);
								}

								function validateEmail()
								{
									var result = true;
									$('#mail-text').removeClass('error');
									
									if ($('#mail-text').val() == '' || $('#mail-text').val() == 'Saisissez votre email')
									{
										//$('#mail-text').val('Saisissez votre email');
										$('#mail-text').addClass('error');
										result = false;
									}
									else
									{
										if (!isValidEmailAddress($('#mail-text').val()))
										{
											$('#mail-text').addClass('error');
											result = false;
										}
									}
									
									return result;
								}
								
								function saveEmail()
								{
									//alert('Adresse à enregistrer : ' + $('#mail-text').val());
									getDatas('addMail', 'resultAddMail', 'mail=' + encode($('#mail-text').val()));
									//alert(resultAddMail);
									
									$('#spanMailAdresse').html($('#mail-text').val());
								
									$('#spanMail').hide();
									
									$('#spanMailOk').fadeTo('slow', 1.0);
									/*
									$('#spanMailOk').fadeTo('slow', 1.0).delay(2000).fadeTo('slow', 0.0).queue(function(){
										$('#linkMail').fadeTo('slow', 1.0);
										document.getElementById('spanMail').style.display = 'none';
										document.getElementById('spanMailOk').style.display = 'none';
									});
									*/
								}
							</script>
							<div class="photo-index"></div>
						</div>
					</div>
					<!-- End Gallery Html Containers -->
					<div style="clear: both;"></div>
				</div>
			</div>
		</div>
		<div id="footer">		
			<?php
				$footer = getFooterSite();
				echo $footer;
				if ($admin)
				{
					echo "<br /><textarea id=\"site-footer-text\" class=\"comment-text\" style=\"text-align:center;\" rows=\"4\">".str_replace("<br />", "\n", $footer)."</textarea><br />";
					echo "<input type=\"button\" class=\"button\" value=\"Modifier le pied de page\" onclick=\"setFooterSite();\" />";
				}
			?>
		</div>
		<script type="text/javascript">
			var gallery;
			jQuery(document).ready(function($) {
				// We only want these styles applied when javascript is enabled
				$('div.content').css('display', 'block');

				// Initially set opacity on thumbs and add
				// additional styling for hover effect on thumbs
				var onMouseOutOpacity = 0.67;
				$('#thumbs ul.thumbs li, div.navigation a.pageLink').opacityrollover({
					mouseOutOpacity:   onMouseOutOpacity,
					mouseOverOpacity:  1.0,
					fadeSpeed:         'fast',
					exemptionSelector: '.selected'
				});
				
				// Initialize Advanced Galleriffic Gallery
				gallery = $('#thumbs').galleriffic({
					delay:                     2500,
					numThumbs:                 10,
					preloadAhead:              10,
					enableTopPager:            false,
					enableBottomPager:         false,
					imageContainerSel:         '#slideshow',
					controlsContainerSel:      '#controls',
					captionContainerSel:       '#caption',
					loadingContainerSel:       '#loading',
					renderSSControls:          true,
					renderNavControls:         true,
					playLinkText:              'D&eacute;marrer un diaporama',
					pauseLinkText:             'Arr&ecirc;ter le diaporama',
					prevLinkText:              '&lsaquo; Photo pr&eacute;c&eacute;dente',
					nextLinkText:              'Photo suivante &rsaquo;',
					nextPageLinkText:          'Suiv. &rsaquo;',
					prevPageLinkText:          '&lsaquo; Pr&eacute;c.',
					enableHistory:             true,
					autoStart:                 false,
					syncTransitions:           true,
					defaultTransitionDuration: 900,
					onSlideChange:             function(prevIndex, nextIndex) {
						// 'this' refers to the gallery, which is an extension of $('#thumbs')
						this.find('ul.thumbs').children()
							.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
							.eq(nextIndex).fadeTo('fast', 1.0);

						// Update the photo index display
							//.html('Photo '+ (nextIndex+1) +' of '+ this.data.length);
						this.$captionContainer.find('div.photo-index')
							.html('Photo ' + (nextIndex+1) + ' / ' + this.data.length);
						
						this.$captionContainer.find('#comment-photo')
							.val(this.data[nextIndex].hash);
						
						initialiseComment(this.data[nextIndex].hash);
					},
					onPageTransitionOut:       function(callback) {
						this.hide();
						this.fadeTo('fast', 0.0);
						callback();
						//this.fadeTo('fast', 0.0, callback);
					},
					onPageTransitionIn:        function() {
						var prevPageLink = this.find('a.prev').css('visibility', 'hidden');
						var nextPageLink = this.find('a.next').css('visibility', 'hidden');
						
						// Show appropriate next / prev page links
						if (this.displayedPage > 0)
							prevPageLink.css('visibility', 'visible');

						var lastPage = this.getNumPages() - 1;
						if (this.displayedPage < lastPage)
							nextPageLink.css('visibility', 'visible');

						this.show();
						this.fadeTo('slow', 1.0);
					}
				});

				/**************** Event handlers for custom next / prev page links **********************/

				gallery.find('a.prev').click(function(e) {
					gallery.previousPage();
					e.preventDefault();
				});

				gallery.find('a.next').click(function(e) {
					gallery.nextPage();
					e.preventDefault();
				});
				
				/****************************************************************************************/

				/**** Functions to support integration of galleriffic with the jquery.history plugin ****/

				// PageLoad function
				// This function is called when:
				// 1. after calling $.historyInit();
				// 2. after calling $.historyLoad();
				// 3. after pushing "Go Back" button of a browser
				function pageload(hash) {
					// alert("pageload: " + hash);
					// hash doesn't contain the first # character.
					if(hash) {
						$.galleriffic.gotoImage(hash);
					} else {
						gallery.gotoIndex(0);
					}
				}

				// Initialize history plugin.
				// The callback is called at once by present location.hash. 
				$.historyInit(pageload, "advanced.html");

				// set onlick event for buttons using the jQuery 1.3 live method
				$("a[rel='history']").live('click', function(e) {
					if (e.button != 0) return true;

					var hash = this.href;
					hash = hash.replace(/^.*#/, '');

					// moves to a new page. 
					// pageload is called at once. 
					// hash don't contain "#", "?"
					$.historyLoad(hash);

					return false;
				});

				/****************************************************************************************/
								
				$("#debug").delay( 2000 ).queue(function(){
					writeDebug('Appel après delai :');
					changeCaptionHeight(gallery);
				}); 
			}); 
			
			onload = function() {
				writeDebug('Appel dans onload :');
				changeCaptionHeight(gallery);
			}
			
			<?php 
				if (!$no_params)
				{
					if (!$havePhotos)
					{
						if ($admin)
						{
							echo "displayAddAlbum();";
							echo "displaySite = function()";
							echo "{";
							echo "	return false;";
							echo "}";
						}
						else
						{
							echo "displayNoAlbum();";
						}
					}
				}
				else
				{
					if ($admin)
					{
						echo "displayCreateSite();";
					}
					else
					{
						echo "displayNoAlbum();";
					}
				}
			?>
		</script>
		<div id="debug">
		</div>
	</body>
</html>