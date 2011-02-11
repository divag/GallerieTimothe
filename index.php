<?php

include('functions/functions.php');
$admin = (isset($_GET['admin']));

?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Les photos du petit Timoth&eacute;</title>
		<link rel="stylesheet" href="css/basic.css" type="text/css" />
		<link rel="stylesheet" href="css/galleriffic-5.css" type="text/css" />
		
		<!-- <link rel="stylesheet" href="css/white.css" type="text/css" /> -->
		<link rel="stylesheet" href="css/black.css" type="text/css" />
		<link rel="stylesheet" href="css/my.css" type="text/css" />
		
		<script type="text/javascript" src="js/jquery-1.5.min.js"></script>
		<script type="text/javascript" src="js/jquery.history.js"></script>
		<script type="text/javascript" src="js/jquery.galleriffic.js"></script>
		<script type="text/javascript" src="js/jquery.opacityrollover.js"></script>
		<script type="text/javascript" src="js/my.js"></script>
		<!-- We only want the thunbnails to display when javascript is disabled -->
		<script type="text/javascript">
			document.write('<style>.noscript { display: none; }</style>');
			var urlBaseFunction = 'http://divag.parishq.net/Timothe/functions/';
			var debug = <?php echo (isset($_GET['debug']) ? 'true' : 'false') ?>;
			var admin = <?php echo ($admin ? 'true' : 'false') ?>;
		</script>
	</head>
	<body>
		<?php
		
			$listePhotos = getListePhotos();
			$idLastAlbum = substr($listePhotos[0], 0, 3);
			$lastAlbum = getAlbumInfos($idLastAlbum);
			$dateLastAlbum = $lastAlbum['date'];
			$titreLastAlbum = $lastAlbum['titre'];
						
		?>
		<div id="page">
			<div id="container">
				<div class="header">
					<h1><a href="">Les photos du petit Timoth&eacute;</a></h1>
					<div id="lastUpdate">
						Mis à jour le <?php echo $dateLastAlbum ?> : <span>Ajout de l'album <b><?php echo $titreLastAlbum ?></b></span>
					</div>
					<br />
				</div>
				<div class="links">
					<a href="##" class="mailLink"><img src="css/mail.gif" alt="Recevoir un mail quand il y a du nouveau" /><span>Recevoir un mail quand il y a du nouveau</span></a>
					<br />
					<a href="##" class="zipLink"><img src="css/zip.gif" alt="T&eacute;l&eacute;charger toutes les photos" /><span>T&eacute;l&eacute;charger toutes les photos</span></a>
				</div>
				<br class="clear" />
				<!-- <h2>Nom de la gallerie...</h2> -->
				<!-- Start Advanced Gallery Html Containers -->				
				<div class="navigation-container">
					<div id="thumbs" class="navigation">
						<a class="pageLink prev" href="#" title="Page précédente"><span>&lt;&lt;</span></a>
					
						<ul class="thumbs noscript">
							<?php
							
								$idAlbum = $idLastAlbum;
								$album = $lastAlbum;
								$dateAlbum = $lastAlbum['date'];
								$titreAlbum = $lastAlbum['titre'];
								
								$idTempAlbum = $idLastAlbum;
								$i = 0;
								
								foreach ($listePhotos as $photo)
								{
									$idAlbum = substr($photo, 0, 3);
									$idPhoto = substr($photo, 4);
									if ($idAlbum != $idTempAlbum)
									{
										$album = getAlbumInfos($idAlbum);
										$dateAlbum = $album['date'];
										$titreAlbum = $album['titre'];
										
										$idTempAlbum = $idAlbum;
									}
								
									$descriptionPhoto = getDescriptionPhoto($photo);
								
									echo "<li>";
									echo "<a class=\"thumb".($admin && trim($descriptionPhoto) == "" ? " no-description" : "").($admin && trim($descriptionPhoto) != "" ? " have-description" : "")."\" name=\"leaf\" href=\"photos/".$photo.".JPG\" id=\"".$idPhoto."\" title=\"".$titreAlbum."\">";
									echo "	<img src=\"photos/".$photo."_thumb.JPG\" alt=\"".$titreAlbum."\" />";
									echo "</a>";
									echo "<div class=\"caption right-part\">";
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
									}
									/*
									else
									{
										if (trim($descriptionPhoto) != "")
											echo "	<div class=\"image-desc\">".$descriptionPhoto."</div>";
									}
									*/
										
									echo "	<div class=\"download\">";
									echo "		<a href=\"photos/".$photo."_original.JPG\">T&eacute;l&eacute;charger l'original</a>";
									echo "	</div>";
									echo "	<br />";
									
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
									echo "</li>";
									
									$i++;
							}
							
							?>
						</ul>
						<a class="pageLink next" href="#" title="Page suivante"><span>&gt;&gt;</span></a>
					</div>
				</div>
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
						
							function initialiseComment()
							{
								$('#comment-login').removeClass('error');
								$('#comment-text').removeClass('error');
								$('#comment-login').val('Saisissez votre nom');
								$('#comment-text').val('Saisissez votre commentaire...');
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
							
							function setDescriptionPhoto(photo)
							{
								getDatas('setDescriptionPhoto', 'resultSetDescriptionPhoto', 'photo=' + photo + '&description=' + encode($('#description-text-' + photo).val()));
								//alert(resultSetDescriptionPhoto);
								window.location.reload();
							}							
						</script>
						<div class="photo-index"></div>
					</div>
				</div>
				<!-- End Gallery Html Containers -->
				<div style="clear: both;"></div>
			</div>
		</div>
		<div id="footer">Timothé est né le 27 janvier 2011 à 2h17 à Nantes. <br />Il mesurait alors 51,5 cm et pesait 3 kg 450. <br />Le plus beau bébé du monde...</div>
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
							.val(this.data[nextIndex].id);
							
						initialiseComment();
					},
					onPageTransitionOut:       function(callback) {
						this.fadeTo('fast', 0.0, callback);
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

						this.fadeTo('fast', 1.0);
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
			
		</script>
		<div id="debug">
		</div>
	</body>
</html>