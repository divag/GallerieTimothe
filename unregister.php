<?php

include('functions/functions.php');
if (isset($_GET) && isset($_GET['id']))
{
	deleteMail(base64_decode($_GET['id']));
}
else
{
	echo "<script>window.location.href = 'index.php';</script>";
}

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
	</head>
	<body>
		<div id="page">
			<div id="container">
				<div id="site">
					<div class="header">
						<h1><a href="">Les photos du petit Timoth&eacute;</a></h1>
						<br />
						<span class="bold">
							Votre adresse mail a bien été supprimée de la mailing-list de ce site.<br />
						</span>
						<br />
						<br />
						<span>
							Pour consulter le site : <a href="index.php">cliquez ici</a><br />
						</span>
						<br />
						<br />
						<br />
					</div>
					<br class="clear" />
				</div>
			</div>
		</div>
	</body>
</html>